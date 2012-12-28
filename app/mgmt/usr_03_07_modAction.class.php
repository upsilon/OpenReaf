<?php
/*
 *  Copyright 2011-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  メッセージ設定
 *
 *  usr_03_07_modAction.class.php
 *  usr_03_07.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_03_07_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns, $dispflg_arr;

		$message = '';
		$para = array();

		$this->set_header_info();

		$uid = $_REQUEST['UserID'];

		$oUC = new user($this->con, $uid);

		$para = $oUC->get_user_columns($columns);

		if (isset($_POST['tourokuBtn'])) {
			$dataset = array();
			$dataset['notice'] = htmlspecialchars_decode($_POST['notice'], ENT_QUOTES);
			$dataset['notice_published'] = 0;
			if (isset($_POST['published_flg'])) {
				$dataset['notice_published'] = strtotime($_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay'].$_POST['FromHour'].$_POST['FromMinute'].'00');
			}
			$dataset['notice_expired'] = 0;
			if (isset($_POST['expired_flg'])) {
				$dataset['notice_expired'] = strtotime($_POST['ToYear'].$_POST['ToMonth'].$_POST['ToDay'].$_POST['ToHour'].$_POST['ToMinute'].'59');
			}
			$dataset['notice_flg'] = $_POST['notice_flg'];
			$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

			$rc = $this->oDB->update('m_user', $dataset, $where);
			if ($rc < 0) {
				$message = '登録できませんでした。';
			} else {
				$message = '正常に登録しました。';
			}
			$para = array_merge($para, $dataset);
			$para['published_flg'] = isset($_POST['published_flg']) ? 1 : 0;
			$para['expired_flg'] = isset($_POST['expired_flg']) ? 1 : 0;
		} else {
			$para['published_flg'] = $para['notice_published'] == 0 ? 0 : 1;
			$para['expired_flg'] = $para['notice_expired'] == 0 ? 0 : 1;
		}

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_07_mod');
		$this->oSmarty->display('usr_03_07.tpl');
	}
}
?>
