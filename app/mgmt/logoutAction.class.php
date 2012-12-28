<?php
/*
 *  Copyright 2009-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  logoutAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/log.class.php';

class logoutAction extends adminAction
{
	function __construct()
	{
		$this->start_session();
	}

	function execute()
	{
		$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 'timeout';

		$this->execute_logout();
		$oLog = new log();
		$oLog->setLog($userid.' logout');

		exit;
	}
}
?>
