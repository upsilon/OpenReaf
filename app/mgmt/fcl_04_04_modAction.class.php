<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  制限設定
 *
 *  fcl_04_04_modAction.class.php
 *  fcl_04_04.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_04_modAction extends adminAction
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
		global $openflg_arr, $openkbn_arr, $webuketimekbn_arr, $limitflg_arr, $pulloutmonlimitkbn_arr, $areapriorityflg_arr, $grouporpersonallimit_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];

		if (isset($_POST['updateBtn'])) {

			if (!isset($_POST['openkbnval'][12])) $_POST['openkbnval'][12] = 0;
			if (!isset($_POST['openkbnval'][13])) $_POST['openkbnval'][13] = 0;
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$rc = $this->update_shitsujyo_info($_POST, $scd, $rcd);
				if ($rc) {
					$rc = $this->update_scheduleptn($_POST, $scd, $rcd);
				} else {
					$message.= '室場情報の登録ができませんでした。<br>';
				}
				if ($rc) {
					$message = '制限情報を登録しました。';
					$success = 1;
				} else {
					$message.= '制限情報の登録ができませんでした。<br>';
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
			$stmp['openkbnval'] = explode(',', $stmp['openkbn']);
			$para = array_merge($stmp, $ptmp);
		}
		$rec = $this->oFA->get_shitsujyo_header($scd, $rcd);

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('openkbn_arr', $openkbn_arr);
		$this->oSmarty->assign('month_arr', range(0, 11));
		$this->oSmarty->assign('webuketimekbn_arr', $webuketimekbn_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('areapriorityflg_arr', $areapriorityflg_arr);
		$this->oSmarty->assign('grouporpersonallimit_arr', $grouporpersonallimit_arr);
		$this->oSmarty->assign('back_url', 'fcl_03_02_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_04_04_mod');
		$this->oSmarty->display('fcl_04_04.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if ($req['webuketimekbn'] != '0') {
			if (!$this->oFA->check_time($req['webuketimefrom'])) {
				$msg.= '受付開始時間が正しくありません。<br>';
				$this->err['WebUkeTimeFrom'] = 1;
			}
			if (!$this->oFA->check_time($req['webuketimeto'])) {
				$msg.= '受付終了時間が正しくありません。<br>';
				$this->err['WebUkeTimeTo'] = 1;
			}
		}

		if (!preg_match("/^[0-9]+$/", $req['pulloutmonlimitdantai'])) {
			$msg.= '月間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMonLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutmonlimitkojin'])) {
			$msg.= '月間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMonLimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutmon1limitdantai'])) {
			$msg.= '月間申込制限(平日・団体抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMon1LimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutmon1limitkojin'])) {
			$msg.= '月間申込制限(平日・個人抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMon1LimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutmon2limitdantai'])) {
			$msg.= '月間申込制限(土日祝日・団体抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMon2LimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutmon2limitkojin'])) {
			$msg.= '月間申込制限(土日祝日・個人抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutMon2LimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutweklimitdantai'])) {
			$msg.= '週間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutWekLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutweklimitkojin'])) {
			$msg.= '週間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutWekLimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutdaylimitdantai'])) {
			$msg.= '日間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutDayLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutdaylimitkojin'])) {
			$msg.= '日間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutDayLimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakumonlimitdantai'])) {
			$msg.= '月間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuMonLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakumonlimitkojin'])) {
			$msg.= '月間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuMonLimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakuweklimitdantai'])) {
			$msg.= '週間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuWekLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakuweklimitkojin'])) {
			$msg.= '週間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuWekLimitKojin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakudaylimitdantai'])) {
			$msg.= '日間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuDayLimitDantai'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakudaylimitkojin'])) {
			$msg.= '日間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuDayLimitKojin'] = 1;
		}
		return $msg;
	}

	function update_shitsujyo_info(&$req, $scd, $rcd)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_shitsujyou');
		$dataset['openkbn'] = '';
		$n = count($req['openkbnval']);
		for ($i = 0; $i < $n; ++$i)
		{
			if ($i > 0) $dataset['openkbn'].= ',';
			$dataset['openkbn'].= $req['openkbnval'][$i];
		}
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
