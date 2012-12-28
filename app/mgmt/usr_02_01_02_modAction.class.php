<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者情報変更
 *
 *  usr_02_01_02_modAction.class.php
 *  usr_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_02_01_02_modAction extends adminAction
{
	private $err = array();

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns, $optionItems, $dispflg_arr, $aInputType;

		$message = '';
		$success = 0;
		$para = array();
		$dup = false;

		$this->set_header_info();

		$uid = get_request_var('UserID');
		if (isset($_GET['NewID'])) {
			$uid = $_GET['NewID'];
			$message = '正常に登録しました。';
		}

		$oUC = new user($this->con, $uid);
		$aSystem = $oUC->get_system_parameters();

		if (isset($_POST['usernameflg'])) {
			$message = $oUC->check_input_data($columns, $_POST, $aSystem, true);
			if ($message == '') {
				$dup = $oUC->check_duplicate($_POST, 'mod');
				if (!$dup || ($dup && $_POST['usernameflg'] == '1')) {
					$dataset = array();
					foreach ($columns as $key => $val)
					{
						if ($val[3] != 'basic' && $val[3] != 'user') continue;
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
					unset($dataset['userid']);
					$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";
					$rc = $this->oDB->update('m_user', $dataset, $where);
					if ($rc < 0) {
						$message = '登録できませんでした。';
						$success = -1;
					} else {
						$message = '正常に登録しました。';
						$success = 1;
					}
					$dup = false;
				} else {
					$success = -1;
				}
			} else {
				$success = -1;
			}
		}
		$para = $oUC->get_user_columns($columns);
		$para['UpdStaffName'] = $oUC->get_staff_name($para['updid']);
		$para['UpdDateView'] = $oUC->getDateView($para['upddate']);
		$para['UpdTimeView'] = $oUC->getTimeView($para['updtime']);
		$para['LastLogin'] = '未アクセス';
		if ($para['lastlogin'] > 0) {
			$para['LastLogin'] = $oUC->get_date_view($para['lastlogin'], true, 2);
		}
		$para['LockOut'] = 0;
		if ($aSystem['lockoutflg'] != '0' &&
			$aSystem['lockout_count'] <= $para['loginerr_count']) {
			$para['LockOut'] = 1;
		}
		$para['Published'] = '指定なし';
		if ($para['notice_published'] > 0) {
			$para['Published'] = $oUC->get_date_view($para['notice_published']);
		}
		$para['Expired'] = '指定なし';
		if ($para['notice_expired'] > 0) {
			$para['Expired'] = $oUC->get_date_view($para['notice_expired']);
		}
		if ($success < 0) {
			foreach ($para as $key => $val)
			{
				if (isset($_POST[$key])) $para[$key] = $_POST[$key];
			}
		}
		foreach ($optionItems as $val)
		{
			$columns[strtolower($val)][4] = $oUC->get_codename_options($val);
		}
		$aFeeKbn = $oUC->get_feekbn_options();
		$kengen_list = $oUC->get_user_shisetsu_list();
		$mokuteki_list = $oUC->get_user_purpose_list();
		$genmen_data = $oUC->get_user_genmen();

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('kengen_list', $kengen_list);
		$this->oSmarty->assign('mokuteki_list', $mokuteki_list);
		$this->oSmarty->assign('genmen_data', $genmen_data);
		$this->oSmarty->assign('err', $oUC->get_error());
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('autoAssign', true);
		$this->oSmarty->assign('is_duplicate', $dup);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('aSystem', $aSystem);
		$this->oSmarty->assign('aInputType', $aInputType);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('input_control', '');
		$this->oSmarty->assign('button_control', '');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'mod');
		$this->oSmarty->assign('op', 'usr_02_01_02_mod');
		$this->oSmarty->display('usr_02_01.tpl');
	}
}
?>
