<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  adminAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/DB.class.php';
require OPENREAF_ROOT_PATH.'/app/class/Smarty.class.php';
require OPENREAF_ROOT_PATH.'/app/class/privilege.class.php';

abstract class adminAction
{
	protected $oDB = null;
	protected $con = null;
	protected $oSmarty = null;
	protected $oPrivilege = null;

	function __construct()
	{
		$mode = 'mgmt';
		$this->oDB = new DBUtil();
		$this->con = $this->oDB->connect();
		$this->oSmarty = new MySmarty($mode);
		$this->start_session();
		$this->check_login();
		$this->oPrivilege = new privilege($_SESSION['userid'], $this->con);
	}

	function set_header_info()
	{
		global $aWeekJ;

		$this->oSmarty->assign('weekday', $aWeekJ[date("w")].'曜日');
		$this->oSmarty->assign('ymd', '平成'.(date('Y')-1988).'年'.date('m月d日'));
		$this->oSmarty->assign('user_name', @$_SESSION['usernm']);
		$this->oSmarty->assign('user_type', @$_SESSION['usertype']);
		$this->oSmarty->assign('user_view', @$_SESSION['view']);
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
			setcookie(session_name(), '', time()-86400, '/');
		}
		if (session_id()) {
			session_unset();
			session_destroy();
		}
	}

	function execute_logout($url='index.php')
	{
		$this->end_session();
		header("Location: ".$url);
		exit;
	}

	function check_login($url='index.php')
	{
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), session_id(), time()+intval(_IDLE_TIMEOUT_), '/');
		} else {
			header('Location: '.$url);
		}
		if (!isset($_SESSION['userid'])) {
			$this->execute_logout($url);
		}
	}

	abstract function execute();

	function getToken($key)
	{
		srand(microtime() * 100000);
		$salt = _SALT_;
		$pToken = md5($salt . uniqid(rand(), true));
		$_SESSION['TOKEN_'.$key] = $pToken;
		$_SESSION['TOKEN_KEY__'] = $key;

		return $pToken;
	}

	function validateToken()
	{
		$flag = true;
		$pkey = '';

		if (isset($_SESSION['TOKEN_KEY__'])) {
			$pkey = $_SESSION['TOKEN_KEY__'];
			unset($_SESSION['TOKEN_KEY__']);
		} else {
			return false;
		}

		$token = isset($_REQUEST[$pkey]) ? $_REQUEST[$pkey] : null;

		if (!isset($_SESSION['TOKEN_'.$pkey])) {
			$flag = false;
		} else {
			if ($_SESSION['TOKEN_'.$pkey] != $token) {
				$flag = false;
			}
			unset($_SESSION['TOKEN_'.$pkey]);
		}

		return $flag;
	}
}
?>
