<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  loginAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/include/privilege.php';
require OPENREAF_ROOT_PATH.'/app/class/log.class.php';

class loginAction
{
	private $oDB = null;
	private $con = null;
	private $oSmarty = null;

	function __construct()
	{
		$this->oDB = new DBUtil();
		$this->con = $this->oDB->connect();
		$this->oSmarty = new MySmarty('mgmt');
	}

	function execute()
	{
		$errmsg = '';
		$back = 0;

		if (_USE_SSL_) {
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: '.getTopUrl('https'));
				return;
			}
		}

		$iccard = get_request_var('ICCARD');

		$template = 'login.tpl';
		if (_IC_CARD_) {
			if ($iccard != 'no') {
				$template = 'login_iccard.tpl';
			} else {
				$back = 1;
			}
		}

		// ログインボタンが押された場合
		if (isset($_POST['loginBtn']))
		{
			$rc = $this->login($_POST['userid'],$_POST['userpass']);
			if ($rc == 0) {
				$this->loginSuccess();
			} elseif ($rc == 2) {
				$errmsg = 'このIDは現在使用することができません。';
			} else {
				$errmsg = 'ログインできませんでした。IDとパスワードを確認してください。';
			}
		}
		// セッション保持されている場合(通常アクセスされた場合)
		else {
			$this->start_session();
			if (isset($_SESSION['userid']))
			{
				if ($this->login($_SESSION['userid'],$_SESSION['userpass']) == 0) {
					$this->loginSuccess();
				} else {
					$errmsg = 'ログインできませんでした。IDとパスワードを確認してください。';
				}
			}
		}

		$this->end_session();
		$this->oSmarty->assign('back', $back);
		$this->oSmarty->assign('errmsg', $errmsg);
		$this->oSmarty->display($template);

		exit;
	}

	// ログイン成功時
	function loginSuccess()
	{
		header('Location: index.php?op=rsv_01_00_list');
		exit;
	}

	// ログイン処理
	function login($userid, $userpass)
	{
		$oLog = new log();

		$id = $userid.'/'.$_SERVER['REMOTE_ADDR'];

		if (_PRIVILEGE_LDAP_FLAG_) {
			$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
			if ($ds) {
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				$sr = ldap_search($ds, _PRIVILEGE_LDAP_ROOTDN_, 'cn='.$userid);
				$info = ldap_get_entries($ds, $sr);

				if ($info['count'] == 0) {
					$oLog->setLog($id.' login error: no id');
					return 1;
				} elseif ($info[0]['userpassword'][0] != '{MD5}'.base64_encode(pack("H*", md5(trim($userpass))))) {
					$oLog->setLog($id.' login error: no match');
					return 1;
				} elseif ($info[0]['appdatefrom'][0] > date("Ymd")) {
					$oLog->setLog($id.' login error: before application day');
					return 2;
				} elseif ($info[0]['haishidate'] && $info[0]['haishidate'][0]<date('Ymd')) {
					$oLog->setLog($id.' login error: expiration');
					return 2;
				} else {
					$this->start_session();
					$_SESSION['userid'] = $userid;
					$_SESSION['userpass'] = $userpass;
					$_SESSION['usernm'] = $info[0]['staffname'][0];
					$_SESSION['usertype'] = $info[0]["tourokukbn"][0];
					$_SESSION['view']= get_privilege_ldap($info[0]);
				}
			} else {					
				$oLog->setLog($id.' login error');
				return 1;
			}
		} else {
			$sql = 'SELECT * FROM m_staff WHERE localgovcode=? AND staffid=?';
			$aWhere = array(_CITY_CODE_, $userid);
			$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

			if (count($row) == 0) {
				$oLog->setLog($id.' login error: no id');
				return 1;
			}
			if ($row['pwd'] != $userpass) {
				$oLog->setLog($id.' login error: no match');
				return 1;
			}
			// 適用日の確認
			if ($row['appdatefrom'] > date('Ymd')) {
				$oLog->setLog($id.' login error: before application day');
				return 2;
			}
			// 廃止の確認
			if ($row['haishidate'] && $row['haishidate'] <= date('Ymd')) {
				$oLog->setLog($id.' login error: expiration');
				return 2;
			}
			$this->start_session();
			$_SESSION['userid'] = $row['staffid'];
			$_SESSION['userpass'] = $row['pwd'];
			$_SESSION['usernm'] = $row['staffname'];
			$_SESSION['usertype']= $row['tourokukbn'];
			$_SESSION['view']= get_privilege($row);
		}
		$oLog->setLog($id.' login');
		return 0;
	}

	function start_session()
	{
		if (!session_id()) {
			session_name(_SESSION_NAME_);
			session_start();
		}
	}

	function end_session()
	{
		if(isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-1800, '/');
		}
		if (session_id()) {
			session_unset();
			session_destroy();
		}
	}
}
?>
