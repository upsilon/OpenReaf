<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金設定曜日一覧
 *
 *  fcl_06_02_summaryAction.class.php
 *  fcl_06_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class fcl_06_02_summaryAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$cno = $_REQUEST['cno'];
		$apd = $_REQUEST['apd'];
		$prfr = $_REQUEST['prfr'];
		$prto = $_REQUEST['prto'];
		$type = $_REQUEST['type'];

		$oSC = new system_common($this->con);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$rec['MenName'] = '';
		if ($cno != 0) {
			$rec['MenName'] = $oFA->get_mencombination_name($scd, $rcd, $cno);
		}

		$sql = 'SELECT * FROM m_stjfee';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=?';
		$sql.= ' AND shitsujyocode=? AND combino=?';
		$sql.= ' AND appdatefrom=?';
		$sql.= ' AND monthdayfrom=? AND monthdayto=?';
		$sql.= " AND timefrom='min' ORDER BY tourokuno";
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno, $apd, $prfr, $prto);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$aStaff = $oSC->get_staff_name_array();

		foreach ($res as $key => $val)
		{
			$res[$key]['UpdTime'] = $oSC->getTimeView($val['updtime']);
			$res[$key]['UpdDate'] = $oSC->getDateView($val['upddate'], false);
			$res[$key]['UpdName'] = isset($aStaff[$val['updid']]) ? $aStaff[$val['updid']] : $val['updid'];
		}

		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->display('fcl_06_02.tpl');
	}
}
?>
