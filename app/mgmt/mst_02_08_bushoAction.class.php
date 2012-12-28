<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  部署コード登録・変更・削除
 *
 *  mst_02_08_bushoAction.class.php
 *  mst_02_08.tpl
 */

class mst_02_08_bushoAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$stMsg = '';
		$ds = false;

		if (isset($_POST['saveBtn'])) {
			if (_PRIVILEGE_LDAP_FLAG_) {
				$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
				if ($ds) {
					ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
					ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
				}
			}

			if (isset($_POST['Code'])) {
				foreach ($_POST['Code'] as $key => $val)
				{
					if (!$this->is_exists($val,
							$_POST['CodeName'][$key],
							$_POST['ShortName'][$key]))
					{
						$dataset = array(
								'bushoname' => $_POST['CodeName'][$key],
								'bushoshortname' => $_POST['ShortName'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND bushocode='".$val."'";
						$this->con->autoExecute('m_busho', $dataset, DB_AUTOQUERY_UPDATE, $a_where);

						if (_PRIVILEGE_LDAP_FLAG_ && $ds) {
							$info = array();
							$info['BushoName'] = $_POST['CodeName'][$key];
							$info['BushoShortName'] = $_POST['ShortName'][$key];
							ldap_modify($ds, 'ou='.$val.','._PRIVILEGE_LDAP_ROOTDN_, $info);
						}
					}
				}
			}

			if (!empty($_POST['code'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'bushocode' => $_POST['code'],
							'bushoname' => $_POST['codename'],
							'bushoshortname' => $_POST['shortname'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_busho', $dataset, DB_AUTOQUERY_INSERT);

					if (_PRIVILEGE_LDAP_FLAG_ && $ds) {
						$info = array();
						$info['ou'] = $_POST['code'];
						$info['BushoName'] = $_POST['codename'];
						$info['BushoShortName'] = $_POST['shortname'];
						$info['objectclass'][] = 'organizationalUnit';
						$info['objectclass'][] = 'ReserveUnitExt';
						ldap_add($ds, 'ou='.$_POST['code'].','._PRIVILEGE_LDAP_ROOTDN_, $info);
					}
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				if (_PRIVILEGE_LDAP_FLAG_) {
					$ds = ldap_connect(_PRIVILEGE_LDAP_IP_);
					if ($ds) {
						ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
						ldap_bind($ds, _PRIVILEGE_LDAP_ROOTDN_, _PRIVILEGE_LDAP_PSW_);
					}
				}
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_busho WHERE localgovcode=? AND bushocode=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);

					if (_PRIVILEGE_LDAP_FLAG_ && $ds) {
						$sr = ldap_search($ds, 'ou='.$_POST['Code'][$key].','._PRIVILEGE_LDAP_ROOTDN_, 'cn=*');
						$info = ldap_get_entries($ds, $sr);

						for ($i = 0; $i < $info['count']; ++$i)
						{
							ldap_delete($ds, $info[$i]['dn']);
						}
						$sr = ldap_search($ds, _PRIVILEGE_LDAP_ROOTDN_, 'ou='.$_POST['Code'][$key]);
						$info = ldap_get_entries($ds, $sr);
						ldap_delete($ds, $info[0]['dn']);
					}
				}
			}
		}

		$sql = "SELECT * FROM m_busho ORDER BY bushocode";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_08.tpl');
	}

	function is_exists($p_code, $p_name, $p_short)
	{
		$sql = "SELECT COUNT(*) FROM m_busho WHERE localgovcode=? AND bushocode=? AND bushoname=? AND bushoshortname=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name, $p_short);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[a-zA-Z0-9]{2,8}$/', $dataset['code'])) {
			$msg = '部署コードは8桁以下の半角英数字で入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['code'])) {
			$msg = '部署コードが重複しています。<br>';
		}
		if (trim($dataset['codename']) == '') {
			$msg = '部署名称を入力してください。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_busho WHERE localgovcode=? AND bushocode=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
