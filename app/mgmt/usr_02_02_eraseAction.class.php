<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者利用停止・抹消
 *
 *  usr_02_02_eraseAction.class.php
 *  usr_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_02_02_eraseAction extends adminAction
{
	private $err = array();

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $columns, $optionItems;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$uid = get_request_var('UserID');

		$oUC = new user($this->con, $uid);

		if (isset($_POST['updateBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				if ($this->update_user_status($_POST, $uid)) {
					$message = '正常に登録しました。';
					$success = 1;
				} else {
					$message = '登録できませんでした。';
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

		if ($success < 0) {
			$para['userjyoutaikbn'] = $_POST['userjyoutaikbn'];
			$para['stoperasedate'] = $_POST['stoperasedate'];
			$para['stopenddate'] = $_POST['stopenddate'];
			$para['stoperasejiyu'] = $_POST['stoperasejiyu'];
		}
		foreach ($optionItems as $val)
		{
			$columns[strtolower($val)][4] = $oUC->get_codename_options($val);
		}
		unset($columns['userjyoutaikbn'][4][0]);
		unset($columns['userjyoutaikbn'][4][4]);
		$aSystem = $oUC->get_system_parameters();
		$aFeeKbn = $oUC->get_feekbn_options();

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('autoAssign', true);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('aSystem', $aSystem);
		$this->oSmarty->assign('input_control', "readonly style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('button_control', "disabled style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'erase');
		$this->oSmarty->assign('op', 'usr_02_02_erase');
		$this->oSmarty->display('usr_02_02.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		$msg = '';
		if ($req['userjyoutaikbn'] == '1') return '';

		if (trim($req['stoperasedate']) == '') {
			$msg.= '停止／抹消日を入力してください。<br>';
			$this->err['StopEraseDate'] = 'class="error"';
		} elseif (!preg_match("/^[0-9]{8}$/", $req['stoperasedate'])) {
			$msg.= '停止／抹消日は8桁の半角数字で入力してください。<br>';
			$this->err['StopEraseDate'] = 'class="error"';
		}
		if (empty($this->err['StopEraseDate'])) {
			if (checkdate(substr($req['stoperasedate'],4,2), substr($req['stoperasedate'],6,2), substr($req['stoperasedate'],0,4)) == false) {
				$msg.= '停止／抹消日が正しくありません。<br>';
				$this->err['StopEraseDate'] = 'class="error"';
			}
		}
		if (!empty($req['stopenddate'])) {
			if (!preg_match("/^[0-9]{8}$/", $req['stoperasedate'])) {
				$msg.= '利用停止解除日は8桁の半角数字で入力してください。<br>';
				$this->err['StopEndDate'] = 'class="error"';
			}
			if (empty($this->err['StopEndDate'])) {
				if (checkdate(substr($req['stopenddate'],4,2), substr($req['stopenddate'],6,2), substr($req['stopenddate'],0,4)) == false) {
					$msg.= '利用停止解除日が正しくありません。<br>';
					$this->err['StopEndDate'] = 'class="error"';
				}
			}
		}
		return $msg;
	}

	function update_user_status(&$req, $uid)
	{
		$dataset = array();
		$dataset['userjyoutaikbn'] = $req['userjyoutaikbn'];

		switch (intval($req['userjyoutaikbn'])) {
		// 通常
		case 1:
			$dataset['stoperasedate'] = NULL;
			$dataset['stopenddate'] = NULL;
			$dataset['stoperasejiyu'] = NULL;
			break;

		// 利用停止
		case 2:
			$dataset['stoperasedate'] = $req['stoperasedate'];
			$dataset['stopenddate'] = $req['stopenddate'] == '' ? NULL : $req['stopenddate'];
			$dataset['stoperasejiyu'] = $req['stoperasejiyu'] == '' ? NULL : $req['stoperasejiyu'];
			break;

		// 登録抹消
		default:
			$dataset['stoperasedate'] = $req['stoperasedate'];
			$dataset['stopenddate'] = NULL;
			$dataset['stoperasejiyu'] = $req['stoperasejiyu'] == '' ? NULL : $req['stoperasejiyu'];
			break;
		}

		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

		$rc = $this->oDB->update('m_user', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
