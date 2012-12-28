<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  helpAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/info.php';

class helpAction
{
	private $oSmarty = null;

	function __construct($type)
	{
		$this->oSmarty = new MySmarty($type);
	}

	function execute()
	{
		$this->oSmarty->assign('condition', OR_HELP_FOR_SYMBOLS);
		$this->oSmarty->assign('message', OR_MEANING_OF_SYMBOLS);
		$this->oSmarty->display('help.tpl');
	}
}
?>
