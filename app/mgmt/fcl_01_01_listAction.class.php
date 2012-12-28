<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設一覧
 *
 *  fcl_01_01_listAction.class.php
 *  fcl_01_01.tpl
 */

class fcl_01_01_listAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$sql = 'SELECT * FROM m_shisetsu WHERE localgovcode=? ORDER BY shisetsucode';
		$aWhere = array(_CITY_CODE_);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		foreach($res as $key => $val)
		{
			if($val['haishidate'] && $val['haishidate'] <= date('Ymd')) {
				$res[$key]['Haishi'] = 1;
			} else {
				$res[$key]['Haishi'] = 0;
			}
		}

		$this->oSmarty->assign('res', $res);
		$this->oSmarty->display('fcl_01_01.tpl');
	}
}
?>
