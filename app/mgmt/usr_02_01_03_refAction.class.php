<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者情報照会
 *
 *  usr_02_01_03_refAction.class.php
 *  usr_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_02_01_03_refAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns, $optionItems, $dispflg_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$uid = $_GET['UserID'];

		$oUC = new user($this->con, $uid);
		$aSystem = $oUC->get_system_parameters();

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

		foreach ($optionItems as $val)
		{
			$columns[strtolower($val)][4] = $oUC->get_codename_options($val);
		}
		$aFeeKbn = $oUC->get_feekbn_options();
		$aSystem = $oUC->get_system_parameters();
		$kengen_list = $oUC->get_user_shisetsu_list();
		$mokuteki_list = $oUC->get_user_purpose_list();
		$genmen_data = $oUC->get_user_genmen();

		$template = 'usr_02_01.tpl';
		if (isset($_GET['refonly'])) {
			$template = 'usr_02_04.tpl';
		}

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('kengen_list', $kengen_list);
		$this->oSmarty->assign('mokuteki_list', $mokuteki_list);
		$this->oSmarty->assign('genmen_data', $genmen_data);
		$this->oSmarty->assign('err', array());
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('autoAssign', true);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('aSystem', $aSystem);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('input_control', "readonly style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('button_control', "disabled style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'ref');
		$this->oSmarty->assign('op', 'usr_02_01_03_ref');
		$this->oSmarty->display($template);
	}
}
?>
