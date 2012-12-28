<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割廃止／廃止取消
 *
 *  fcl_05_01_03_aboAction.class.php
 *  fcl_05_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_timetable.class.php';

class fcl_05_01_03_aboAction extends adminAction
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
		$abosuccess = 0;

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$oTT = new facility_timetable($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['expireBtn'])) {
			$message = $oTT->check_haishi_date($_POST);
			if ($message == '') {
				if ($oTT->expire_stj_timetable($_POST)) {
					$message = '廃止しました。';
				} else {
					$message = '廃止できませんでした。';
					$success = -1;
				}
			} else {
				$success = -1;
			}
		} elseif (isset($_POST['resumeBtn'])) {
			$_POST['HaishiDate'] = NULL;
			if ($oTT->expire_stj_timetable($_POST)) {
				$message = '廃止取消しました。';
			} else {
				$message = '廃止取消できませんでした。';
				$success = -1;
			}
		}
		$para = $oTT->get_stj_timetable_data();
		if ($success < 0) {
			$para['haishidate'] = $_POST['HaishiDate'];
		} elseif ($para['haishidate'] != '') {
			$abosuccess = 1;
		}

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oTT->get_error());
		$this->oSmarty->assign('komatanitimekbn_arr', $komatanitimekbn_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_01_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'fcl_05_01_03_abo');
		$this->oSmarty->display('fcl_05_01.tpl');
	}
}
?>
