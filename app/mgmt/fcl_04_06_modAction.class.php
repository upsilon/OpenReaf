<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  一般予約設定
 *
 *  fcl_04_06_modAction.class.php
 *  fcl_04_06.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_06_modAction extends adminAction
{
	private $oFA = null;
	private $err = array();

	function __construct()
	{
		parent::__construct();

		$this->oFA = new facility($this->con);
	}

	function execute()
	{
		global $ippanyoyakukbn_arr, $ippanresstartflg_arr, $ipnchgflg1_arr, $ipnchgflg2_arr, $ippanreslimitflg_arr, $ippancanlimitflg_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];

		if (isset($_POST['updateBtn'])) {

			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$rc = $this->update_shitsujyo_info($_POST, $scd, $rcd);
				if ($rc) {
					$rc = $this->update_scheduleptn($_POST, $scd, $rcd);
				} else {
					$message.= '室場情報の登録ができませんでした。<br>';
				}
				if ($rc) {
					$message = '一般予約の設定情報を登録しました。';
					$success = 1;
				} else {
					$message.= 'スケジュール情報の登録ができませんでした。<br>';
					$success = -1;
				}
			} else {
				$success = -1;
			}
		}
		if ($success < 0) {
			$para = $_POST;
		} else {
			$stmp = $this->oFA->get_shitsujyo_data($scd, $rcd);
			$ptmp = $this->oFA->get_yoyakuscheduleptn($scd, $rcd);
			$para = array_merge($stmp, $ptmp);
		}
		$rec = $this->oFA->get_shitsujyo_header($scd, $rcd);

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('ippanyoyakukbn_arr', $ippanyoyakukbn_arr);
		$this->oSmarty->assign('ippanresstartflg_arr', $ippanresstartflg_arr);
		$this->oSmarty->assign('ipnchgflg1_arr', $ipnchgflg1_arr);
		$this->oSmarty->assign('ipnchgflg2_arr', $ipnchgflg2_arr);
		$this->oSmarty->assign('ippanreslimitflg_arr', $ippanreslimitflg_arr);
		$this->oSmarty->assign('ippancanlimitflg_arr', $ippancanlimitflg_arr);
		$this->oSmarty->assign('back_url', 'fcl_03_02_menu');
		$this->oSmarty->assign('input_control', '');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_04_06_mod');
		$this->oSmarty->display('fcl_04_06.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if (!preg_match("/^[0-9]+$/", $req['ippanresstartmon'])) {
			$msg.= '受付開始日('._INSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanResStartMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanresstartday'])) {
			$msg.= '受付開始日('._INSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanResStartDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanresfrommon'])) {
			$msg.= '受付開始日('._INSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanResFromMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanresfromday'])) {
			$msg.= '受付開始日('._INSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanResFromDay'] = 1;
		} elseif ($req['ippanresfromday'] < 1 || 31 < $req['ippanresfromday']) {
			$msg.= '受付開始日('._INSIDE_.')の日は１～31の範囲で入力してください。<br>';
			$this->err['IppanResFromDay'] = 1;
		}
		if (!$this->oFA->check_time($req['ippanresfromtime'])) {
			$msg.= '受付開始日('._INSIDE_.')の時刻が正しくありません。<br>';
			$this->err['IppanResFromTime'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairesstartmon'])) {
			$msg.= '受付開始日('._OUTSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResStartMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairesstartday'])) {
			$msg.= '受付開始日('._OUTSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResStartDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairesfrommon'])) {
			$msg.= '受付開始日('._OUTSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResFromMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairesfromday'])) {
			$msg.= '受付開始日('._OUTSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResFromDay'] = 1;
		} elseif ($req['ippanshigairesfromday'] < 1 || 31 < $req['ippanshigairesfromday']) {
			$msg.= '受付開始日('._OUTSIDE_.')の日は１～31の範囲で入力してください。<br>';
			$this->err['IppanShigaiResFromDay'] = 1;
		}
		if (!$this->oFA->check_time($req['ippanshigairesfromtime'])) {
			$msg.= '受付開始日('._INSIDE_.')の時刻が正しくありません。<br>';
			$this->err['IppanShigaiResFromTime'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanreslimitmon'])) {
			$msg.= '受付締切日('._INSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanResLimitMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanreslimitday'])) {
			$msg.= '受付締切日('._INSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanResLimitDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanrestomon'])) {
			$msg.= '受付締切日('._INSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanResToMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanrestoday'])) {
			$msg.= '受付締切日('._INSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanResToDay'] = 1;
		} elseif ($req['ippanrestoday'] < 1 || 31 < $req['ippanrestoday']) {
			$msg.= '受付締切日('._INSIDE_.')の日は１～31の範囲で入力してください。<br>';
			$this->err['IppanResToDay'] = 1;
		}
		if (!$this->oFA->check_time($req['ippanrestotime'])) {
			$msg.= '受付締切日('._INSIDE_.')の時刻が正しくありません。<br>';
			$this->err['IppanResToTime'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigaireslimitmon'])) {
			$msg.= '受付締切日('._OUTSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResLimitMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigaireslimitday'])) {
			$msg.= '受付締切日('._OUTSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResLimitDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairestomon'])) {
			$msg.= '受付締切日('._OUTSIDE_.')の月は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResToMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippanshigairestoday'])) {
			$msg.= '受付締切日('._OUTSIDE_.')の日は半角数字で入力してください。<br>';
			$this->err['IppanShigaiResToDay'] = 1;
		} elseif ($req['ippanshigairestoday'] < 1 || 31 < $req['ippanshigairestoday']) {
			$msg.= '受付締切日('._OUTSIDE_.')の日は１～31の範囲で入力してください。<br>';
			$this->err['IppanShigaiResToDay'] = 1;
		}
		if (!$this->oFA->check_time($req['ippanshigairestotime'])) {
			$msg.= '受付締切日('._OUTSIDE_.')の時刻が正しくありません。<br>';
			$this->err['IppanShigaiResToTime'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippancanlimitday'])) {
			$msg.= '取消受付締切日の日は半角数字で入力してください。<br>';
			$this->err['IppanCanLimitDay'] = 1;
		}
		if (!$this->oFA->check_time($req['ippancanlimittime'])) {
			$msg.= '取消受付締切日の時刻が正しくありません。<br>';
			$this->err['IppanCanLimitTime'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippancanclosemon'])) {
			$msg.= '取消受付締切日の月は半角数字で入力してください。<br>';
			$this->err['IppanCanCloseMon'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['ippancancloseday'])) {
			$msg.= '取消受付締切日の日は半角数字で入力してください。<br>';
			$this->err['IppanCanCloseDay'] = 1;
		} elseif ($req['ippancancloseday'] < 1 || 31 < $req['ippancancloseday']) {
			$msg.= '取消受付締切日の日は１～31の範囲で入力してください。<br>';
			$this->err['IppanCanCloseDay'] = 1;
		}
		return $msg;
	}

	function update_shitsujyo_info(&$req, $scd, $rcd)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_shitsujyou');
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd."'";

		$rc = $this->oDB->update('m_shitsujyou', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function update_scheduleptn(&$req, $scd, $rcd)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_yoyakuscheduleptn');
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd."'";

		$rc = $this->oDB->update('m_yoyakuscheduleptn', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
