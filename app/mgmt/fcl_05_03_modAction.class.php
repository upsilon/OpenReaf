<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  定期休館日設定
 *
 *  fcl_05_03_modAction.class.php
 *  fcl_05_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_03_modAction extends adminAction
{
	private $holiclosedflg_arr = array('休館しない', '休館する', '土・日を除き休館', '日を除き休館', '休館日設定に従う');
	private $closeddaychgflg_arr = array('振替しない', '振替する(祝祭日の翌平日)');

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['updateBtn'])) {
			$message = $this->check_input_data($_POST, $scd, $rcd);
			if ($message == '') {
				if ($this->update_closedday($_POST, $scd, $rcd)) {
					$message = '正常に更新しました。';
				} else {
					$message = '更新できませんでした。';
				}
			}
			$para = $_POST;
		} else {
			$sql = 'SELECT * FROM m_closedday';
			$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
			$aWhere = array(_CITY_CODE_, $scd, $rcd);
			$para = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
			$para['exception'] = array_fill(0, 8, '');
			$exception = explode(',', $para['exception_day']);
			foreach ($exception as $key => $val) {
				$para['exception'][$key] = $val;
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('holiclosedflg_arr', $this->holiclosedflg_arr);
		$this->oSmarty->assign('closeddaychgflg_arr', $this->closeddaychgflg_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_03_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_05_03_mod');
		$this->oSmarty->display('fcl_05_03.tpl');
	}

	function check_input_data(&$req, $scd , $rcd)
	{
		$msg = '';

		for ($i = 1; $i <= 3; ++$i)
		{
			$key = 'koteiclosedday'.$i;
			if ($req[$key] != '') {
				if (!preg_match("/^[0-9]{2}$/", $req[$key])) {
					$msg .= '月内定期休館日は数字2桁で指定してください : '.$req[$key].' \n';
				}
			}
			
		}
		foreach ($req['exception'] as $val)
		{
			if ($val != '') {
				if (!preg_match("/^[0-9]{4}$/", $val)) {
					$msg .= '休館除外日は数字4桁で指定してください : '.$val.' \n';
				}
			}
		}
		return $msg;
	}

	function update_closedday(&$req, $scd, $rcd)
	{
		$sql = 'DELETE FROM m_closedday';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$this->con->query($sql, $aWhere);

		$dataset = $this->oDB->make_base_dataset($req, 'm_closedday');
		$dataset['exception_day'] = '';
		foreach ($req['exception'] as $val) {
			if ($val != '') {
				if ($dataset['exception_day'] != '') $dataset['exception_day'] .= ',';
				$dataset['exception_day'] .= $val;
			}
		}
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['appdatefrom'] = date('Ymd');
		$dataset['monthdayfrom'] = '0101';
		$dataset['monthdayto'] = '1231';
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('m_closedday', $dataset);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
