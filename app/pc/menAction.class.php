<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  menAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';

class menAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$BTN_MAX = intval(_MEN_BUTTON_MAX_);
		$PageNO = isset($_GET['page_no']) ? intval($_GET['page_no']):0;
		$Offset = $PageNO * $BTN_MAX;

		$ShitsujyoCode = '';
		$ShitsujyoName = '';
		if (isset($_REQUEST['ShitsujyoCode'])) {
			$ShitsujyoCode = $_REQUEST['ShitsujyoCode'];
			$_SESSION['Y_I']['shitsujyocode'] = $ShitsujyoCode;
			$ShitsujyoName = $_REQUEST['ShitsujyoName'];
			$_SESSION['M_I']['ShitsujyoName'] = $ShitsujyoName;
		} elseif (isset($_SESSION['Y_I']['shitsujyocode'])) {
			$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
			$ShitsujyoName = $_SESSION['M_I']['ShitsujyoName'];
		} else {
			$this->display_error_msg(OR_BACK_TO_TOP);
		}

		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];

		$sql = "SELECT DISTINCT c.combino, c.combiskbno";
		$sql.= " FROM m_mencombination c";
		$sql.= " JOIN m_men m USING (localgovcode, shisetsucode, shitsujyocode, mencode)";
		$sql.= " WHERE c.localgovcode=? AND c.shisetsucode=?";
		$sql.= " AND c.shitsujyocode=?";
		$sql.= " AND (DATE(m.appdatefrom)>DATE(NOW())";
		$sql.= " OR (m.menhaishidate<=? AND m.menhaishidate<>'' AND m.menhaishidate IS NOT NULL))";
		$sql.= " ORDER BY combiskbno, combino";

		$aWhere = array(_CITY_CODE_, $ShisetsuCode, $ShitsujyoCode, date('Ymd'));
		$HaishiCombiNo = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_FLIPPED);

		$sql = "SELECT DISTINCT combino, combiname, combiskbno FROM m_mencombination";
		$sql.= " WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?";
		$sql.= " AND openflg='1' ORDER BY combiskbno, combino";
		$aWhere = array(_CITY_CODE_, $ShisetsuCode, $ShitsujyoCode);
		$rows = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$res = array();
		if (isset($HaishiCombiNo[0])) {
			foreach ($rows as $val)
			{
				if (in_array($val['combino'], $HaishiCombiNo[0])) continue;
				$res[] = $val;
			}
			unset($HaishiCombiNo);
		} else {
			$res = $rows;
		}

		$total = count($res);

		$message = OR_CHOOSE_A_PIECE;
		if ($total == 0) $message = OR_NO_PIECE;

		$recs = array();
		$i = 0;
		foreach ($res as $val)
		{
			if ($Offset <= $i && $i < ($Offset + $BTN_MAX)) $recs[] = $val;
			++$i;
		}
		unset($res);

		$max_page = ceil($total / $BTN_MAX);

		$BtnNextFG = 0;
		if (($PageNO + 1) < $max_page) {
			$BtnNextFG = 1;
		}

		$mode = 0;
		$condition = OR_PIECE_CHOICE;
		$BackLink = '?op=shitsujyo';
		if (isset($_SESSION['skip_stj'])) {
			$BackLink = '?op=shisetsu';
		}

		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $_SESSION['M_I']['ShisetsuClassName']);
		$this->oSmarty->assign('ShisetsuName', $_SESSION['M_I']['ShisetsuName']);
		$this->oSmarty->assign('ShitsujyoName', $ShitsujyoName);
		$this->oSmarty->assign('ShitsujyoCode', $ShitsujyoCode);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('next_fg', $BtnNextFG);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('men.tpl');
	}
}
?>
