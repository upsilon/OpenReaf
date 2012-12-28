<?php
/*
 *  Copyright 2008-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  Action.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/DB.class.php';
require OPENREAF_ROOT_PATH.'/app/class/Smarty.class.php';

abstract class Action
{
	protected $oDB = null;
	protected $con = null;
	protected $oSmarty = null;
	protected $type = '';

	function __construct($type='pc')
	{
		$this->type = $type;
		$this->oDB = new DBUtil();
		$this->con = $this->oDB->connect();
		$this->oSmarty = new MySmarty($type);
		$this->start_session();
	}

	function display_error_msg($message, $back_link='')
	{
		$mode = isset($_SESSION['UID']) ? 1 : 0;

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('MODE', $mode);	
		$this->oSmarty->assign('BACK_LINK', $back_link);	
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('confirm.tpl');
		exit();
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

	function check_login($return='')
	{
		if (_TermClass_ != 'Mobile' && _TermClass_ != 'SmartPhone') {
			if (isset($_COOKIE[session_name()])) {
				setcookie(session_name(), session_id(), time()+intval(_IDLE_TIMEOUT_), '/');
			} else {
				header('Location: index.php');
			}
		}

		$result = true;

		if (isset($_SESSION['UID']) && isset($_SESSION['PWD'])) {
			$sql = "SELECT * FROM m_user";
			$sql.= " WHERE localgovcode=? AND userid=? AND pwd=?";
			$sql.= " AND userlimit>=? AND yoyakukyokaflgweb='1'";
			$aWhere = array(_CITY_CODE_, $_SESSION['UID'], $_SESSION['PWD'], date('Ymd'));
			$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC); 
			if ($row) {
				if ($row['userjyoutaikbn'] != '1'
					&& intval($row['stoperasedate']) <= intval(date('Ymd'))) {
					$result = false;
				}
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}
		if (!$result) {
			if (_TermClass_ == 'Mobile' || _TermClass_ == 'SmartPhone') {
				$_REQUEST['return'] = $return;
				$class_name = 'loginAction';
				require OPENREAF_ROOT_PATH.'/app/'.$this->type.'/'.$class_name.'.class.php';
				$oAction = new $class_name($this->type);
				$oAction->execute();
				exit();
			}
			header('Location:index.php?op=login&return='.$return);
			exit();
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
