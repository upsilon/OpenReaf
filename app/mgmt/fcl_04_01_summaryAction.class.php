<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約時間割一覧
 *
 *  fcl_04_01_summaryAction.class.php
 *  fcl_04_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class fcl_04_01_summaryAction extends adminAction
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
		$type = $_REQUEST['type'];

		$oSC = new system_common($this->con);

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$sql = 'SELECT DISTINCT appdatefrom, haishidate,
			monthdayfrom, monthdayto, komaclass,
			upddate, updtime, updid
			FROM m_stjtimetable
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			ORDER BY appdatefrom, monthdayfrom';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
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
		$this->oSmarty->assign('nowDate', date('Ymd'));
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->display('fcl_04_01.tpl');
	}
}
?>
