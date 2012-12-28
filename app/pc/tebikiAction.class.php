<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  tebikiAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/tebiki.php';

class tebikiAction
{
	private $oSmarty = null;

	function __construct($type)
	{
		$this->oSmarty = new MySmarty($type);
	}

	function execute()
	{
		$this->oSmarty->assign('condition', OR_USAGE_GUIDE);
		$this->oSmarty->assign('message', OR_USAGE_FLOW);
		$this->oSmarty->assign('MODE', 3);
		$this->oSmarty->display('tebiki.tpl');
	}
}
?>
