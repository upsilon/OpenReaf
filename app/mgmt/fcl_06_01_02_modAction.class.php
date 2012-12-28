<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金表変更
 *
 *  fcl_06_01_02_modAction.class.php
 *  fcl_06_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_fee.class.php';

class fcl_06_01_02_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $setflg_arr, $feetourokukbn_arr;

		$message = '';
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$cno = $_REQUEST['cno'];
		$tcd = $_REQUEST['tcd'];
		$type = $_REQUEST['type'];
		$oFF = new facility_fee($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$rec['MenName'] = '';
		if ($cno != 0) {
			$rec['MenName'] = $oFA->get_mencombination_name($scd, $rcd, $cno);
		}

		if (isset($_POST['updateBtn'])) {
			$message = $oFF->check_input_data($_POST);
			if ($message == '') {
				$this->con->autoCommit(false);
				$oFF->delete_stj_fee($tcd);
				if ($oFF->insert_stj_fee($_POST)) {
					$this->con->commit();
					$message = '正常に登録しました。';
				} else {
					$this->con->rollback();
					$message = '登録できませんでした。';
				}
			}
			$para = $_POST;
		} else {
			$para = $oFF->get_stj_fee_data();
		}
		$oFF->set_tab_index($para);
		$aFeeKbn = $oFF->get_feekbn_options();

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oFF->get_error());
		$this->oSmarty->assign('setflg_arr', $setflg_arr);
		$this->oSmarty->assign('feetourokukbn_arr', $feetourokukbn_arr);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('back_url', 'fcl_06_02_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_06_01_02_mod');
		$this->oSmarty->display('fcl_06_01.tpl');
	}
}
?>
