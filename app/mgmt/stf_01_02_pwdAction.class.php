<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員パスワード変更
 *
 *  stf_01_02_pwdAction.class.php
 *  stf_01_02.tpl
 */

class stf_01_02_pwdAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$para = array();

		$this->set_header_info();

		$uid = $_SESSION['userid'];

		if (isset($_POST['updateBtn'])) {

			$message = $this->check_input_data($_POST);
			if ($message == '')
			{
				if ($this->update_staff_pwd($_POST, $uid)) {
					$message = 'パスワードを変更しました。';
					$_SESSION['userpass'] = $_POST['pwd'];
				} else {
					$message = '変更できませんでした。';
					$para = $_POST;
				}
			} else {
				$para = $_POST;
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->display('stf_01_02.tpl');
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!$this->is_exist_staff($dataset)) {
			 return '現在のパスワードが間違っています。<br>';
		}
		if (strlen($dataset['pwd']) == 0) {
			$msg.= '新しいパスワードを入力してください。<br>';
		}
		if (strlen($dataset['pwd2']) == 0) {
			$msg.= '確認用のパスワードを入力してください。<br>';
		} else {
			if ($dataset['pwd'] != $dataset['pwd2']) {
				$msg.= '確認用のパスワードが一致しません。<br>';
			}
		}
		return $msg;
	}

	function is_exist_staff(&$dataset)
	{
		$sql = 'SELECT COUNT(staffid) FROM m_staff WHERE localgovcode=? AND staffid=? AND pwd=?';
		$aWhere = array(_CITY_CODE_, $_SESSION['userid'], $dataset['oldpwd']);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res == 0) {
			return false;
		} else {
			return true;
		}
	}

	function update_staff_pwd (&$req, $uid)
	{
		$dataset = array();
		$dataset['pwd'] = $req['pwd'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_."' AND staffid='".$uid."'";
		$rc = $this->oDB->update('m_staff', $dataset, $where);
		if ($rc < 0) {
			return false;
		}

		if (_PRIVILEGE_LDAP_FLAG_) {
			$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
			if ($ds) {
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				$sr = ldap_search($ds, _PRIVILEGE_LDAP_ROOTDN_, 'cn='.$uid);
				$curinfo = ldap_get_entries($ds, $sr);

				$info = array();
				$info['userPassword'] = '{MD5}'.base64_encode(pack("H*",md5($req['pwd']))); 
				ldap_modify($ds, $curinfo[0]['dn'], $info);
			}
		}
		return true;
	}
}
?>
