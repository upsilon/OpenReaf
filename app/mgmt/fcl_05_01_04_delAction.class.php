<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割削除
 *
 *  fcl_05_01_04_delAction.class.php
 *  fcl_05_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_timetable.class.php';

class fcl_05_01_04_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $komatanitimekbn_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$oTT = new facility_timetable($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$para = $oTT->get_stj_timetable_data();

		if (isset($_POST['deleteBtn'])) {
			if ($oTT->delete_stj_timetable()) {
				$message = '削除しました。';
				$success = 1;
			} else {
				$message = '削除できませんでした。';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('komatanitimekbn_arr', $komatanitimekbn_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_01_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_05_01_04_del');
		$this->oSmarty->display('fcl_05_01.tpl');
	}
}
?>
