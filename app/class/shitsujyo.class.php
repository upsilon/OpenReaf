<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場クラス
 *
 *  shitsujyo.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class shitsujyo extends facility
{
	private $err = array();

	//-------------------------------------------------------------------
	// コンストラクタ
	//-------------------------------------------------------------------
	function __construct(&$con)
	{
		parent::__construct($con);
	}

	//------------------------------------------------------------
	// 減免情報取得
	//------------------------------------------------------------
	function get_genmen_options()
	{
		$sql = "SELECT koteigencode, koteigenname";
		$sql.= " FROM m_genmen WHERE localgovcode=?";
		$sql.= " ORDER BY koteigencode";
		$aWhere = array(_CITY_CODE_);
		$rows = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($rows as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($rows);
		return $recs;
	}

	//------------------------------------------------------------
	// 割増情報取得
	//------------------------------------------------------------
	function get_extracharge_options()
	{
		$sql = "SELECT extracode, extraname";
		$sql.= " FROM m_extracharge WHERE localgovcode=?";
		$sql.= " ORDER BY extracode";
		$aWhere = array(_CITY_CODE_);
		$rows = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($rows as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($rows);
		return $recs;
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
		if (strlen($req['shitsujyoskbcode']) == 0) {
			$msg.= '表示順を入力してください。<br>';
			$this->err['ShitsujyoSkbCode'] = 1;
		} elseif (!preg_match('/^[0-9]+$/', $req['shitsujyoskbcode'])) {
			$msg.= '表示順を半角数字で入力してください。<br>';
			$this->err['ShitsujyoSkbCode'] = 1;
		}
		if (strlen($req['shitsujyoname']) == 0) {
			$msg.= '室場名称を入力してください。<br>';
			$this->err['ShitsujyoName'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['teiin'])) {
			$msg.= '定員は半角数字で入力してください。<br>';
			$this->err['Teiin'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['teiin_min'])) {
			$msg.= '最少利用人数は半角数字で入力してください。<br>';
			$this->err['Teiin_min'] = 1;
		}
		if ($req['shitsujyokbn'] != '2') {
			$sql = 'SELECT COUNT(shitsujyocode) FROM m_fuzokushitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
			$aWhere = array(_CITY_CODE_, $req['scd'], $req['rcd']);
			$res = $this->con->getOne($sql, $aWhere);
			if ($res > 0) {
				$msg = '付属室場が登録されているので、室場区分を変更できません。<br>';
			}
		}
		if (!preg_match("/^[0-9]+$/", $req['feepaylimtday'])) {
			$msg.= '使用料支払期限(一般予約)は半角数字で入力してください。<br>';
			$this->err['FeePayLimtDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutfeepaylimtday'])) {
			$msg.= '使用料支払期限(抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutFeePayLimtDay'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['yoyakudispkoma'])) {
			$msg.= '表示コマ単位(一般予約)は半角数字で入力してください。<br>';
			$this->err['YoyakuDispKoma'] = 1;
		}
		if (!preg_match("/^[0-9]+$/", $req['pulloutdispkoma'])) {
			$msg.= '表示コマ単位(抽選)は半角数字で入力してください。<br>';
			$this->err['PullOutDispKoma'] = 1;
		}
		return $msg;
	}

	function check_haishi_date(&$req)
	{
		$msg = '';

		if (trim($req['HaishiDate']) == '') {
			$msg.= '廃止日を入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		} elseif (!preg_match('/^[0-9]{8}$/', $req['HaishiDate'])) {
			$msg.= '廃止日は8桁の半角数字で入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		}
		if (empty($this->err['HaishiDate'])) {
			if (checkdate(substr($req['HaishiDate'],4,2), substr($req['HaishiDate'],6,2), substr($req['HaishiDate'],0,4)) == false) {
				$msg.= '廃止日が正しくありません。<br>';
				$this->err['HaishiDate'] = 1;
			} elseif ($req['HaishiDate'] < $req['appdatefrom']) {
				$msg = '廃止日が適用開始日以前の日付です。<br>';
				$this->err['HaishiDate'] = 1;
			}
		}
		return $msg;
	}
}	
?>
