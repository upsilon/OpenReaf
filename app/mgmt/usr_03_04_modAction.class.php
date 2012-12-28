<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用目的変更
 *
 *  usr_03_04_modAction.class.php
 *  usr_03_04.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class usr_03_04_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';

		$this->set_header_info();

		$uid = $_REQUEST['UserID'];

		$oSC = new system_common($this->con);

		if (isset($_POST['updateBtn'])) {
			if ($this->update_usrpurpose($_POST, $uid)) {
				$message = '正常に登録しました。';
			} else {
				$message = '登録できませんでした。';
			}
		}
		$para = $oSC->get_user_data($uid);
		$user_list = $this->get_user_list($uid);
		$sports_list = $this->get_purpose_list('01');
		$culture_list = $this->get_purpose_list('02');
		foreach ($sports_list as $key => $val)
		{
			$sports_list[$key]['checked'] = '';
			if (isset($user_list[$val['mokutekicode']])) {
				$sports_list[$key]['checked'] = 'checked';
			}
		}
		foreach ($culture_list as $key => $val)
		{
			$culture_list[$key]['checked'] = '';
			if (isset($user_list[$val['mokutekicode']])) {
				$culture_list[$key]['checked'] = 'checked';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('sports_list', $sports_list);
		$this->oSmarty->assign('culture_list', $culture_list);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_04_mod');
		$this->oSmarty->display('usr_03_04.tpl');
	}

	function get_user_list($uid)
	{
		$sql = 'SELECT purpose FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		$res = $this->con->getOne($sql, $aWhere);

		if ($res == '') return array();

		$rows = explode(',', $res);
		$recs = array();
		foreach ($rows as $val) $recs[$val] = 1;
		unset($rows);
		return $recs;
	}

	function get_purpose_list($code)
	{
		$sql = "SELECT mokutekicode, mokutekiname, mokutekiskbcode
			FROM m_mokuteki
			WHERE mokutekidaicode=? AND mokutekicode<>'00'
			ORDER BY mokutekiskbcode, mokutekicode";

		return $this->con->getAll($sql, array($code), DB_FETCHMODE_ASSOC);
	}

	function update_usrpurpose(&$req, $uid)
	{
		$aCode = array();
		if (isset($req['MokutekiCode_sports'])) {
			foreach ($req['MokutekiCode_sports'] as $val)
			{
				$aCode[] = $val;
			}
		}
		if (isset($req['MokutekiCode_culture'])) {
			foreach ($req['MokutekiCode_culture'] as $val)
			{
				$aCode[] = $val;
			}
		}

		$dataset = array();
		$dataset['purpose'] = implode(',', $aCode);
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

		$rc = $this->oDB->update('m_user', $dataset, $where);
		if ($rc < 0) return false;
		return true;
	}
}
?>
