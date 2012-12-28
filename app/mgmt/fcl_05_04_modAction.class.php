<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  申込不可日設定
 *
 *  fcl_05_04_modAction.class.php
 *  fcl_05_04.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/time_schedule.class.php';

class fcl_05_04_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$para = array();

		$this->set_header_info();

		$scd = $_POST['scd'];
		$rcd = $_POST['rcd'];
		$type = $_POST['type'];
		$pyear = intval($_POST['selYear']);
		$pmonth = intval($_POST['selMonth']);
		$yoyakuKbn = isset($_POST['YoyakuKbn']) ? $_POST['YoyakuKbn'] : '03';

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['updateBtn'])) {
			if ($this->update_unavailableday($_POST, $scd, $rcd, $pmonth, $yoyakuKbn)) {
				$message = '正常に更新しました。';
			} else {
				$message = '更新できませんでした。';
			}
		}
		$aYoyakuKbn = $oFA->get_codename_options();
		unset($aYoyakuKbn['01']);
		unset($aYoyakuKbn['02']);

		$oTS = new time_schedule($this->con, _CITY_CODE_, $scd, $rcd);

		$firstDate = mktime(0, 0, 0, $pmonth, 1, $pyear);
		$lastDay = date('t', $firstDate);

		$recs = array();
		$j = date('w', $firstDate);
		for ($i = 0; $i < $j; ++$i)
		{
			$recs[$i]['day'] = 0;
			$recs[$i]['closed'] = 0;
			$recs[$i]['holiday'] = '';
		}
		for ($i = 1; $i <= $lastDay; ++$i)
		{
			$recs[$j]['day'] = $i;
			$pDate = $pyear.sprintf('%02d%02d', $pmonth, $i);
			$recs[$j]['closed'] = $oTS->KyukanHantei($pDate);
			$recs[$j]['holiday'] = $oTS->getHoliFlg($pDate);
			++$j;
		}
		if ($j%7 != 0) {
			for ($i = $j%7; $i < 7; ++$i)
			{
				$recs[$j]['day'] = 0;
				$recs[$j]['closed'] = 0;
				$recs[$j]['holiday'] = '';
				++$j;
			}
		}
		$para['year'] = $pyear;
		$para['month'] = $pmonth;
		$para['YoyakuKbn'] = intval($yoyakuKbn) + 100;

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('req', $_POST);
		$this->oSmarty->assign('aYoyakuKbn', $aYoyakuKbn);
		$this->oSmarty->assign('back_url', 'fcl_04_03_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_05_04_mod');
		$this->oSmarty->display('fcl_05_04.tpl');
	}

	function update_unavailableday(&$req, $scd, $rcd, $month, $kbn)
	{
		$sql = 'DELETE FROM m_unavailableday';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND SUBSTRING(closedday, 1, 2)=? AND yoyakukbn=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd, sprintf('%02d', $month), $kbn);
		$this->con->query($sql, $aWhere);

		if (!isset($req['Day'])) return true;

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['appdatefrom'] = date('Ymd');
		$dataset['yoyakukbn'] = $kbn;

		foreach ($req['Day'] as $key => $val)
		{
			$dataset['closedday'] = sprintf('%02d%02d', $month, $key);
			$rc = $this->oDB->insert('m_unavailableday', $dataset);
			if ($rc < 0) {
				return false;
			}
		}
		return true;
	}
}
?>
