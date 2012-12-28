<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  loginAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/login.php';
require OPENREAF_ROOT_PATH.'/app/class/log.class.php';

class loginAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$return = isset($_REQUEST['return']) ? $_REQUEST['return'] : 'user_menu';
		if (empty($return)) $return = 'user_menu';

		$BackLink = getTopUrl();
		if ($return == 'fuzoku') {
			$BackLink = 'index.php?op=daily&UseDate='.$_SESSION['Y_I']['usedate'];
		}

		$sql = 'SELECT useridlng, useridlngmin, userid_size,
			pwdlng, pwdlngmin, pwd_size,
			loginkbn, logintimefrom, logintimeto,
			sitecloseflg, siteclosefrom, sitecloseto,
			lockoutflg, lockout_count, reentry_interval
			FROM m_systemparameter WHERE localgovcode=?';
		$sys = $this->con->getRow($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);

		$message = '';
		if ($sys['sitecloseflg'] == '1') {
			$message = OR_NOT_AVAILABLE;
		} elseif ($sys['sitecloseflg'] == '2') {
			if ($sys['siteclosefrom'] <= time() && time() <= $sys['sitecloseto']) {
				$message = OR_NOT_AVAILABLE;
			}
		}
		if ($sys['loginkbn'] == '1' && $message == '') {
			$Now = date('Hi');
			if ($sys['logintimefrom'] == $sys['logintimeto']) {
				$message = OR_NOT_AVAILABLE;
			} elseif ($Now < $sys['logintimefrom'] || $sys['logintimeto'] < $Now) {
				$message = sprintf(OR_LOGIN_TIME_CONTROL, $this->timeFormat($sys['logintimefrom']), $this->timeFormat($sys['logintimeto']));
			}
		}

		if ($message == '') {
			if (isset($_POST['check'])) {

				$UserID = $_POST['UserIdTextBox'];
				$Password = $_POST['PasswordTextBox'];

				$oLog = new log($this->type);
				$logmsg = $UserID.'/'.$_SERVER['REMOTE_ADDR'].' '.$return;

				$sql = "SELECT * FROM m_user
					WHERE localgovcode=? AND userid=?";
				$aWhere = array(_CITY_CODE_, $UserID);
				$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
				if (!$row) {
					$message = OR_AUTHENTICATION_FAIL;
					$oLog->setLog($logmsg.' ID does not exist');
				} elseif ($sys['lockoutflg'] != '0') {
					if ($sys['lockout_count'] <= $row['loginerr_count']) {
						$message = OR_DONT_PERMIT_LOGIN;
						if ($sys['lockoutflg'] == '2') {
							$reentry = $row['lastlogin'] + $sys['reentry_interval']*60;
							if ($reentry < time()) {
								$message = '';
								$oLog->setLog($logmsg.' account unlock');
							} else {
								$oLog->setLog($logmsg.' account lock');
							}
						} else {
							$oLog->setLog($logmsg.' account lock');
						}
					}
				}
				if ($message == '') {
					$today = intval(date('Ymd'));
					if ($row['pwd'] != $Password) {
						$message = OR_AUTHENTICATION_FAIL;
						$current_count = $row['loginerr_count'] + 1;
						if ($sys['lockoutflg'] != '0' && $sys['lockout_count'] <= $current_count) {
							$message = OR_DONT_PERMIT_LOGIN;
						}
						$this->set_access_data($UserID, $current_count);
						$oLog->setLog($logmsg.' password error');
					} elseif ($row['userjyoutaikbn'] != '1'
						&& intval($row['stoperasedate']) <= $today) {
						$message = OR_DISCONTINUANCE;
					} elseif (intval($row['userlimit']) < $today) {
						$message = OR_CONFIRM_EXPIRATION;
					} elseif ($row['yoyakukyokaflgweb'] == '0') {
						$message = OR_CONFIRM_SETTING;
					} else {
						$_SESSION['UID'] = $UserID;
						$_SESSION['PWD'] = $Password;
						$_SESSION['UNAME'] = $row['namesei'];
						$_SESSION['USERLIMIT'] = $row['userlimit'];
						$_SESSION['KOJINDANKBN'] = $row['kojindankbn'];
						$_SESSION['USERAREAKBN'] = $row['userareakbn'];
						$_SESSION['USERSTATUS'] = 1;
						if ($row['userjyoutaikbn'] != '1'
							&& intval($row['stoperasedate']) <= $today) {
							$_SESSION['USERSTATUS'] = 0;
						}
						$_SESSION['USEKBN'] = $row['usekbn'];

						$this->set_access_data($UserID, 0);
						$oLog->setLog($logmsg);
						$loc = 'Location: index.php?op='.$return;
						header($loc);
						exit();
					}
				}
			} else {
				$message = OR_INPUT_YOUR_ID;
			}
		}
		$this->oSmarty->assign('condition', OR_AUTHENTICATION);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('sys', $sys);
		$this->oSmarty->assign('return', $return);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('login.tpl');
	}

	function set_access_data($userid, $count)
	{
		$dataset = array();
		$dataset['lastlogin'] = time();
		$dataset['loginerr_count'] = $count;
		$where = "localgovcode='"._CITY_CODE_."' AND userid='".$userid."'";
		$this->oDB->update('m_user', $dataset, $where);
	}

	function timeFormat($time)
	{
		return sprintf('%d:%s', intval(substr($time, 0, 2)), substr($time, 2, 2));
	}
}
?>
