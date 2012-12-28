<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  shisetsuAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';

class shisetsuAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		if (isset($_SESSION['skip_stj'])) {
			unset($_SESSION['skip_stj']);
		}
		$_SESSION['screenflg'] = isset($_SESSION['screenflg']) ? $_SESSION['screenflg'] : 0;

		$BTN_MAX = intval(_SHISETSU_BUTTON_MAX_);
		$PageNO = isset($_GET['page_no']) ? intval($_GET['page_no']):0;
		$Offset = $PageNO * $BTN_MAX;

		$ShisetsuClassCode = '';
		$ShisetsuClassName = '';
		if (isset($_REQUEST['ShisetsuClassCode'])) {
			$ShisetsuClassCode = $_REQUEST['ShisetsuClassCode'];
			$_SESSION['Y_I']['shisetsuclasscode'] = $ShisetsuClassCode;
			$ShisetsuClassName = $_REQUEST['ShisetsuClassName'];
		} elseif (isset($_SESSION['Y_I']['shisetsuclasscode'])) {
			$ShisetsuClassCode = $_SESSION['Y_I']['shisetsuclasscode'];
			if ($_SESSION['screenflg'] == 1) {
				$ShisetsuClassName = $_SESSION['M_I']['ShisetsuClassName'];
			}
		}
		$_SESSION['M_I']['ShisetsuClassName'] = $ShisetsuClassName;

		$sql = "SELECT DISTINCT s.shisetsuclasscode, s.shisetsucode, s.shisetsuname, s.shisetsuskbcode";
		$sql.= " FROM m_shisetsu s";
		$sql.= " JOIN m_shitsujyou t USING (localgovcode, shisetsucode)";
		$sql.= " WHERE s.localgovcode=?";

		$_SESSION['ShisetsuRestrictionFlg'] = 0;
		if (isset($_SESSION['UID'])) {
			$RestrictionFlg = checkShisetsuRestriction($this->con, _CITY_CODE_);
			if ($RestrictionFlg) {
				$_SESSION['ShisetsuRestrictionFlg'] = 1;
				$sql2 = 'SELECT shisetsu FROM m_user WHERE localgovcode=? AND userid=?';
				$value = $this->con->getOne($sql2, array(_CITY_CODE_, $_SESSION['UID']));
				if ($value != '') {
					$sql.= " AND (s.shisetsucode='".str_replace(',', "' OR s.shisetsucode='", $value)."')";
				}
			}
		}
		if ($_SESSION['screenflg'] == 1) {
			$sql.= " AND s.shisetsuclasscode='".$ShisetsuClassCode."'";
		}
		$sql.= " AND s.openflg='1' AND t.openflg='1'";
		$sql.= " AND t.shitsujyokbn<'3'";
		$sql.= " AND DATE(s.appdatefrom)<=DATE(NOW())";
		$sql.= " AND (s.haishidate>? OR s.haishidate='' OR s.haishidate IS NULL)";
		$sql.= " ORDER BY shisetsuskbcode, shisetsucode";
		$aWhere = array(_CITY_CODE_, date('Ymd'));

		$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$total = count($recs);
		if ($BTN_MAX < $total) {
			$sql.= " LIMIT ? OFFSET ?";
			array_push($aWhere, $BTN_MAX, $Offset);
			$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		}

		$max_page = ceil($total / $BTN_MAX);

		$BtnNextFG = 0;
		if (($PageNO + 1) < $max_page) {
			$BtnNextFG = 1;
		}

		$message = OR_CHOOSE_A_FACILITY;
		if ($total == 0) {
			$message = OR_NO_FACILITY;
		}
		$mode = 0;
		$condition = OR_FACILITY_CHOICE;
		$BackLink = '?op=top';

		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
			$BackLink = '?op=user_menu';
		}
		if ($_SESSION['screenflg'] == 1) {
			$BackLink = '?op=shisetsuclass';
		}

		$_SESSION['SentakuMode'] = 0;

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $ShisetsuClassName);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('next_fg', $BtnNextFG);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('shisetsu.tpl');
	}
}
?>
