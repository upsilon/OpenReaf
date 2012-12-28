<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員情報照会
 *
 *  stf_02_01_03_refAction.class.php
 *  stf_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/staff.class.php';

class stf_02_01_03_refAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$sid = $_REQUEST['staffid'];

		$oST = new staff($this->con);

		$aBusho = $this->oPrivilege->get_busho_options();
		$aShisetsu = $oST->get_shisetsu_options();
		$aShitsujyo = $oST->get_shitsujyo_options($aShisetsu);

		$para = $oST->get_staff_data($sid);
		$para['pwd2'] = $para['pwd'];
		$para['userfacilities'] = array();
		$facilities = $oST->get_facilities_code($sid);
		if ($facilities) {
			$aShisetsuAll = $oST->get_shisetsu_options(true);
			$para['userfacilities'] = $oST->make_facility_list($facilities, $aShisetsuAll);
		}

		switch($_SESSION['usertype'])
		{
		    case 1:
			$this->oSmarty->assign('SystemMOn',0);
			$this->oSmarty->assign('ShisetsuMOn',0);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		    case 2:
			$this->oSmarty->assign('SystemMOn',0);
			$this->oSmarty->assign('ShisetsuMOn',1);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		    case 3:
			$this->oSmarty->assign('SystemMOn',1);
			$this->oSmarty->assign('ShisetsuMOn',1);
			$this->oSmarty->assign('ShisetsuCOn',1);
			break;
		}     
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('aShitsujyo', $aShitsujyo);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'ref');
		$this->oSmarty->assign('op', 'stf_02_01_03_ref');
		$this->oSmarty->display('stf_02_01.tpl');
	}
}
?>
