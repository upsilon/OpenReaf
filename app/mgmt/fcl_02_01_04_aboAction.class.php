<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設廃止／廃止解除
 *
 *  fcl_02_01_04_aboAction.class.php
 *  fcl_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_01_04_aboAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $openflg_arr, $dispflg_arr, $useflg_arr, $fractionflg_arr,
			$limitflg_arr, $shisetsukbn_arr, $pulloutmonlimitkbn_arr;

		$message = '';
		$success = 0;
		$abosuccess = 0;

		$this->set_header_info();

		$oFA = new facility($this->con);

		$scd = '';
		if (isset($_GET['scd'])) {
			$scd = $_GET['scd']; 
		} elseif (isset($_POST['scd'])) {
			$scd = $_POST['scd']; 
		}

		if (isset($_POST['HaishiCancelBtn'])
			|| isset($_POST['HaishiBtn'])) {

			$dataset = array();
			$dataset['haishidate'] = NULL;
			$dataset['upddate'] = date('Ymd');
			$dataset['updtime'] = date('His');
			$dataset['updid'] = $_SESSION['userid'];

			if (isset($_POST['HaishiBtn'])) {
				$dataset['haishidate'] = $_POST['HaishiDate'];
				$message = $this->check_input_data($_POST);
			}
			if ($message == '') {
				$where = "localgovcode='"._CITY_CODE_."' AND shisetsucode='".$scd."'";
				$rc = $this->oDB->update('m_shisetsu', $dataset, $where);
				if ($rc < 0) {
					$message = '登録できませんでした。';
					$success = -1;
				} else {
					$message = '正常に登録しました。';
					$success = 1;
				}
			} else {
				$success = -1;
			}
		}
		$para = $oFA->get_shisetsu_data($scd);
		if ($success < 0) {
			$para['haishidate'] = $_POST['HaishiDate'];
		} elseif ($para['haishidate'] != '') {
			$abosuccess = 1;
		}

		$aBusho = $this->oPrivilege->get_busho_options();
		$aShisetsuClass = $oFA->get_shisetsuclass_options();

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('scd', $scd);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('aShisetsuClass', $aShisetsuClass);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('fractionflg_arr', $fractionflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('shisetsukbn_arr', $shisetsukbn_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('button_control', 'disabled');
		$this->oSmarty->assign('err', array());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'fcl_02_01_04_abo');
		$this->oSmarty->display('fcl_02_01.tpl');
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (trim($dataset['HaishiDate']) == '') {
			$msg.= '廃止日を入力してください。<br>';
		} elseif (!checkdate(intval(substr($dataset['HaishiDate'],4,2)), intval(substr($dataset['HaishiDate'],6,2)), intval(substr($dataset['HaishiDate'],0,4)))) {
			$msg.= '廃止日を適正に指定してください。<br>';
		} elseif ($dataset['appdatefrom'] > $dataset['HaishiDate']) {
			$msg.= '廃止日が適用開始日以前の日付です<br>';
		}

		return $msg;
	}
}
?>
