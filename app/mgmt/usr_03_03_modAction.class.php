<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設利用権限変更
 *
 *  usr_03_03_modAction.class.php
 *  usr_03_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class usr_03_03_modAction extends adminAction
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
			if ($this->update_usrshisetsu($_POST, $uid)) {
				$message = '正常に登録しました。';
			} else {
				$message = '登録できませんでした。';
			}
		}
		$para = $oSC->get_user_data($uid);
		$user_list = $this->get_user_list($uid);
		$sports_list = $this->get_shisetsu_list('01');
		$culture_list = $this->get_shisetsu_list('02');
		foreach ($sports_list as $key => $val)
		{
			$sports_list[$key]['checked'] = '';
			if (isset($user_list[$val['shisetsucode']])) {
				$sports_list[$key]['checked'] = 'checked';
			}
		}
		foreach ($culture_list as $key => $val)
		{
			$culture_list[$key]['checked'] = '';
			if (isset($user_list[$val['shisetsucode']])) {
				$culture_list[$key]['checked'] = 'checked';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('sports_list', $sports_list);
		$this->oSmarty->assign('culture_list', $culture_list);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_03_mod');
		$this->oSmarty->display('usr_03_03.tpl');
	}

	function get_user_list($uid)
	{
		$sql = 'SELECT shisetsu FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		$res = $this->con->getOne($sql, $aWhere);

		if ($res == '') return array();

		$rows = explode(',', $res);
		$recs = array();
		foreach ($rows as $val) $recs[$val] = 1;
		unset($rows);
		return $recs;
	}

	function get_shisetsu_list($code)
	{
		$sql = "SELECT shisetsucode, shisetsuname, shisetsuskbcode
			FROM m_shisetsu
			WHERE shisetsuclassdaicode=?
			AND (haishidate>? OR haishidate IS NULL OR haishidate='')
			ORDER BY shisetsuskbcode, shisetsucode";

		return $this->con->getAll($sql, array($code, date('Ymd')), DB_FETCHMODE_ASSOC);
	}

	function update_usrshisetsu(&$req, $uid)
	{
		$aCode = array();
		if (isset($req['ShisetsuCode_sports'])) {
			foreach ($req['ShisetsuCode_sports'] as $val)
			{
				$aCode[] = $val;
			}
		}
		if (isset($req['ShisetsuCode_culture'])) {
			foreach ($req['ShisetsuCode_culture'] as $val)
			{
				$aCode[] = $val;
			}
		}

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsu'] = implode(',', $aCode);
		$dataset['userid'] = $uid;
		$dataset['upddate'] = date('Ymd');
		$dataset['updid'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_."' AND userid='".$uid."'";

		$rc = $this->oDB->update('m_user', $dataset, $where);
		if ($rc < 0) return false;
		return true;
	}
}
?>
