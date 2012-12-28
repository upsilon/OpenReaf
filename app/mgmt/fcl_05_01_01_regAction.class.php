<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割登録
 *
 *  fcl_05_01_01_regAction.class.php
 *  fcl_05_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/facility_timetable.class.php';

class fcl_05_01_01_regAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $komatanitimekbn_arr;

		$mode = 'reg';
		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$oTT = new facility_timetable($this->oDB, $_REQUEST);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		if (isset($_POST['insertBtn'])) {
			$message = $oTT->check_input_data($_POST, $mode);
			if ($message == '') {
				if ($oTT->insert_stj_timetable($_POST)) {
					$message = '正常に登録しました。';
					$success = 1;
				} else {
					$message = '登録できませんでした。';
				}
			}
			$para = $_POST;
		} elseif (isset($_POST['komaname_add_btn_hd'])) {
			$para = $_POST;
			$para['komaname'][] = '';
			$para['komanametimefrom'][] = '';
			$para['komanametimeto'][] = '';
		} else {
			for ($i = 0; $i < _MAX_KOMA_; ++$i)
			{
				$para['komakbn'][$i] = sprintf('%02d',$i);
				$para['komakbntimefrom'][$i] = '';
				$para['komakbntimeto'][$i] = '';
			}
			$para['komaname'][] = '';
			$para['komanametimefrom'][] = '';
			$para['komanametimeto'][] = '';
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('err', $oTT->get_error());
		$this->oSmarty->assign('komatanitimekbn_arr', $komatanitimekbn_arr);
		$this->oSmarty->assign('back_url', 'fcl_04_01_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', $mode);
		$this->oSmarty->assign('op', 'fcl_05_01_01_reg');
		$this->oSmarty->display('fcl_05_01.tpl');
	}
}
?>
