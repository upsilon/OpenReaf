<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金設定廃止／廃止取消
 *
 *  fcl_06_03_01_aboAction.class.php
 *  fcl_06_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_fee.class.php';

class fcl_06_03_01_aboAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$para = array();
		$abosuccess = 0;

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$cno = $_REQUEST['cno'];
		$oFF = new facility_fee($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$rec['MenName'] = '';
		if ($cno != 0) {
			$rec['MenName'] = $oFA->get_mencombination_name($scd, $rcd, $cno);
		}

		if (isset($_POST['expireBtn'])) {
			$message = $oFF->check_haishi_date($_POST);
			if ($message == '') {
				if ($oFF->expire_stj_fee($_POST)) {
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
			if ($oFF->expire_stj_fee($_POST)) {
				$message = '廃止取消しました。';
			} else {
				$message = '廃止取消できませんでした。';
				$success = -1;
			}
		}
		$para = $oFF->get_stj_fee_data();
		if ($success < 0) {
			$para['haishidate'] = $_POST['HaishiDate'];
		} elseif ($para['haishidate'] != '') {
			$abosuccess = 1;
		}

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oFF->get_error());
		$this->oSmarty->assign('back_url', 'fcl_05_05_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'fcl_06_03_01_abo');
		$this->oSmarty->display('fcl_06_03.tpl');
	}
}
?>
