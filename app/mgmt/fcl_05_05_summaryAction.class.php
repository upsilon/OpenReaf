<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金設定期間一覧
 *
 *  fcl_05_05_summaryAction.class.php
 *  fcl_05_05.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_05_summaryAction extends adminAction
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
		$type = $_REQUEST['type'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$rec['MenName'] = '';
		if ($cno != 0) {
			$rec['MenName'] = $oFA->get_mencombination_name($scd, $rcd, $cno);
		}

		$sql = 'SELECT DISTINCT appdatefrom, monthdayfrom, monthdayto, haishidate';
		$sql.= ' FROM m_stjfee';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=?';
		$sql.= " AND shitsujyocode=? AND combino=? AND timefrom='min'";
		$sql.= ' ORDER BY appdatefrom, monthdayfrom, monthdayto';
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('today', date('Ymd'));
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('nowDate', date('Ymd'));
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->display('fcl_05_05.tpl');
	}
}
?>
