<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設分類一覧
 *
 *  fcl_01_03_listAction.class.php
 *  fcl_01_03.tpl
 */

class fcl_01_03_listAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$sql = 'SELECT * FROM m_shisetsuclass WHERE localgovcode=?';
		$sql.= ' ORDER BY shisetsuclasscode ASC';
		$aWhere = array(_CITY_CODE_);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('res', $res);
		$this->oSmarty->display('fcl_01_03.tpl');
	}
}
?>
