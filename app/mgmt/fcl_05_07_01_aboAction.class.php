<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面廃止／廃止取消
 *
 *  fcl_05_07_01_aboAction.class.php
 *  fcl_05_07.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_07_01_aboAction extends adminAction
{
	private $err = array();

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
		$mcd = $_REQUEST['mcd'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['expireBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				if ($this->expire_men($_POST, $scd, $rcd, $mcd)) {
					$message = '廃止しました。';
				} else {
					$message = '廃止できませんでした。';
					$success = -1;
				}
			}
		} elseif (isset($_POST['resumeBtn'])) {
			$_POST['MenHaishiDate'] = NULL;
			if ($this->expire_men($_POST, $scd, $rcd, $mcd)) {
				$message = '廃止取消しました。';
			} else {
				$message = '廃止取消できませんでした。';
				$success = -1;
			}
		}
		$para = $oFA->get_men_data($scd, $rcd, $mcd);
		if ($success < 0) {
			$para['menhaishidate'] = $_POST['MenHaishiDate'];
		} elseif ($para['menhaishidate'] != '') {
			$abosuccess = 1;
		}

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('back_url', 'fcl_04_08_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'fcl_05_07_01_abo');
		$this->oSmarty->display('fcl_05_07.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if (trim($req['MenHaishiDate']) == '') {
			$msg.= '廃止日を入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		} elseif (!preg_match('/^[0-9]{8}$/', $req['MenHaishiDate'])) {
			$msg.= '廃止日は8桁の半角数字で入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		}
		if (empty($this->err['HaishiDate'])) {
			if (checkdate(substr($req['MenHaishiDate'],4,2), substr($req['MenHaishiDate'],6,2), substr($req['MenHaishiDate'],0,4)) == false) {
				$msg.= '廃止日が正しくありません。<br>';
				$this->err['HaishiDate'] = 1;
			} elseif ($req['MenHaishiDate'] < $req['appdatefrom']) {
				$msg = '廃止日が適用開始日以前の日付です。<br>';
				$this->err['HaishiDate'] = 1;
			}
		}
		return $msg;
	}

	function expire_men(&$req, $scd, $rcd, $mcd)
	{
		$dataset = array();
		$dataset['menhaishidate'] = $req['MenHaishiDate'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd
			."' AND mencode='".$mcd."'";

		$rc = $this->oDB->update('m_men', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
