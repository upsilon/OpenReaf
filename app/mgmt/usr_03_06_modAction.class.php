<?php
/*
 *  Copyright 2011-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者アクセス状態変更
 *
 *  usr_03_06_modAction.class.php
 *  usr_03_06.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_03_06_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns;

		$message = '';
		$para = array();

		$this->set_header_info();

		$uid = $_REQUEST['UserID'];

		$oUC = new user($this->con, $uid);

		if (isset($_POST['tourokuBtn'])) {
			$dataset['loginerr_count'] = 0;
			$dataset['upddate'] = date('Ymd');
			$dataset['updtime'] = date('His');
			$dataset['updid'] = $_SESSION['userid'];
			$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

			$rc = $this->oDB->update('m_user', $dataset, $where);
			if ($rc < 0) {
				$message = '登録できませんでした。';
			} else {
				$message = '正常に登録しました。';
			}
		}
		$para = $oUC->get_user_columns($columns);
		$para['LastLogin'] = '未アクセス';
		if ($para['lastlogin'] > 0) {
			$para['LastLogin'] = $oUC->get_date_view($para['lastlogin'], true, 2);
		}
		$aSystem = $oUC->get_system_parameters();
		$para['LockOut'] = 0;
		if ($aSystem['lockoutflg'] != '0' &&
			$aSystem['lockout_count'] <= $para['loginerr_count']) {
			$para['LockOut'] = 1;
		}

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_06_mod');
		$this->oSmarty->display('usr_03_06.tpl');
	}
}
?>
