<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  Smarty.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/smarty/Smarty.class.php';

class MySmarty extends Smarty
{
	function __construct($type='mgmt')
	{
		parent::__construct();

		$this->setTemplateDir(OPENREAF_ROOT_PATH.'/app/templates/'.$type.'/');
		$this->setCompileDir(OPENREAF_ROOT_PATH.'/var/templates_c/'.$type.'/');
		$this->error_reporting = E_ALL & ~E_NOTICE;
	}
}
?>
