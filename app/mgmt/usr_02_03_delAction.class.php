<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者情報削除
 *
 *  usr_02_03_delAction.class.php
 *  usr_02_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/user.class.php';

class usr_02_03_delAction extends adminAction
{
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

		$para = $oUC->get_user_columns($columns);
		$para['UpdStaffName'] = $oUC->get_staff_name($para['updid']);
		$para['UpdDateView'] = $oUC->getDateView($para['upddate']);
		$para['UpdTimeView'] = $oUC->getTimeView($para['updtime']);
		foreach ($optionItems as $val)
		{
			$columns[strtolower($val)][4] = $oUC->get_codename_options($val);
		}
		$aSystem = $oUC->get_system_parameters();
		$aFeeKbn = $oUC->get_feekbn_options();

		if ($this->check_using($uid)) {
			$success = 1;
			$message = '予約・抽選・収納のトランザクションデータに当該利用者IDが存在するため、削除できません。';
		}
		if (isset($_POST['deleteBtn'])) {
			if ($this->delete_user($uid)) {
				$message = '削除しました。';
				$success = 1;
			} else {
				$message = '削除できませんでした。';
			}
		}

		$this->oSmarty->assign('col', $columns);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('autoAssign', true);
		$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
		$this->oSmarty->assign('aSystem', $aSystem);
		$this->oSmarty->assign('input_control', "readonly style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('button_control', "disabled style='background-color:#FFFFCC;font-color:#000000;'");
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'usr_02_03_del');
		$this->oSmarty->display('usr_02_03.tpl');
	}

	function delete_user($uid)
	{
		$aWhere = array(_CITY_CODE_, $uid);
		$where = ' WHERE localgovcode=? AND userid=?';

		$sql = 'DELETE FROM m_usrgenmen'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			return false;
		}
		$sql = 'DELETE FROM m_user'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function check_using($uid)
	{
		$aWhere = array(_CITY_CODE_, $uid);
		$where = ' WHERE localgovcode=? AND userid=?';

		$sql = 'SELECT COUNT(yoyakunum) FROM t_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(yoyakunum) FROM h_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(pulloutyoyakunum) FROM t_pulloutyoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(pulloutyoyakunum) FROM h_pullout'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(yoyakunum) FROM t_yoyakufeeuketsuke'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		return false;
	}
}
?>
