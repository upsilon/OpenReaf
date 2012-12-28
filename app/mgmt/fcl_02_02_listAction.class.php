<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場一覧
 *
 *  fcl_02_02_listAction.class.php
 *  fcl_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_02_listAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$oFA = new facility($this->con);

		$rec = $oFA->get_shisetsu_data($_REQUEST['scd']);

		$sql = 'SELECT * FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? ORDER BY shitsujyocode';
		$aWhere = array(_CITY_CODE_, $_REQUEST['scd']);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		foreach($res as $key => $val)
		{
			$res[$key]['Haishi'] = 0;
			if($val['haishidate'] && $val['haishidate'] <= date('Ymd')) {
				$res[$key]['Haishi'] = 1;
			}
			$res[$key]['openflg_view'] = '非公開';
			if ($val['openflg'] == '1') {
				$res[$key]['openflg_view'] = '公開';
			}
		}

		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('aShitsujyoKbn', $GLOBALS['shitsujyokbn_arr']);
		$this->oSmarty->display('fcl_02_02.tpl');
	}
}
?>
