<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面組合せ情報変更
 *
 *  fcl_05_09_01_modAction.class.php
 *  fcl_05_09.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_09_01_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $useflg_arr, $openflg_arr, $openkbn_arr;

		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$cno = $_REQUEST['cno'];
		$type = $_REQUEST['type'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['updateBtn'])) {

			if (!isset($_POST['openkbnval'][12])) $_POST['openkbnval'][12] = 0;
			if (!isset($_POST['openkbnval'][13])) $_POST['openkbnval'][13] = 0;
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$rc = $this->update_mencombination($_POST, $scd, $rcd, $cno);
				if ($rc) {
					$message = '組合せ情報を変更しました。';
					$success = 1;
				} else {
					$message.= '組合せ情報の変更ができませんでした。<br>';
					$success = -1;
				}
			} else {
				$success = -1;
			}
		}
		$res = $oFA->get_mencombination_data($scd, $rcd, $cno);
		$para = $oFA->make_mencombination_info($res);
		$para['openkbnval'] = explode(',', $para['openkbn']);
		unset($res);
		if ($success < 0) {
			$para['combiname'] = $_POST['combiname'];
			$para['combiskbno'] = $_POST['combiskbno'];
			$para['openflg'] = $_POST['openflg'];
			$para['openkbnval'] = $_POST['openkbnval'];
			$para['mokuteki'] = $_POST['mokuteki'];
		}

		$input_control = $type == 'ref' ? 'readonly' : '';

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('openkbn_arr', $openkbn_arr);
		$this->oSmarty->assign('month_arr', range(0, 11));
		$this->oSmarty->assign('back_url', 'fcl_04_09_summary');
		$this->oSmarty->assign('input_control', $input_control);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_05_09_01_mod');
		$this->oSmarty->display('fcl_05_09.tpl');
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if ($req['combiname'] == '') {
			$msg.= '組合せ名を指定してください。<br>';
		}
		return $msg;
	}

	function update_mencombination(&$req, $scd, $rcd, $cno)
	{
		$dataset = array();
		$dataset['combiskbno'] = intval($req['combiskbno']);
		$dataset['combiname'] = $req['combiname'];
		$dataset['openflg'] = intval($req['openflg']);
		$dataset['openkbn'] = '';
		$n = count($req['openkbnval']);
		for ($i = 0; $i < $n; ++$i)
		{
			if ($i > 0) $dataset['openkbn'].= ',';
			$dataset['openkbn'].= $req['openkbnval'][$i];
		}
		$dataset['openkbn_disable'] = $req['openkbn_disable'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd
			."' AND combino=".$cno;

		$rc = $this->oDB->update('m_mencombination', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
