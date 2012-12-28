<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  システムコード
 *
 *  mst_01_01_topAction.class.php
 *  mst_01_01.tpl
 */

class mst_01_01_topAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$this->oSmarty->display('mst_01_01.tpl');
	}
}
?>
