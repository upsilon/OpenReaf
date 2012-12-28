<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設分類情報登録・変更
 *
 *  fcl_02_03_01_regAction.class.php
 *  fcl_02_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_03_01_regAction extends adminAction
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
		global $delflg_arr, $limitflg_arr, $pulloutmonlimitkbn_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$ccd = get_request_var('ccd');
		$mode = $ccd == '' ? 'reg' : 'mod';

		if (isset($_POST['commitBtn'])) {

			$message = $this->check_input_data($_POST);
			if ($message == '') {

				$dataset = $this->oDB->make_base_dataset($_POST, 'm_shisetsuclass');

				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];

				$rc = 0;
				if ($mode == 'mod') {
					unset($dataset['shisetsuclasscode']);
					$where = "localgovcode='"._CITY_CODE_."' AND shisetsuclasscode='".$ccd."'";
					$rc = $this->oDB->update('m_shisetsuclass', $dataset, $where);
				} else {
					$dataset['localgovcode'] = _CITY_CODE_;
					$rc = $this->oDB->insert('m_shisetsuclass', $dataset);
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
			$para = $this->oFA->get_shisetsuclass_data($ccd);
		}

		$this->oSmarty->assign('ccd', $ccd);
		$this->oSmarty->assign('req', $para);
		$this->oSmarty->assign('delflg_arr', $delflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', '');
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $mode);
		$this->oSmarty->assign('op', 'fcl_02_03_01_reg');
		$this->oSmarty->display('fcl_02_03.tpl');
	}

	function get_error()
	{
		return $this->err;
	}
	
	//-------------------------------------------------------------------
	// 入力データチェック
	//-------------------------------------------------------------------
	function check_input_data(&$req, $duplicate_check=false)
	{
		$msg = '';

		// 施設分類コード
		if (strlen($req['shisetsuclasscode']) == 0) {
			$msg.= '施設分類コードを入力してください。<br>';
			$this->err['ShisetsuClassCode'] = 1;
		} elseif (!preg_match('/^[0-9]{2}$/', $req['shisetsuclasscode'])) {
			$msg.= '施設分類コードは2桁の半角数字で入力してください。<br>';
			$this->err['ShisetsuClassCode'] = 1;
		} elseif ($duplicate_check) {
			$res = $this->oFA->get_shisetsuclass_data($req['shisetsuclasscode']);
			if (!empty($res)) {
				$msg.= '施設分類コードが重複しています。<br>';
				$this->err['ShisetsuClassCode'] = 1;
			}
		}
		// 表示順
		if (!preg_match('/^[0-9]+$/', $req['shisetsuclassskbcode'])) {
			$msg.= '表示順は半角数字で入力してください。<br>';
			$this->err['ShisetsuClassSkbCode'] = 1;
		}
		// 施設名称
		if (strlen($req['shisetsuclassname']) == 0) {
			$msg.= '施設名称を入力してください。<br>';
			$this->err['ShisetsuClassName'] = 1;
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
}
?>
