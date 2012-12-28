<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  shisetsuclassAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';

class shisetsuclassAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$BTN_MAX = 8;
		$PageNO = isset($_GET['page_no']) ? intval($_GET['page_no']):0;
		$Offset = $PageNO * $BTN_MAX;

		$sql = "SELECT shisetsuclasscode, shisetsuclassname, shisetsuclassskbcode";
		$sql.= " FROM m_shisetsuclass";
		$sql.= " WHERE localgovcode=? AND delflg='0'";
		$sql.= " ORDER BY shisetsuclassskbcode, shisetsuclasscode";

		$aWhere = array(_CITY_CODE_);
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

		$message = OR_CHOOSE_A_CATEGORY;
		$mode = 0;
		$condition = OR_CATEGORY_CHOICE;
		$BackLink = '?op=top';

		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
			$BackLink = '?op=user_menu';
		}

		$_SESSION['SentakuMode'] = 0;
		$_SESSION['screenflg'] = 1;

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('next_fg', $BtnNextFG);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('shisetsuclass.tpl');
	}
}
?>
