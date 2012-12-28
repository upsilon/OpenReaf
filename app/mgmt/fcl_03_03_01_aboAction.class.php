<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場廃止／廃止取消
 *
 *  fcl_03_03_01_aboAction.class.php
 *  fcl_03_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/shitsujyo.class.php';

class fcl_03_03_01_aboAction extends adminAction
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
		$abosuccess = 0;

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];

		$oFA = new shitsujyo($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['expireBtn'])) {
			$message = $oFA->check_haishi_date($_POST);
			if ($message == '') {
				if ($this->expire_shitsujyo($_POST, $scd, $rcd)) {
					$message = '廃止しました。';
				} else {
					$message = '廃止できませんでした。';
					$success = -1;
				}
			}
		} elseif (isset($_POST['resumeBtn'])) {
			$_POST['HaishiDate'] = NULL;
			if ($this->expire_shitsujyo($_POST, $scd, $rcd)) {
				$message = '廃止取消しました。';
			} else {
				$message = '廃止取消できませんでした。';
				$success = -1;
			}
		}
		$para = $oFA->get_shitsujyo_data($scd, $rcd);
		if ($success < 0) {
			$para['haishidate'] = $_POST['HaishiDate'];
		} elseif ($para['haishidate'] != '') {
			$abosuccess = 1;
		}

		$this->oSmarty->assign('aboSuccess', $abosuccess);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oFA->get_error());
		$this->oSmarty->assign('back_url', 'fcl_02_02_list');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'abo');
		$this->oSmarty->assign('op', 'fcl_03_03_01_abo');
		$this->oSmarty->display('fcl_03_03.tpl');
	}

	function expire_shitsujyo(&$req, $scd, $rcd)
	{
		$dataset = array();
		$dataset['haishidate'] = $req['HaishiDate'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd."'";

		$rc = $this->oDB->update('m_shitsujyou', $dataset, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}
}
?>
