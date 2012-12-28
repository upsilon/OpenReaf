<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  空き状況検索
 *
 *  rsv_01_02_searchAction.class.php
 *  rsv_01_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

define('P_I', 'rsv_01_02');

class rsv_01_02_searchAction extends adminAction
{
	private $aDuration = array('1日', '1週間', '2週間', '1ヶ月', '2ヶ月', '3ヶ月');
	private $aTimeArea = array('午前', '午後', '夜間');

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $aWeekJ;

		$message = '';
		$p = array();

		$this->set_header_info();

		$oSC = new system_common($this->con);

		if (isset($_GET['back'])) {
			$p = $_SESSION[P_I];
		} elseif (empty($_POST) || isset($_POST['clearBtn'])) {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
			$p['TimeArea'] = array(0, 1, 2);
			$p['DayOfWeek'] = array(0, 1, 2, 3, 4, 5, 6);
		} else {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
			$p = $_POST;
		}

		$aShisetsu = $this->oPrivilege->get_shisetsu_list();
		if (!isset($p['ShisetsuCode'])) {
			$scd = $this->oPrivilege->getDefaultShisetsuCode();
			$p['ShisetsuCode'] = $scd == '' ? key($aShisetsu) : $scd;
		}
		$aShitsujyo = $this->oPrivilege->get_shitsujyo_list($p['ShisetsuCode']);

		$searchMode = isset($p['searchMode']) ? $p['searchMode'] : 0;
		if ($searchMode == 0) {
			$p['ShitsujyoCode'] = '';
		}

		$dateFrom = isset($p['FromYear']) ? mktime(0, 0, 0, intval($p['FromMonth']), intval($p['FromDay']), intval($p['FromYear'])) : time();

		$aMokuteki01 = $this->get_purpose_options('01');
		$aMokuteki02 = $this->get_purpose_options('02');

		$this->oSmarty->assign('p', $p);
		$this->oSmarty->assign('dateFrom', $dateFrom);
		$this->oSmarty->assign('aDuration', $this->aDuration);
		$this->oSmarty->assign('aTimeArea', $this->aTimeArea);
		$this->oSmarty->assign('aWeekJ', $aWeekJ);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('aShitsujyo', $aShitsujyo);
		$this->oSmarty->assign('aMokuteki01', $aMokuteki01);
		$this->oSmarty->assign('aMokuteki02', $aMokuteki02);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->display('rsv_01_02.tpl');
	}

	function get_purpose_options($DaiCode)
	{
		$priSql =  $this->oPrivilege->getStaffShitsujyoSql('p');
		if (empty($priSql)) return array();

		$aWhere = array_merge(array(_CITY_CODE_, $DaiCode), $priSql[1]);
		$sql = "SELECT DISTINCT m.mokutekicode, m.mokutekiname";
		$sql.= " FROM m_stjpurpose p";
		$sql.= " JOIN m_mokuteki m USING (mokutekicode, localgovcode)";
		$sql.= " WHERE m.localgovcode=? AND m.mokutekidaicode=?";
		$sql.= " AND ".$priSql[0]." ORDER BY mokutekicode";
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}
}
?>
