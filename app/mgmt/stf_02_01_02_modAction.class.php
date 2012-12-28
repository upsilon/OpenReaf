<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員情報変更
 *
 *  stf_02_01_02_modAction.class.php
 *  stf_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/staff.class.php';

class stf_02_01_02_modAction extends adminAction
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
		$aShisetsuAll = $oST->get_shisetsu_options(true);
		$aShisetsu = $oST->get_shisetsu_options();
		$aShitsujyo = $oST->get_shitsujyo_options($aShisetsu);

		if (isset($_POST['updateBtn'])) {

			$message = $oST->check_input_data($_POST);
			if ($message == '') {
				if ($this->update_staff($_POST)) {
					$message = '変更しました。';
					$success = 1;
				} else {
					$message = '変更できませんでした。';
					$success = -1;
				}
			} else {
				$success = -1;
			}
		}
		if ($success < 0) {
			$para = $_POST;
			if (isset($para['userfacilities'])) {
				$para['userfacilities'] = $oST->make_facility_list($para['userfacilities'], $aShisetsuAll);
			}
		} else {
			$para = $oST->get_staff_data($sid);
			$para['pwd2'] = $para['pwd'];
			$para['userfacilities'] = array();
			$facilities = $oST->get_facilities_code($sid);
			if ($facilities) {
				$para['userfacilities'] = $oST->make_facility_list($facilities, $aShisetsuAll);
			}
		}

		switch($_SESSION['usertype'])
		{
		    case 1:
			$this->oSmarty->assign('SystemMOn',0);
			$this->oSmarty->assign('ShisetsuMOn',0);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		    case 2:
			$this->oSmarty->assign('SystemMOn',0);
			$this->oSmarty->assign('ShisetsuMOn',1);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		    case 3:
			$this->oSmarty->assign('SystemMOn',1);
			$this->oSmarty->assign('ShisetsuMOn',1);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		}     
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('aShitsujyo', $aShitsujyo);
		$this->oSmarty->assign('err', $oST->get_error());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'mod');
		$this->oSmarty->assign('op', 'stf_02_01_02_mod');
		$this->oSmarty->display('stf_02_01.tpl');
	}

	function update_staff(&$req)
	{
		$dataset = $this->oDB->make_base_dataset($req, 'm_staff');
		$dataset['kengencode1'] = isset($req['kengencode1']) ? $req['kengencode1'] : '00';
		$dataset['kengencode2'] = isset($req['kengencode2']) ? $req['kengencode2'] : '00';
		$dataset['kengencode3'] = isset($req['kengencode3']) ? $req['kengencode3'] : '00';
		$dataset['kengencode4'] = isset($req['kengencode4']) ? $req['kengencode4'] : '00';
		$dataset['kengencode5'] = isset($req['kengencode5']) ? $req['kengencode5'] : '00';
		$dataset['kengencode6'] = isset($req['kengencode6']) ? $req['kengencode6'] : '00';
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		unset($dataset['staffid']);
		$where = "localgovcode='"._CITY_CODE_."' AND staffid='".$req['staffid']."'";
		$rc = $this->oDB->update('m_staff', $dataset, $where);
		if ($rc < 0) {
			return false;
		}

		$sql = "DELETE FROM m_staffshisetsu WHERE localgovcode=? AND staffid=?";
		$aWhere = array(_CITY_CODE_, $req['staffid']);
		$this->con->query($sql, $aWhere);

		if (!isset($req['userfacilities'])) return true;

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['staffid'] = $req['staffid'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updid'] = $_SESSION['userid'];

		foreach ($req['userfacilities'] as $val)
		{
			$code = explode(':', $val);
			$dataset['shisetsucode'] = $code[0];
			$dataset['shitsujyocode'] = isset($code[1]) ? $code[1] : '';
			$dataset['updtime'] = date('His');
			$rc = $this->oDB->insert('m_staffshisetsu', $dataset);
			if ($rc < 0) {
				return false;
			}
		}

		if (_PRIVILEGE_LDAP_FLAG_) {
			$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
			if ($ds) {
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				$sr = ldap_search($ds, 'ou='.$req['BushoCode'].','._PRIVILEGE_LDAP_ROOTDN_, 'cn='.$req['StaffID']);
				$curinfo = ldap_get_entries($ds, $sr);

				ldap_delete($ds, $curinfo[0]['dn']);

				$info = array();
				$info['cn'] = $req['staffid'];
				$info['sn'] = $req['staffid'];
				$info['uid'] = $req['staffid'];
				$info['uidNumber'] = 5000+intval($req['staffid']);
				$info['gidNumber'] = 5000+intval($req['staffid']);
				$info['AppDateFrom'] = $req['appdatefrom'];
				$info['homeDirectory'] = '/home/'.$req['staffid'];

				$info['userPassword'] = '{MD5}'.base64_encode(pack("H*",md5($req['pwd']))); 

				$info['StaffName'] = $req['staffname'];
				$info['StaffNum'] = $req['staffnum'];
				$info['TourokuKbn'] = $req['tourokukbn'];
				$info['KengenCode1'] = isset($req['kengencode1']) ? $req['kengencode1'] : '00';
				$info['KengenCode2'] = isset($req['kengencode2']) ? $req['kengencode2'] : '00';
				$info['KengenCode3'] = isset($req['kengencode3']) ? $req['kengencode3'] : '00';
				$info['KengenCode4'] = isset($req['kengencode4']) ? $req['kengencode4'] : '00';
				$info['KengenCode5'] = isset($req['kengencode5']) ? $req['kengencode5'] : '00';
				$info['KengenCode6'] = isset($req['kengencode6']) ? $req['kengencode6'] : '00';
				if ($curinfo[0]['HaishiDate'][0] != '') {
					$info['HaishiDate'] = trim($curinfo[0]['HaishiDate'][0]);
				}
				if ($curinfo[0]['HaishiFlg'][0] != '') {
					$info['ShadowFlag'] = trim($curinfo[0]['HaishiFlg'][0]);
				}
				$info['objectclass'][] = "organizationalPerson";
				$info['objectclass'][] = "posixAccount";
				$info['objectclass'][] = "ReservePersonExt";
				$info['objectclass'][] = "shadowAccount";
				ldap_add($ds, $curinfo[0]['dn'], $info);
			}
		}
		return true;
	}
}
?>
