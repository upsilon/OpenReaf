<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者情報登録
 *
 *  usr_02_01_01_regAction.class.php
 *  usr_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_02_01_01_regAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns, $optionItems, $aInputType;

		$message = '';
		$success = 0;
		$para = array();
		$dup = false;

		$this->set_header_info();

		$oUC = new user($this->con, '');
		$aSystem = $oUC->get_system_parameters();
		$autoAssign = $oUC->is_auto_assign();

		if (isset($_POST['usernameflg'])) {
			$message = $oUC->check_input_data($columns, $_POST, $aSystem, $autoAssign);
			if ($message == '') {
				$dup = $oUC->check_duplicate($_POST, 'reg');
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
					if ($autoAssign) {
						$dataset['userid'] = $oUC->get_userid();
					}
					if ($dataset['hyoujimei'] == '') {
						$dataset['hyoujimei'] = $dataset['userid'];
					}
					$dataset['temporaryid'] = '';
					$dataset['tourokubushocode'] = $oUC->get_busho_code();
					$dataset['firstentrydate'] = date('Ymd');
					$dataset['newapplydate'] = date('Ymd');
					$dataset['shisetsu'] = '';
					$dataset['purpose'] = '';
					$dataset['userjyoutaikbn'] = '1';
					$dataset['localgovcode'] = _CITY_CODE_;
					$dataset['upddate'] = date('Ymd');
					$dataset['updtime'] = date('His');
					$dataset['updid'] = $_SESSION['userid'];

					$rc = $this->oDB->insert('m_user', $dataset);
					if ($rc < 0) {
						$message = '登録できませんでした。';
					} else {
						$message = '正常に登録しました。';
						$success = 1;
						$_POST['userid'] = $dataset['userid'];
						header('Location:index.php?op=usr_02_01_02_mod&NewID='.$dataset['userid']);
						return;
					}
					$dup = false;
				}
			}
			$para = $_POST;
		} else {
			$para['firstapplydateyear'] = date('Y');
			$para['firstapplydatemonth'] = date('m');
			$para['firstapplydateday'] = date('d');
		}

		foreach ($optionItems as $val)
		{
			$columns[strtolower($val)][4] = $oUC->get_codename_options($val);
		}
		$aFeeKbn = $oUC->get_feekbn_options();

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('err', $oUC->get_error());
		$this->oSmarty->assign('autoAssign', $autoAssign);
		$this->oSmarty->assign('is_duplicate', $dup);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('aSystem', $aSystem);
		$this->oSmarty->assign('aInputType', $aInputType);
		$this->oSmarty->assign('input_control', '');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'reg');
		$this->oSmarty->assign('op', 'usr_02_01_01_reg');
		$this->oSmarty->display('usr_02_01.tpl');
	}
}
?>
