<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場情報登録
 *
 *  fcl_03_01_01_regAction.class.php
 *  fcl_03_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/shitsujyo.class.php';

class fcl_03_01_01_regAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $shitsujyokbn_arr, $feepaylimtkbn_arr, $feeLimitautocanflg_arr, $aGenmenType;

		$message = '';
		$success = 0;
		$para = array();

		$oFA = new shitsujyo($this->con);

		$this->set_header_info();

		$scd = $_REQUEST['scd'];

		if (isset($_POST['insertBtn'])) {

			$message = $oFA->check_input_data($_POST);
			if ($message == '') {
				$rcd = $this->get_new_ShitsujyoCode($scd);
				$rc = $this->insert_shitsujyo_info($_POST, $scd, $rcd);
				if ($rc) {
					$rc = $this->insert_scheduleptn($_POST, $scd, $rcd);
				} else {
					$message.= '室場情報の登録ができませんでした。<br>';
				}
				if ($rc) {
					$rc = $this->insert_closedday($_POST, $scd, $rcd);
				} else {
					$message.= 'スケジュール情報の登録ができませんでした。<br>';
				}
				if ($rc) {
					$message = '室場情報を登録しました。';
					$success = 1;
				} else {
					$message.= '休館情報の登録ができませんでした。<br>';
				}
			}
			$para = $_POST;
		}
		$aGenmen = $oFA->get_genmen_options();
		$aExtra = $oFA->get_extracharge_options();
		$aPayLimitKbn = array();
		$lines = ceil(count($feepaylimtkbn_arr) / 2);
		if ($lines > 0)
		{
			$aPayLimitKbn = array_chunk($feepaylimtkbn_arr, $lines, true);
		}
		$genmentype_arr = $aGenmenType;
		unset($genmentype_arr[0]);
		$rec = array();
		$rec['shisetsuname'] = $oFA->get_shisetsu_name($scd);

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oFA->get_error());
		$this->oSmarty->assign('shitsujyokbn_arr', $shitsujyokbn_arr);
		$this->oSmarty->assign('aGenmen', $aGenmen);
		$this->oSmarty->assign('aExtra', $aExtra);
		$this->oSmarty->assign('genmentype_arr', $genmentype_arr);
		$this->oSmarty->assign('aPayLimitKbn', $aPayLimitKbn);
		$this->oSmarty->assign('feeLimitautocanflg_arr', $feeLimitautocanflg_arr);
		$this->oSmarty->assign('back_url', 'fcl_02_02_list');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'reg');
		$this->oSmarty->assign('op', 'fcl_03_01_01_reg');
		$this->oSmarty->display('fcl_03_01.tpl');
	}

	function insert_shitsujyo_info(&$req, $scd, $rcd)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_shitsujyou');
		$dataset['genmen'] = '';
		if (isset($req['genmen_chk'])) {
			$dataset['genmen'] = implode(',', $req['genmen_chk']);
		}
		$dataset['genapplyflg'] = '';
		if (isset($req['genapplyflg_chk'])) {
			$dataset['genapplyflg'] = implode(',', $req['genapplyflg_chk']);
		}
		$dataset['extracharge'] = '';
		if (isset($req['extracharge_chk'])) {
			$dataset['extracharge'] = implode(',', $req['extracharge_chk']);
		}
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('m_shitsujyou', $dataset);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function insert_scheduleptn(&$req, $scd, $rcd)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_yoyakuscheduleptn');
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('m_yoyakuscheduleptn', $dataset);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function insert_closedday(&$req, $scd, $rcd)
	{
		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['appdatefrom'] = $req['appdatefrom'];
		$dataset['monthdayfrom'] = '0101';
		$dataset['monthdayto'] = '1231';
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('m_closedday', $dataset);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function get_new_ShitsujyoCode($scd)
	{
		$sql = 'SELECT MAX(shitsujyocode) FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=?';
		$code = intval($this->con->getOne($sql, array(_CITY_CODE_, $scd))) + 1;
		return sprintf('%02d', $code);
	}
}
?>
