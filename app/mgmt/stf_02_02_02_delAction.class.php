<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員削除
 *
 *  stf_02_02_02_delAction.class.php
 *  stf_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/staff.class.php';

class stf_02_02_02_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$sid = $_REQUEST['staffid'];

		$oST = new staff($this->con);

		$aBusho = $this->oPrivilege->get_busho_options();
		$para = $oST->get_staff_data($sid);

		if ($this->check_using($sid)) {
			$success = 1;
			$message = '予約・抽選・収納・還付のトランザクションデータおよび利用者データに当該職員IDが存在するため、削除できません。';
		}
		if (isset($_POST['deleteBtn'])) {
			if ($this->delete_staff($sid)) {
				$message = '削除しました。';
				$success = 1;
			} else {
				$message = '削除できませんでした。';
			}
		}

		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('back_url', 'fcl_02_02_list');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'stf_02_02_02_del');
		$this->oSmarty->display('stf_02_02.tpl');
	}

	function delete_staff($sid)
	{
		$aWhere = array(_CITY_CODE_, $sid);
		$where = ' WHERE localgovcode=? AND staffid=?';

		$sql = 'DELETE FROM m_staffshisetsu'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			return false;
		}
		$sql = 'DELETE FROM m_staff'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			return false;
		}

		if (_PRIVILEGE_LDAP_FLAG_) {
			$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			if ($ds) {
				ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				$sr = ldap_search($ds, _PRIVILEGE_LDAP_ROOTDN_, 'cn='.$req['StaffID']);
				$info = ldap_get_entries($ds, $sr);

				if ($info['count'] == 1) {
					ldap_delete($ds, $info[0]['dn']);
				}
			}
		}
		return true;
	}

	function check_using($uid)
	{
		$aWhere = array(_CITY_CODE_, $uid, $uid, $uid);
		$where = ' WHERE localgovcode=? AND (updid=? OR daikouid=? OR lstupdid=?)';
		$sql = 'SELECT COUNT(updid) FROM h_pullout'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$aWhere = array(_CITY_CODE_, $uid, $uid, $uid, $uid);
		$where = ' WHERE localgovcode=? AND (updid=? OR daikouid=? OR cancelstaffid=? OR lstupdid=?)';
		$sql = 'SELECT COUNT(updid) FROM h_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$aWhere = array(_CITY_CODE_, $uid, $uid);
		$where = ' WHERE localgovcode=? AND (updid=? OR daikouid=?)';
		$sql = 'SELECT COUNT(updid) FROM t_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$where = ' WHERE localgovcode=? AND (updid=? OR daikouid=?)';
		$sql = 'SELECT COUNT(updid) FROM t_pulloutyoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$where = ' WHERE localgovcode=? AND (updid=? OR receptid=?)';
		$sql = 'SELECT COUNT(updid) FROM t_yoyakufeeuketsuke'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$where = ' WHERE localgovcode=? AND (receiptstaffid=? OR cancelstaffid=?)';
		$sql = 'SELECT COUNT(receiptstaffid) FROM t_yoyakukanpujyutou'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$where = ' WHERE localgovcode=? AND (staffid=? OR updid=?)';
		$sql = 'SELECT COUNT(updid) FROM t_potalmemo'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(updid) FROM t_staffbbs'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$aWhere = array(_CITY_CODE_, $uid);
		$where = ' WHERE localgovcode=? AND updid=?';
		$sql = 'SELECT COUNT(updid) FROM t_yoyakufeeshinsei'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(updid) FROM m_user'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		return false;
	}
}
?>
