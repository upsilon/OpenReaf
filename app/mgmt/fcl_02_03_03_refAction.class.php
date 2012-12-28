<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設分類情報参照
 *
 *  fcl_02_03_03_refAction.class.php
 *  fcl_02_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_03_03_refAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $delflg_arr, $limitflg_arr, $pulloutmonlimitkbn_arr;

		$message = '';

		$this->set_header_info();

		$oFA = new facility($this->con);

		$para = $oFA->get_shisetsuclass_data($_GET['ccd']);

		$this->oSmarty->assign('req', $para);

		$this->oSmarty->assign('delflg_arr', $delflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'ref');
		$this->oSmarty->assign('op', 'fcl_02_03_03_ref');
		$this->oSmarty->display('fcl_02_03.tpl');
	}
}
?>
