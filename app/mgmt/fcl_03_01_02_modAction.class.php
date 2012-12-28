<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場情報変更
 *
 *  fcl_03_01_02_modAction.class.php
 *  fcl_03_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/shitsujyo.class.php';

class fcl_03_01_02_modAction extends adminAction
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
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];

		if (isset($_POST['updateBtn'])) {

			$message = $oFA->check_input_data($_POST);
			if ($message == '') {
				$rc = $this->update_shitsujyo_info($_POST, $scd, $rcd);
				if ($rc) {
					$rc = $this->update_scheduleptn($_POST, $scd, $rcd);
				} else {
					$message.= '室場情報の登録ができませんでした。<br>';
				}
				if ($rc) {
					$message = '室場情報を更新しました。';
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
			$stmp = $oFA->get_shitsujyo_data($scd, $rcd);
			$ptmp = $oFA->get_yoyakuscheduleptn($scd, $rcd);
			$para = array_merge($stmp, $ptmp);
			$para['genmen_chk'] = explode(',', $para['genmen']);
			$para['genapplyflg_chk'] = explode(',', $para['genapplyflg']);
			$para['extracharge_chk'] = explode(',', $para['extracharge']);
		}
		$aGenmen = $oFA->get_genmen_options();
		$aExtra = $oFA->get_extracharge_options();
		$aPayLimitKbn = array();
		$lines = ceil(count($feepaylimtkbn_arr) / 2);
		if ($lines > 0) {
			$aPayLimitKbn = array_chunk($feepaylimtkbn_arr, $lines, true);
		}
		$genmentype_arr = $aGenmenType;
		unset($genmentype_arr[0]);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

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
		$this->oSmarty->assign('back_url', 'fcl_03_02_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_03_01_02_mod');
		$this->oSmarty->display('fcl_03_01.tpl');
	}

	function update_shitsujyo_info(&$req, $scd, $rcd)
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
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd."'";

		$rc = $this->oDB->update('m_shitsujyou', $dataset, $where);
		if ($rc < 0) return false;
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
		if ($rc < 0) return false;
		return true;
	}
}
?>
