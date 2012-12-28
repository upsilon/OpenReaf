<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者クラス
 *
 *  user.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class user extends system_common
{
	private $uid = ''; 
	private $err = array();

	function __construct(&$con, $uid)
	{
		parent::__construct($con);

		$this->uid = $uid;
	}

	function set_uid($uid)
	{
		$this->uid = $uid;
	}

	function get_user_columns(&$cols)
	{
		$sql = 'SELECT * FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $this->uid);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if (empty($res)) return array();

		foreach ($cols as $key => $val)
		{
			if ($val[1] == 'date') {
				$res[$key.'year'] = substr($res[$key], 0, 4);
				$res[$key.'month'] = substr($res[$key], 4, 2);
				$res[$key.'day'] = substr($res[$key], 6, 2);
			}
		}
		return $res;
	}

	function get_user_shisetsu_list()
	{
		$sql = 'SELECT shisetsu FROM m_user WHERE localgovcode=? AND userid=?';
		$value = $this->con->getOne($sql, array(_CITY_CODE_, $this->uid));
		if ($value == '') return array();

		$sql = 'SELECT shisetsuname, shisetsucode, shisetsuskbcode FROM m_shisetsu';
		$sql.= " WHERE localgovcode=? AND (shisetsucode='".str_replace(',', "' OR shisetsucode='", $value)."')";
		$sql.= " AND (haishidate>? OR haishidate IS NULL OR haishidate = '')";
		$sql.= ' ORDER BY shisetsuskbcode, shisetsucode';
		$aWhere = array(_CITY_CODE_, date('Ymd'));
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_user_purpose_list()
	{
		$sql = 'SELECT purpose FROM m_user WHERE localgovcode=? AND userid=?';
		$value = $this->con->getOne($sql, array(_CITY_CODE_, $this->uid));
		if ($value == '') return array();

		$sql = 'SELECT mokutekiname, mokutekicode, mokutekiskbcode FROM m_mokuteki';
		$sql.= " WHERE localgovcode=? AND (mokutekicode='".str_replace(',', "' OR mokutekicode='", $value)."')";
		$sql.= ' ORDER BY mokutekiskbcode, mokutekicode';
		return $this->con->getAll($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);
	}

	function get_user_genmen()
	{
		$sql = "SELECT g.koteigenname, u.koteigencode, u.appday, limitday, keizokuflg
			FROM m_usrgenmen u
			JOIN m_genmen g USING (localgovcode, koteigencode)
			WHERE u.localgovcode=? AND u.userid=?";
		$aWhere = array(_CITY_CODE_, $this->uid);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if ($res) {
			$res['AppDayYear'] = substr($res['appday'], 0, 4);
			$res['AppDayMonth'] = substr($res['appday'], 4, 2);
			$res['AppDayDay'] = substr($res['appday'], 6, 2);
			$res['LimitDayYear'] = substr($res['limitday'], 0, 4);
			$res['LimitDayMonth'] = substr($res['limitday'], 4, 2);
			$res['LimitDayDay'] = substr($res['limitday'], 6, 2);
		}
		return $res;
	}

	function get_feekbn_options()
	{
		$sql = 'SELECT feekbn, feekbnname FROM m_feekbn
			WHERE localgovcode=? ORDER BY feekbn';
		$aWhere = array(_CITY_CODE_);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($res);
		return $recs;
	}

	// 重複チェック
	function check_duplicate(&$req, $mode)
	{
		$sql = 'SELECT COUNT(userid) FROM m_user
			WHERE localgovcode=? AND kojindankbn=? AND namesei=? AND adr1=?';
		$aWhere = array(_CITY_CODE_, $req['kojindankbn'], $req['namesei'], $req['adr1']);
		if ($mode == 'mod') {
			$sql.= ' AND userid<>?';
			array_push($aWhere, $req['userid']);
		}
		$res = $this->con->getOne($sql, $aWhere);

		if ($res) return true;
		return false;
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$cols, &$req, &$sys, $autoAssign)
	{
		$msg = '';

		foreach ($cols as $key => $val)
		{
			if ($val[3] != 'basic' && $val[3] != 'user') continue;
			if (!$val[2]) continue;
			if ($val[1] == 'text' || $val[1] == 'number') {
				if (trim($req[$key]) == '') {
					$msg.= $val[0].'を入力してください。<br>';
					$this->err[$key] = 'class="error"';
				}
			} elseif ($val[1] == 'radio') {
				if (!isset($req[$key])) {
					$msg.= $val[0].'を指定してください。<br>';
					$this->err[$key] = 'class="error"';
				}
			} elseif ($val[1] == 'date') {
				$y = intval($req[$key.'year']);
				$m = intval($req[$key.'month']);
				$d = intval($req[$key.'day']);
				if ($y == 0 || $m == 0 || $d == 0) {
					$msg.= $val[0].'を入力してください。<br>';
					$this->err[$key] = 'class="error"';
				} elseif (!checkdate($m, $d, $y)) {
					$msg.= $val[0].'を確認してください。<br>';
					$this->err[$key] = 'class="error"';
				}
			} elseif ($val[1] == 'select') {
				if ($req[$key] == '') {
					$msg.= $val[0].'を指定してください。<br>';
					$this->err[$key] = 'class="error"';
				}
			}
		}
		if (!$autoAssign) {
			$m = $this->validate_userid($req['userid'], $sys);
			if ($m != '') {
				$msg .= $m;
				$this->err['userid'] = 'class="error"';
			}
		}
		if (empty($this->err['pwd'])) {	
			$m = $this->validate_password($req['pwd'], $sys);
			if ($m != '') {
				$msg .= $m;
				$this->err['pwd'] = 'class="error"';
			}
		}
		if ($req['mailsendflg'] != '0' && $req['mailadr'] == '') {
			$msg .= 'メール送信を指定しているため、メールアドレスを入力してください。<br>';
			$this->err['mailadr'] = 'class="error"';
		}
		return $msg;
	}

	function is_auto_assign()
	{
		$sql = 'SELECT saibanflg FROM m_saiban WHERE localgovcode=? AND saibancode=?';
		$aWhere = array(_CITY_CODE_, 'UserID');
		$res = $this->con->getOne($sql, $aWhere);
		if ($res == '1') return true;
		return false;
	}

	function get_busho_code()
	{
		$sql = "SELECT bushocode FROM m_staff WHERE localgovcode=? AND staffid=?";
		$aWhere = array(_CITY_CODE_, $_SESSION['userid']);
		return $this->con->getOne($sql, $aWhere);
	}

	// 利用者ID採番処理
	function get_userid()
	{
		$this->con->query("LOCK TABLES m_saiban, m_user WRITE");

		$sql = "SELECT * FROM m_saiban";
		$sql .= " WHERE localgovcode =? AND saibancode=?";
		$aWhere = array(_CITY_CODE_, 'UserID');
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$newID = '';
		// UserIDが存在しないまで加算する(手入力により重複がありえる)
		do
		{
			++$rec['saibanno'];
			$sStyle = "%0{$rec['saibannolng']}d";
			$newID = $rec['prefix'].sprintf($sStyle, $rec['saibanno']).$rec['suffix'];
			$existFlg = $this->exist_userid($newID);
			if (!$existFlg) break;
		} while(1);

		$upData['saibanno'] = $rec['saibanno'];
		$upData['upddate'] = date('Ymd');
		$upData['updtime'] = date('His');
		$upData['updiD'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_."' AND saibancode='UserID'";
		$this->con->autoExecute('m_saiban', $upData, DB_AUTOQUERY_UPDATE, $where);
		$num = $this->con->affectedRows();
		// ロック解除
		$this->con->query('UNLOCK TABLES');

		if ($num == 1) {
			return $newID;
		}
		// 採番失敗
		return false;
	}

	function exist_userid($UserID)
	{
		$sql = 'SELECT COUNT(userid) FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $UserID);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res) return true;
		return false;
	}

	// 使用文字チェック
	function validate_input_type($str, $type)
	{
		global $aInputType;
		$match_str = '';

		switch ($type) {
		case '2':
			$match_str .= 'A-HJ-NP-Z';
		case '1':
			$match_str .= 'a-km-pr-z';
		case '0':
			$match_str .= '0-9';
		}
		if (!preg_match('/^['.$match_str.']+$/', $str)) {
			return $aInputType[$type].'で入力してください。<br>';
		}
		return '';
	}

	// 利用者IDチェック
	function validate_userid($UserID, &$sys)
	{
		$msg = '';
		if ($UserID == '') {
			return '利用者IDを入力してください。<br>';
		}
		$m = $this->validate_input_type($UserID, $sys['useridtype']);
		if ($m != '') $msg = '利用者IDは'.$m;
		if (strlen($UserID) < $sys['useridlngmin'] || strlen($UserID) > $sys['useridlng']) {
			$msg.= '利用者IDは'.$sys['useridlngmin'] .'文字以上'. $sys['useridlng'] . '文字以内で入力してください。<br>';
		}
		if ($msg == '') {
			if ($this->exist_userid($UserID)) {
				$msg.= '指定の利用者IDは、既に登録されています<br>';
			}
		}
		return $msg;
	}

	function validate_password($Pwd, &$sys)
	{
		$msg = '';

		$m = $this->validate_input_type($Pwd, $sys['pwdtype']);
		if ($m != '') $msg = 'パスワードは'.$m;
		if (strlen($Pwd) < $sys['pwdlngmin'] || strlen($Pwd) > $sys['pwdlng']) {
			if ($sys['pwdlngmin'] == $sys['pwdlng']) {
				$msg.= 'パスワードは'.$sys['pwdlng'].'文字で入力してください。';
			} else {
				$msg.= 'パスワードは'.$sys['pwdlngmin'] .'文字以上'. $sys['PwdLng'] . '文字以内で入力してください。';
			}
		}
		return $msg;
	}
}
?>
