<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  申込不可日設定メニュー
 *
 *  fcl_04_03_menuAction.class.php
 *  fcl_04_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_03_menuAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$start_time = mktime(0,0,0,date('n')-1,1,date('Y'));
		$start_month = date('n', $start_time);
		$start_year = date('Y', $start_time);

		$res = array();
		for ($i = 0; $i < 12; ++$i)
		{
			$value = mktime(0, 0, 0, $start_month+$i, 1, $start_year);
			$pyear = date('Y', $value);
			$pmonth = date('n', $value);
			$res[$pmonth] = array(
				'monthName'=>$pyear."年".$pmonth."月",
				'year' => $pyear,
				'month' => $pmonth
				);
		}
		ksort($res);

		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->display('fcl_04_03.tpl');
	}
}
?>
