<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面組合せ登録
 *
 *  fcl_05_08_regAction.class.php
 *  fcl_05_08.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_08_regAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $useflg_arr, $openflg_arr, $openkbn_arr;

		$message = '';
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$aMen = $this->get_men_options($scd, $rcd);

		if (isset($_POST['insertBtn'])) {

			if (!isset($_POST['openkbnval'][12])) $_POST['openkbnval'][12] = 0;
			if (!isset($_POST['openkbnval'][13])) $_POST['openkbnval'][13] = 0;
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				if ($_POST['combiname'] == '') {
					$pName = '';
					foreach($_POST['mencode'] as $val)
					{
						if ($pName != '') $pName .= '+';
						$pName .= $aMen[$val];
					}
					$_POST['combiname'] = $pName;
				}
				$rc = $this->insert_mencombination($_POST, $scd, $rcd);
				if ($rc) {
					$message = '組合せ情報を登録しました。';
				} else {
					$message.= '組合せ情報の登録ができませんでした。<br>';
				}
			}
			$para = $_POST;
		}
		$lines = ceil(count($aMen) / 2);
		if ($lines > 0) $aMen = array_chunk($aMen, $lines, true);

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('openkbn_arr', $openkbn_arr);
		$this->oSmarty->assign('month_arr', range(0, 11));
		$this->oSmarty->assign('aMen', $aMen);
		$this->oSmarty->assign('back_url', 'fcl_04_09_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'reg');
		$this->oSmarty->assign('op', 'fcl_05_08_reg');
		$this->oSmarty->display('fcl_05_08.tpl');
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if (!isset($req['mencode'])) {
			$msg.= '利用単位を選択してください。<br>';
		}
		if (empty($req['combino'])) {
			$msg.= '組合せ番号を指定してください。(1以上)<br>';
		}
		elseif ($this->is_exist_combino($req)) {
			$msg.= '組合せ番号が既に使われています。<br>';
		}
		return $msg;
	}

	function insert_mencombination(&$req, $scd, $rcd)
	{
		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['combino'] = intval($req['combino']);
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
		$dataset['updid'] = $_SESSION['userid'];

		foreach ($req['mencode'] as $val)
		{
			$dataset['mencode'] = $val;
			$dataset['updtime'] = date('His');
			$rc = $this->oDB->insert('m_mencombination', $dataset);
			if ($rc < 0) {
				return false;
			}
		}
		return true;
	}

	function get_men_options ($scd, $rcd)
	{
		$sql = 'SELECT mencode, menname FROM m_men';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$sql.= ' ORDER BY mencode';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$res = $this->con->getAll($sql, $aWhere , DB_FETCHMODE_ASSOC);

		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val['mencode']] = $val['menname'];
		}
		unset($res);
		return $recs;
	}

	function is_exist_combino($dataset)
	{
		$sql = "SELECT COUNT(combino) FROM m_mencombination
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?";
		$aWhere = array(_CITY_CODE_, $dataset['scd'],
				$dataset['rcd'], $dataset['combino']);
		$res = $this->con->getOne($sql, $aWhere);

		return ($res == 0) ? false : true;
	}
}
?>
