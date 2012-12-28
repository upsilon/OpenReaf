<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設情報登録・変更
 *
 *  fcl_02_01_01_regAction.class.php
 *  fcl_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_01_01_regAction extends adminAction
{
	private $err = array();

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
		$para = array();

		$this->set_header_info();

		$scd = get_request_var('scd');
		$mode = $scd == '' ? 'reg' : 'mod';

		$oFA = new facility($this->con);

		if (isset($_POST['commitBtn'])) {

			$message = $this->check_input_data($_POST);
			if ($message == '') {

				$dataset = $this->oDB->make_base_dataset($_POST, 'm_shisetsu');

				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];

				$rc = 0;
				if ($mode == 'mod') {
					$where = "localgovcode='"._CITY_CODE_."' AND ShisetsuCode='".$scd."'";
					$rc = $this->oDB->update('m_shisetsu', $dataset, $where);
				} else {
					$dataset['localgovcode'] = _CITY_CODE_;
					$dataset['shisetsucode'] = $this->get_new_ShisetsuCode();
					$rc = $this->oDB->insert('m_shisetsu', $dataset);
				}
				if ($rc < 0) {
					$message = '登録できませんでした。';
				} else {
					$message = '正常に登録しました。';
					$success = 1;
				}
			}
			$para = $_POST;
		} elseif ($mode == 'mod') {
			$para = $oFA->get_shisetsu_data($scd);
		}
		$aBusho = $this->oPrivilege->get_busho_options();
		$aShisetsuClass = $oFA->get_shisetsuclass_options();

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
		$this->oSmarty->assign('input_control', '');
		$this->oSmarty->assign('button_control', '');
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $mode);
		$this->oSmarty->assign('op', 'fcl_02_01_01_reg');
		$this->oSmarty->display('fcl_02_01.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	//-------------------------------------------------------------------
	// 入力データチェック
	//-------------------------------------------------------------------
	function check_input_data(&$req)
	{
		$msg = '';

		// 適用開始日
		if (strlen($req['appdatefrom']) == 0) {
			$msg.= '適用開始日を入力してください。<br>';
			$this->err['AppDateFrom'] = 1;
			
		} elseif (!checkdate(substr($req['appdatefrom'],4,2),substr($req['appdatefrom'],6,2),substr($req['appdatefrom'],0,4))) {
			$msg.= '適用開始日を年月日を確認してください。<br>';
			$this->err['AppDateFrom'] = 1;
		} 
		// 施設名称
		if (strlen($req['shisetsuname']) == 0) {
			$msg.= '施設名称を入力してください。<br>';
			$this->err['ShisetsuName'] = 1;
		}
		// 表示順
		if (!preg_match('/^[0-9]+$/', $req['shisetsuskbcode'])) {
			$msg.= '表示順は半角数字で入力してください。<br>';
			$this->err['ShisetsuSkbCode'] = 1;
		}
		if (strlen($req['tel1']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['tel1'])) {
				$msg.= '電話番号は半角数字で入力してください。<br>';
				$this->err['Tel1'] = 1;
			}
		}
		if (strlen($req['tel2']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['tel2'])) {
				$msg.= '電話番号は半角数字で入力してください。<br>';
				$this->err['Tel2'] = 1;
			}
		}
		if (strlen($req['tel3']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['tel3'])) {
				$msg.= '電話番号は半角数字で入力してください。<br>';
				$this->err['Tel3'] = 1;
			}
		}
		if (strlen($req['telno21']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['telno21'])) {
				$msg.= '問い合せ先電話番号は半角数字で入力してください。<br>';
				$this->err['TelNo21'] = 1;
			}
		}
		if (strlen($req['telno22']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['telno22'])) {
				$msg.= '問い合せ先電話番号は半角数字で入力してください。<br>';
				$this->err['TelNo22'] = 1;
			}
		}
		if (strlen($req['telno23']) != 0) {
			if (!preg_match('/^[0-9]+$/', $req['telno23'])) {
				$msg.= '問い合せ先電話番号は半角数字で入力してください。<br>';
				$this->err['TelNo23'] = 1;
			}
		}
		if (!preg_match('/^[0-9]+$/', $req['grouppulloutmonlimit'])) {
			$msg.= '月間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['GroupPullOutMonLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['grouppulloutmon1limit'])) {
			$msg.= '月間申込制限(平日・団体抽選)は半角数字で入力してください。<br>';
			$this->err['GroupPullOutMon1Limit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['grouppulloutmon2limit'])) {
			$msg.= '月間申込制限(土日祝日・団体抽選)は半角数字で入力してください。<br>';
			$this->err['GroupPullOutMon2Limit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['grouppulloutweklimit'])) {
			$msg.= '週間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['GroupPullOutWekLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['grouppulloutdaylimit'])) {
			$msg.= '日間申込制限(団体抽選)は半角数字で入力してください。<br>';
			$this->err['GroupPullOutDayLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalpulloutmonlimit'])) {
			$msg.= '月間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PersonalPullOutMonLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalpulloutmon1limit'])) {
			$msg.= '月間申込制限(平日・個人抽選)は半角数字で入力してください。<br>';
			$this->err['PersonalPullOutMon1Limit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalpulloutmon2limit'])) {
			$msg.= '月間申込制限(土日祝日・個人抽選)は半角数字で入力してください。<br>';
			$this->err['PersonalPullOutMon2Limit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalpulloutweklimit'])) {
			$msg.= '週間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PersonalPullOutWekLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalpulloutdaylimit'])) {
			$msg.= '日間申込制限(個人抽選)は半角数字で入力してください。<br>';
			$this->err['PersonalPullOutDayLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['groupippanmonlimit'])) {
			$msg.= '月間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['GroupIppanMonLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['groupippanweklimit'])) {
			$msg.= '週間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['GroupIppanWekLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['groupippandaylimit'])) {
			$msg.= '日間申込制限(団体予約)は半角数字で入力してください。<br>';
			$this->err['GroupIppanDayLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalippanmonlimit'])) {
			$msg.= '月間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['PersonalIppanMonLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalippanweklimit'])) {
			$msg.= '週間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['PersonalIppanWekLimit'] = 1;
		}
		if (!preg_match('/^[0-9]+$/', $req['personalippandaylimit'])) {
			$msg.= '日間申込制限(個人予約)は半角数字で入力してください。<br>';
			$this->err['PersonalIppanDayLimit'] = 1;
		}
		return $msg;
	}

	function get_new_ShisetsuCode()
	{
		$sql = 'SELECT MAX(shisetsucode) FROM m_shisetsu WHERE localgovcode=?';
		$code = intval($this->con->getOne($sql, array(_CITY_CODE_))) + 1;
		return sprintf('%03d', $code);
	}
}
?>
