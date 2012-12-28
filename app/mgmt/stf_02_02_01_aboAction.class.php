<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員廃止／廃止取消
 *
 *  stf_02_02_01_aboAction.class.php
 *  stf_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/staff.class.php';

class stf_02_02_01_aboAction extends adminAction
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
		$abosuccess = 0;

		$this->set_header_info();

		$sid = $_REQUEST['staffid'];

		$oST = new staff($this->con);

		$aBusho = $this->oPrivilege->get_busho_options();

		if (isset($_POST['expireBtn'])) {
			$message = $oST->check_haishi_date($_POST);
			if ($message == '') {
				if ($this->expire_staff($_POST, $sid)) {
					$message = '廃止しました。';
				} else {
					$message = '廃止できませんでした。';
					$success = -1;
				}
			} else {
				$success = -1;
			}
		} elseif (isset($_POST['resumeBtn'])) {
			$_POST['HaishiDate'] = NULL;
			if ($this->expire_staff($_POST, $sid)) {
				$message = '廃止取消しました。';
			} else {
				$message = '廃止取消できませんでした。';
				$success = -1;
			}
		}
		$para = $oST->get_staff_data($sid);
		if ($success < 0) {
			$para['haishidate'] = $_POST['HaishiDate'];
		} elseif ($para['haishidate'] != '') {
			$abosuccess = 1;
		}

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oST->get_error());
		$this->oSmarty->assign('back_url', 'fcl_02_02_list');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'stf_02_02_01_abo');
		$this->oSmarty->display('stf_02_02.tpl');
	}

	function expire_staff(&$req, $sid)
	{
		$dataset = array();
		$dataset['haishidate'] = $req['HaishiDate'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND staffid='".$sid."'";

		$rc = $this->oDB->update('m_staff', $dataset, $where);
		if ($rc < 0) {
			return false;
		}

		if (_PRIVILEGE_LDAP_FLAG_) {
			$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
			if ($ds) {
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				$sr = ldap_search($ds, _PRIVILEGE_LDAP_ROOTDN_, 'cn='.$req['staffid']);
				$curinfo = ldap_get_entries($ds, $sr);

				if ($curinfo['count'] == 1) {
					$info = array();
					if (empty($req['HaishiDate'])) {
						$info['HaishiDate'] = array();
						$info['ShadowFlag'] = array();
					} else {
						$info['HaishiDate'] = $req['HaishiDate'];
						$info['ShadowFlag'] = '1';
					}
					ldap_modify($ds, $curinfo[0]['dn'], $info);
				}
			}
		}
		return true;
	}
}
?>
