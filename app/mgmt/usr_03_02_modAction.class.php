<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者詳細情報変更
 *
 *  usr_03_02_modAction.class.php
 *  usr_03_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_03_02_modAction extends adminAction
{
	private $err = array();

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$uid = $_REQUEST['UserID'];

		$oUC = new user($this->con, $uid);

		if (isset($_POST['tourokuBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$dataset = array();
				foreach ($columns as $key => $val)
				{
					if ($val[3] != 'detail') continue;
					if ($val[1] == 'date') {
						$y = substr('0000'.$_POST[$key.'year'], -4, 4);
						$m = substr('00'.$_POST[$key.'month'], -2, 2);
						$d = substr('00'.$_POST[$key.'day'], -2, 2);
						$dataset[$key] = $y.$m.$d;
					} elseif (isset($_POST[$key])) {
						if ($val[1] == 'number') {
							$dataset[$key] = intval($_POST[$key]);
						} else {
							$dataset[$key] = $_POST[$key];
						}
					}
				}
				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];
				$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

				$rc = $this->oDB->update('m_user', $dataset, $where);
				if ($rc < 0) {
					$message = '登録できませんでした。';
					$success = -1;
				} else {
					$message = '正常に登録しました。';
					$success = 1;
				}
			} else {
				$success = -1;
			}
		}
		$para = $oUC->get_user_columns($columns);

		if ($success < 0) {
			foreach ($para as $key => $val)
			{
				if (isset($_POST[$key])) $para[$key] = $_POST[$key];
			}
		}

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_02_mod');
		$this->oSmarty->display('usr_03_02.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		global $columns;

		$msg = '';

		foreach ($columns as $key => $val)
		{
			if ($val[3] != 'detail') continue;
			if (!$val[2]) continue;
			if ($val[1] == 'date') {
				$y = intval($req[$key.'year']);
				$m = intval($req[$key.'month']);
				$d = intval($req[$key.'day']);
				if ($y == 0 || $m == 0 || $d == 0) {
					$msg.= $val[0].'を入力してください。\n';
					$this->err[$key] = 'class="error"';
				} elseif (!checkdate($m, $d, $y)) {
					$msg.= $val[0].'を確認してください。\n';
					$this->err[$key] = 'class="error"';
				}
			} elseif (empty($req[$key])) {
				$msg.= $val[0].'を入力してください。\n';
				$this->err[$key] = 'class="error"';
			}
		}
		return $msg;
	}
}
?>
