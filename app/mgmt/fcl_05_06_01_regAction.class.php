<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面情報登録・更新
 *
 *  fcl_05_06_01_regAction.class.php
 *  fcl_05_06.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_06_01_regAction extends adminAction
{
	private $err = array();

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $pulloutukemnflg_arr;

		$message = '';
		$success = 0;

		$this->set_header_info();

		$type = $_REQUEST['type'];
		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$mcd = get_request_var('mcd');
		$mode = 'ref';
		if ($type == 'mod') {
			$mode = $mcd == '' ? 'reg' : 'mod';
		}

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['commitBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$dataset = $this->oDB->make_base_dataset($_POST, 'm_men');
				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];

				$rc = 0;
				if ($mode == 'mod') {
					$where = "localgovcode='"._CITY_CODE_
						."' AND shisetsucode='".$scd
						."' AND shitsujyocode='".$rcd
						."' AND mencode='".$mcd."'";

					$rc = $this->oDB->update('m_men', $dataset, $where);
				} else {
					$dataset['localgovcode'] = _CITY_CODE_;
					$dataset['shisetsucode'] = $scd;
					$dataset['shitsujyocode'] = $rcd;
					$dataset['mencode'] = $this->get_new_MenCode($scd, $rcd);
					$rc = $this->oDB->insert('m_men', $dataset);
				}
				if ($rc< 0) {
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
		if ($success == 0 && $mcd != '') {
			$para = $oFA->get_men_data($scd, $rcd, $mcd);
		} else {
			$para = $_POST;
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('pulloutukemnflg_arr', $pulloutukemnflg_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_08_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $mode);
		$this->oSmarty->assign('op', 'fcl_05_06_01_reg');
		$this->oSmarty->display('fcl_05_06.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	//-------------------------------------------------------------------
	// 入力データチェック
	//-------------------------------------------------------------------
	function check_input_data($req)
	{
		$msg = '';

		if (trim($req['appdatefrom']) == '') {
			$msg.= '適用開始日を入力してください。<br>';
			$this->err['AppDateFrom'] = 1;
		} elseif (checkdate(substr($req['appdatefrom'],4,2),substr($req['appdatefrom'],6,2),substr($req['appdatefrom'],0,4)) == false) {
			$msg.= '適用開始日が正しくありません。<br>';
			$this->err['AppDateFrom'] = 1;
		}
		if (strlen($req['menskbcode']) == 0) {
			$msg .= '表示順を入力してください。<br>';
			$this->err['MenSkbCode'] = 1;
		} elseif (!preg_match('/^[0-9]+$/', $req['menskbcode'])) {
			$msg .= '表示順を半角数字で入力してください。<br>';
			$this->err['MenSkbCode'] = 1;
		}
		if (strlen($req['menname']) == 0) {
			$msg .= '利用単位名称を入力してください。<br>';
			$this->err['MenName'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['teiin'])) {
			$msg = '定員は半角数字で入力してください。';
			$this->err['Teiin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['teiin_min'])) {
			$msg.= '最少利用人数は半角数字で入力してください。<br>';
			$this->err['Teiin_min'] = 1;
		}
		return $msg;
	}

	function get_new_MenCode($scd, $rcd)
	{
		$sql = 'SELECT MAX(mencode) FROM m_men WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$code = intval($this->con->getOne($sql, array(_CITY_CODE_, $scd, $rcd)));
		++$code;
		return sprintf('%02d', $code);
	}
}
?>
