<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割一覧
 *
 *  fcl_04_09_summaryAction.class.php
 *  fcl_04_09.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_09_summaryAction extends adminAction
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
		$res = $oFA->get_mencombination_data($scd, $rcd);
		$recs = $oFA->make_mencombination_list($res);
		unset($res);

		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $recs);
		$this->oSmarty->display('fcl_04_09.tpl');
	}
}
?>
