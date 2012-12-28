<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金表登録
 *
 *  fcl_06_01_01_regAction.class.php
 *  fcl_06_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_fee.class.php';

class fcl_06_01_01_regAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $setflg_arr, $feetourokukbn_arr;

		$message = '';
		$success = 0;
		$para = array();

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

		if (isset($_POST['insertBtn'])) {
			$message = $oFF->check_input_data($_POST);
			if ($message == '') {
				$num = $oFF->get_max_number();
				if ($oFF->insert_stj_fee($_POST, $num)) {
					$message = '正常に登録しました。';
					$success = 1;
					$_REQUEST['tcd'] = $num;
					$_REQUEST['apd'] = $_POST['appdatefrom'];
					$_REQUEST['prfr'] = $_POST['monthdayfrom'];
					$_REQUEST['prto'] = $_POST['monthdayto'];
				} else {
					$message = '登録できませんでした。';
				}
			}
			$para = $_POST;
		} else {
			if (isset($_REQUEST['apd'])) {
				$para['appdatefrom'] = $_REQUEST['apd'];
				$para['monthdayfrom'] = $_REQUEST['prfr'];
				$para['monthdayto'] = $_REQUEST['prto'];
			}
			for ($i = 0; $i < 10; ++$i)
			{
				$para['feekbn'][$i] = '';
				$para['minfee'][$i] = '';
				$para['flatfee'][$i] = '';
				for ($j = 0; $j < _MAX_KOMA_; ++$j)
				{
					$para['fee'][$j][$i] = '';
				}
			}
			for ($i = 0; $i < _MAX_KOMA_; ++$i)
			{
				$para['timefrom'][$i] = '';
				$para['timeto'][$i] = '';
			}
		}
		$oFF->set_tab_index($para);
		$aFeeKbn = $oFF->get_feekbn_options();

		$back_url = isset($_GET['new']) ? 'fcl_05_05_summary' : 'fcl_06_02_summary';

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oFF->get_error());
		$this->oSmarty->assign('setflg_arr', $setflg_arr);
		$this->oSmarty->assign('feetourokukbn_arr', $feetourokukbn_arr);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('back_url', $back_url);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'reg');
		$this->oSmarty->assign('op', 'fcl_06_01_01_reg');
		$this->oSmarty->display('fcl_06_01.tpl');
	}
}
?>
