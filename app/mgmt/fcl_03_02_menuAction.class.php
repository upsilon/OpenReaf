<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場情報処理メニュー
 *
 *  fcl_03_02_menuAction.class.php
 *  fcl_03_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_03_02_menuAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $menu_arr;

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$type = $_REQUEST['type'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('menu_arr', $menu_arr[$type]);
		$this->oSmarty->display('fcl_03_02.tpl');
	}
}
?>
