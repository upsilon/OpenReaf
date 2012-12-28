<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割変更
 *
 *  fcl_05_01_02_modAction.class.php
 *  fcl_05_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_timetable.class.php';

class fcl_05_01_02_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $komatanitimekbn_arr;

		$message = '';
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];
		$oTT = new facility_timetable($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['updateBtn'])) {
			$message = $oTT->check_input_data($_POST, $type);
			if ($message == '') {
				$oTT->delete_stj_timetable();
				if ($oTT->insert_stj_timetable($_POST)) {
					$message = '正常に更新しました。';
				} else {
					$message = '更新できませんでした。';
				}
			}
			$para = $_POST;
		} elseif (isset($_POST['komaname_add_btn_hd'])) {
			$para = $_POST;
			$para['komaname'][] = '';
			$para['komanametimefrom'][] = '';
			$para['komanametimeto'][] = '';
		} else {
			$para = $oTT->get_stj_timetable_data();
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oTT->get_error());
		$this->oSmarty->assign('komatanitimekbn_arr', $komatanitimekbn_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_01_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_05_01_02_mod');
		$this->oSmarty->display('fcl_05_01.tpl');
	}
}
?>
