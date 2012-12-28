<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設情報参照
 *
 *  fcl_02_01_03_refAction.class.php
 *  fcl_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_01_03_refAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $openflg_arr, $dispflg_arr, $useflg_arr, $fractionflg_arr,
			$limitflg_arr, $shisetsukbn_arr, $pulloutmonlimitkbn_arr;

		$message = '';

		$this->set_header_info();

		$oFA = new facility($this->con);

		$para = $oFA->get_shisetsu_data($_GET['scd']);

		$aBusho = $this->oPrivilege->get_busho_options();
		$aShisetsuClass = $oFA->get_shisetsuclass_options();

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('aShisetsuClass', $aShisetsuClass);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('fractionflg_arr', $fractionflg_arr);
		$this->oSmarty->assign('shisetsukbn_arr', $shisetsukbn_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('button_control', 'disabled');
		$this->oSmarty->assign('err', array());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'ref');
		$this->oSmarty->assign('op', 'fcl_02_01_03_ref');
		$this->oSmarty->display('fcl_02_01.tpl');
	}
}
?>
