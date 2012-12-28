<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  shitsujyoAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';

class shitsujyoAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$BTN_MAX = intval(_SHITSUJYOU_BUTTON_MAX_);
		$PageNO = isset($_GET['page_no']) ? intval($_GET['page_no']):0;
		$Offset = $PageNO * $BTN_MAX;

		$ShisetsuCode = '';
		$ShisetsuName = '';
		if (isset($_REQUEST['ShisetsuCode'])) {
			$_SESSION['Y_I']['shisetsuclasscode'] = $_REQUEST['ShisetsuClassCode'];
			$ShisetsuCode = $_REQUEST['ShisetsuCode'];
			$_SESSION['Y_I']['shisetsucode'] = $ShisetsuCode;
			$ShisetsuName = $_REQUEST['ShisetsuName'];
			$_SESSION['M_I']['ShisetsuName'] = $ShisetsuName;
		} elseif (isset($_SESSION['Y_I']['shisetsucode'])) {
			$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
			$ShisetsuName = $_SESSION['M_I']['ShisetsuName'];
		}

		$sql = 'SELECT s.shitsujyocode, count(m.mencode) as mencount';
		$sql.= ' FROM m_shitsujyou s';
		$sql.= ' LEFT JOIN m_men m USING (localgovcode, shisetsucode, shitsujyocode)';
		$sql.= ' WHERE s.localgovcode=? AND s.shisetsucode=?';
		$sql.= ' GROUP BY shitsujyocode ORDER BY shitsujyocode';

		$aWhere = array(_CITY_CODE_, $ShisetsuCode);
		$recs = $this->con->getAll($sql, $aWhere);
		$menCount = array();
		foreach ($recs as $val) $menCount[$val[0]] = $val[1];

		$sql = "SELECT s.shitsujyocode, s.shitsujyoname, s.shitsujyoskbcode,";
		$sql.= " p.webuketimekbn, p.webuketimefrom, p.webuketimeto";
		$sql.= " FROM m_shitsujyou s";
		$sql.= " JOIN m_yoyakuscheduleptn p USING (localgovcode, shisetsucode, shitsujyocode)";
		$sql.= " WHERE s.localgovcode=? AND s.shisetsucode=?";
		$sql.= " AND s.shitsujyokbn<'3' AND s.openflg='1'";
		$sql.= " AND DATE(s.appdatefrom)<=DATE(NOW())";
		$sql.= " AND (s.haishidate>? OR s.haishidate='' OR s.haishidate IS NULL)";
		$sql.= " ORDER BY shitsujyoskbcode, shitsujyocode";

		$aWhere = array(_CITY_CODE_, $ShisetsuCode, date('Ymd'));
		$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$total = count($recs);

		if ($total == 1 && is_web_enable($recs[0]) == 1) {
			$_SESSION['skip_stj'] = $recs[0]['shitsujyocode'];
			$_SESSION['Y_I']['shitsujyocode'] = $recs[0]['shitsujyocode'];
			$_SESSION['M_I']['ShitsujyoName'] = $recs[0]['shitsujyoname'];
			$destination = 'men';
			if ($menCount[$recs[0]['shitsujyocode']] == 0) {
				$_SESSION['Y_I']['combino'] = 0;
				$_SESSION['M_I']['CombiName'] = '';
				$_SESSION['Y_I']['mencode'] = array('ZZ');
				$destination = 'monthly';
			}
			header('Location: index.php?op='.$destination);
			exit();
		}

		if ($BTN_MAX < $total) {
			$sql.= " LIMIT ? OFFSET ?";
			array_push($aWhere, $BTN_MAX, $Offset);
			$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		}

		foreach ($recs as $key => $val)
		{
			$recs[$key]['WebOpen'] = is_web_enable($val);
			$recs[$key]['Dest'] = $menCount[$val['shitsujyocode']] == 0 ? 'monthly' : 'men';
		}

		$max_page = ceil($total / $BTN_MAX);

		$BtnNextFG = 0;
		if (($PageNO + 1) < $max_page) {
			$BtnNextFG = 1;
		}

		$message = OR_CHOOSE_A_PLACE;
		$mode = 0;
		$condition = OR_PLACE_CHOICE;

		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $_SESSION['M_I']['ShisetsuClassName']);
		$this->oSmarty->assign('ShisetsuName', $ShisetsuName);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('next_fg', $BtnNextFG);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', '?op=shisetsu');
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('shitsujyo.tpl');
	}
}
?>
