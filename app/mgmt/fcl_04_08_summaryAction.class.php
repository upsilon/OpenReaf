<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用単位一覧
 *
 *  fcl_04_08_summaryAction.class.php
 *  fcl_04_08.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_08_summaryAction extends adminAction
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

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$res = $oFA->get_men_data_list($scd, $rcd);

		$stj_using = $this->check_using($scd, $rcd);

		$this->oSmarty->assign('stj_using', $stj_using);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->display('fcl_04_08.tpl');
	}

	function check_using($scd, $rcd)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd, 0);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';

		$sql = 'SELECT COUNT(yoyakunum) FROM t_yoyakufeeshinsei'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(updid) FROM m_stjfee'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		return false;
	}
}
?>
