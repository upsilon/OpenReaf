<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  faqAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/tebiki.php';

class faqAction
{
	private $oSmarty = null;

	function __construct($type)
	{
		$this->oSmarty = new MySmarty($type);
	}

	function execute()
	{
		$this->oSmarty->assign('condition', OR_FAQ);
		$this->oSmarty->assign('MODE', 3);
		if (_TermClass_ == 'Kiosk') {
			$this->oSmarty->assign('BACK_LINK', '?op=tebiki');
		}
		$this->oSmarty->display('faq.tpl');
	}
}
?>
