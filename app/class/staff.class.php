<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員データクラス
 *
 *  staff.class.php
 */

class staff
{
	private $con = null;
	private $err = array();

	function __construct(&$con)
	{
		$this->con = $con;
	}

	function get_staff_data($staffid)
	{
		$sql = 'SELECT * FROM m_staff';
		$sql.= ' WHERE localgovcode=? AND staffid=?';
		$aWhere = array(_CITY_CODE_, $staffid);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_facilities_code($staffid)
	{
		$sql = 'SELECT shisetsucode, shitsujyocode';
		$sql.= ' FROM m_staffshisetsu';
		$sql.= ' WHERE localgovcode=? AND staffid=?';
		$sql.= ' ORDER BY shisetsucode, shitsujyocode';
		$aWhere = array(_CITY_CODE_, $staffid);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$recs = array();
		foreach ($res as $val)
		{
			$pcode = $val['shisetsucode'];
			if ($val['shitsujyocode'] != '') $pcode.= ':'.$val['shitsujyocode'];
			$recs[] = $pcode;
		}
		unset($res);
		return $recs;
	}

	function make_facility_list($res, &$aShisetsu)
	{
		$recs = array();
		foreach ($res as $val)
		{
			if (preg_match("/:/", $val)) {
				$code = explode(':', $val);
				$recs[$val] = $aShisetsu[$code[0]].' '.$this->get_shitsujyo_name($code[0], $code[1]);
			} else {
				$recs[$val] = $aShisetsu[$val];
			}
		}
		return $recs;
	}

	function get_shitsujyo_name($scd, $rcd)
	{
		$sql = 'SELECT shitsujyoname FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		return $this->con->getOne($sql, $aWhere);
	}

	function get_shisetsu_options($all=false)
	{
		$sql = 'SELECT shisetsucode, shisetsuname, shisetsuskbcode
			FROM m_shisetsu';
		$where = " WHERE (haishidate>'".date('Ymd')."' OR haishidate='' OR haishidate IS NULL)";
		if ($all) $where = '';
		$orderby = ' ORDER BY shisetsuskbcode, shisetsucode';
		$res = $this->con->getAll($sql.$where.$orderby);

		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	function get_shitsujyo_options(&$aShisetsu)
	{
		$sql = "SELECT shisetsucode, shitsujyocode, shitsujyoname
			FROM m_shitsujyou
			WHERE (haishidate>? OR haishidate='' OR haishidate IS NULL)
			ORDER BY shisetsucode, shitsujyocode";
		$res = $this->con->getAll($sql, array(date('Ymd')), DB_FETCHMODE_ASSOC);

		$recs = array();
		foreach ($aShisetsu as $code => $name)
		{
			$recs[$code]['ShisetsuCode'] = $code;
			$recs[$code]['ShisetsuName'] = $name;
			$pCode = '';
			$pName = '';
			foreach ($res as $key => $val)
			{
				if ($code == $val['shisetsucode']) {
					if ($pCode) $pCode .= ',';
					if ($pName) $pName .= ',';
					$pCode .= '"'.$val['shitsujyocode'].'"';
					$pName .= '"'.$val['shitsujyoname'].'"';
				}
			}
			$recs[$code]['strShitsujyoCode'] = $pCode;
			$recs[$code]['strShitsujyoName'] = $pName;
		}
		unset($res);
		return $recs;
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req, $duplicate_check=false)
	{
		$msg = '';

		if ($req['staffid'] == '') {
			$msg .= '職員IDを入力してください。<br>';
			$this->err['StaffID'] = 1;
		} elseif ($duplicate_check) {
			$res = $this->get_staff_data($req['staffid']);
			if (!empty($res)) {
				$msg.= '職員IDが重複しています。<br>';
				$this->err['StaffID'] = 1;
			}
		}
		if ($req['staffname'] == '') {
			$msg .= '職員名を入力してください。<br>';
			$this->err['StaffName'] = 1;
		}
		if ($req['bushocode'] == '') {
			$msg .= '所属部署を選択してください。<br>';
			$this->err['BushoCode'] = 1;
		}
		/*
		if ($req['staffnum'] == '') {
			$msg .= '職員番号を入力してください。<br>';
			$this->err['StaffNum'] = 1;
		}*/

		if (trim($req['appdatefrom']) == '') {
			$msg .= "適用開始日を入力してください。<br>";
			$this->err['AppDateFrom'] = 1;
		} elseif (!preg_match("/^[0-9]{8}$/", $req['appdatefrom'])) {
			$msg .= "適用開始日を8桁の半角数字で入力してください。<br>";
			$this->err['AppDateFrom'] = 1;
		}
		if (empty($this->err['AppDateFrom'])) {
			if (!$this->chkDate($req['appdatefrom'])){
				$msg .= "適用開始日の年月日が不正です。<br>";
				$this->err['AppDateFrom'] = 1;
			}
		}

		if ($req['pwd'] == '') {
			$msg .= "パスワードを入力してください。<br>";
			$this->err['Pwd'] = 1;
		} elseif (!preg_match("/^[0-9a-zA-Z]+$/", $req['pwd'])) {
			$msg .= "パスワードは半角で入力してください。<br>";
			$this->err['Pwd'] = 1;
		}
		if ($req['pwd2'] == '') {
			$msg .= "確認用のパスワードを入力してください。<br>";
			$this->err['Pwd2'] = 1;
		} elseif ($req['pwd'] != $req['pwd2']) {
			$this->err['Pwd'] = 1;
			$this->err['Pwd2'] = 1;
			$msg .= "確認用のパスワードが一致しません。<br>";
		}

		/*
		if (!isset($_REQUEST["userfacilities"]))
		{
			$msg .= '対象施設指定してください。<br>';
			$this->err["UserFacilitys"] = 1;
		}*/

		if (!isset($req['tourokukbn'])) {
			$msg .= '登録区分を指定してください。<br>';
			$this->err['TourokuKbn'] = 1;
		}
		// KengenCode
		$chkbox_errflg = 0;
		if (isset($req['kengencode1'])) {
			++$chkbox_errflg;
		}
		if (isset($req['kengencode2'])) {
			++$chkbox_errflg;
		}
		if (isset($req['kengencode3'])) {
			++$chkbox_errflg;
		}
		if (isset($req['kengencode4'])) {
			++$chkbox_errflg;
		}
		if (isset($req['kengencode5'])) {
			++$chkbox_errflg;
		}
		if (isset($req['kengencode6'])) {
			++$chkbox_errflg;
		}

		if ($chkbox_errflg == 0) {
			$this->err['KengenCode1'] = 1;
			$this->err['KengenCode2'] = 1;
			$this->err['KengenCode3'] = 1;
			$this->err['KengenCode4'] = 1;
			$this->err['KengenCode5'] = 1;
			$this->err['KengenCode6'] = 1;
			$msg .= '業務権限を指定してください。<br>';
		}
		return $msg;
	}

	function check_haishi_date(&$req)
	{
		$msg = '';

		if (trim($req['HaishiDate']) == '') {
			$msg.= '廃止日を入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		} elseif (!preg_match("/^[0-9]{8}$/", $req['HaishiDate'])) {
			$msg.= '廃止日は8桁の半角数字で入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		}
		if (empty($this->err['HaishiDate'])) {
			if (!$this->chkDate($req['HaishiDate'])) {
				$msg.= '廃止日が正しくありません。<br>';
				$this->err['HaishiDate'] = 1;
			} elseif ($req['HaishiDate'] < $req['AppDateFrom']) {
				$msg = '廃止日が適用開始日以前の日付です。<br>';
				$this->err['HaishiDate'] = 1;
			}
		}
		return $msg;
	}

	function chkDate($_date)
	{
		$year = substr($_date, 0, 4);
		$month = substr($_date, 4, 2);
		$day = substr($_date, 6, 2);
		return(checkdate($month, $day, $year));
	}
}
?>
