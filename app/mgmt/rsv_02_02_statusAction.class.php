<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  空き状況表示
 *
 *  rsv_02_02_statusAction.class.php
 *  rsv_02_02_01.tpl
 *  rsv_02_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';

define('P_I', 'rsv_01_02');

class rsv_02_02_statusAction extends adminAction
{
	private $aDuration = array('1 day', '1 week', '2 week', '1 month', '2 month', '3 month');
	private $status_mark = array('○', '×', '△', '', '-');
	private $oSC = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$message = '';

		$ShisetsuCode = '';
		$ShitsujyoCode = '';
		$rsv_purpose = array();
		$date_list = array();
		$time_area_flag = array(1, 1, 1);
		$time_area_count = 3;
		$date_from = 0;
		$date_to = 0;
		$date_step = '';

		$this->set_header_info();

		if (isset($_REQUEST['forward'])) {
			$date_from = mktime(0, 0, 0, $_SESSION[P_I]['FromMonth'], $_SESSION[P_I]['FromDay'], $_SESSION[P_I]['FromYear']); 
			$date_step = $this->aDuration[$_SESSION[P_I]['Duration']];
			$date_from = strtotime('+'.$date_step, $date_from);
			$_SESSION[P_I]['FromYear'] = date('Y', $date_from);
			$_SESSION[P_I]['FromMonth'] = date('m', $date_from);
			$_SESSION[P_I]['FromDay'] = date('j', $date_from);

		} elseif (isset($_REQUEST['previous'])) {
			$date_from = mktime(0, 0, 0, $_SESSION[P_I]['FromMonth'], $_SESSION[P_I]['FromDay'], $_SESSION[P_I]['FromYear']); 
			$date_step = $this->aDuration[$_SESSION[P_I]['Duration']];
			$date_from = strtotime('-'.$date_step, $date_from);
			$_SESSION[P_I]['FromYear'] = date('Y', $date_from);
			$_SESSION[P_I]['FromMonth'] = date('m', $date_from);
			$_SESSION[P_I]['FromDay'] = date('j', $date_from);
		} elseif (isset($_POST['searchMode'])) {
			$_SESSION[P_I] = $_POST;
		}
		$date_from = mktime(0, 0, 0, $_SESSION[P_I]['FromMonth'], $_SESSION[P_I]['FromDay'], $_SESSION[P_I]['FromYear']); 
		$date_step = $this->aDuration[$_SESSION[P_I]['Duration']];
		$date_to = strtotime('+'.$date_step, $date_from);

		if (!_ROOM_STATUS_ALL_DAY_) {
			if (isset($_SESSION[P_I]['TimeArea'])) {
				$time_area_flag = array(0, 0, 0);
				$time_area_count = 0;
				foreach ($_SESSION[P_I]['TimeArea'] as $val)
				{
					$time_area_flag[$val] = 1;
					++$time_area_count;
				}
			}
		}

		$dow_flag = array(0, 0, 0, 0, 0, 0, 0);
		if (isset($_SESSION[P_I]['DayOfWeek'])) {
			foreach ($_SESSION[P_I]['DayOfWeek'] as $val)
			{
				$dow_flag[$val] = 1;
			}
		}

		if (isset($_SESSION[P_I]['chkGenre'])) {
			foreach ($_SESSION[P_I]['chkGenre'] as $val)
			{
				$rsv_purpose[] = $val;
			}
		}

		$date_list = $this->get_date_array($date_from, $date_to, $dow_flag);
		$ShisetsuCode = $_SESSION[P_I]['ShisetsuCode']; 
		$ShitsujyoCode = $_SESSION[P_I]['ShitsujyoCode']; 

		$aPurpose = array();
		$res1 = array();
		if (!empty($rsv_purpose)) {
			$aPurpose = $this->get_shisetsu_by_purpose($rsv_purpose);
		}
		if ($ShisetsuCode == '') {
			$res1 = $this->oPrivilege->get_shisetsu_list();
			$this->get_allowed_shisetsu($aPurpose, $res1);
		} else {
			$sql = 'SELECT shisetsucode, shisetsuname, shisetsuskbcode FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=? ORDER BY shisetsuskbcode, shisetsucode';
			$row = $this->con->getRow($sql, array(_CITY_CODE_, $ShisetsuCode), DB_FETCHMODE_ASSOC);
			$res1[$row['shisetsucode']] = $row['shisetsuname'];
		}
		$recs = array();
		foreach ($res1 as $key => $val)
		{
			$recs[$key] = array('ShisetsuName' => $val,
						'shitsujyo' => array());
		}

		foreach ($res1 as $key1 => $val1)
		{
			$res2 = array();
			$sql = "SELECT localgovcode, shisetsucode, shitsujyocode, shitsujyoname, teiin, shitsujyoskbcode";
			$sql.= " FROM m_shitsujyou WHERE localgovcode=?";
			$sql.= " AND shitsujyokbn<'3'";
			if ($ShitsujyoCode == '') {
				$priSql = $this->oPrivilege->getStaffShitsujyoSql('', $key1);
				$aWhere = array_merge(array(_CITY_CODE_), $priSql[1]);
				$sql.= ' AND '.$priSql[0];
				if (!empty($rsv_purpose)) {
					$pShitsujyo = $this->get_allowed_shitsujyo($aPurpose, $key1);
					$sql.= " AND (shitsujyocode='".implode("' OR shitsujyocode='", $pShitsujyo)."')";
				}
				$sql.= ' ORDER BY shisetsucode, shitsujyoskbcode, shitsujyocode';
				$res2 = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
			} else {
				$sql.= " AND shisetsucode=? AND shitsujyocode=? ORDER BY shisetsucode, shitsujyoskbcode, shitsujyocode";
				$res2 = $this->con->getAll($sql, array(_CITY_CODE_, $key1, $ShitsujyoCode), DB_FETCHMODE_ASSOC);
			}
			foreach ($res2 as $val2)
			{
				$recs[$val2['shisetsucode']]['shitsujyo'][$val2['shitsujyocode']] = $val2;
				$recs[$val2['shisetsucode']]['shitsujyo'][$val2['shitsujyocode']]['combi'] = array(); 
			}
			unset($res2);
		}
		unset($res1);

		$sql1 = 'SELECT c.localgovcode, c.shisetsucode, c.shitsujyocode, m.mencode, c.combino, c.combiname, m.teiin, c.combiskbno';
		$sql1.= ' FROM m_mencombination c';
		$sql1.= ' JOIN m_men m';
		$sql1.= ' USING(localgovcode, shisetsucode, shitsujyocode, mencode)';
		$sql1.= ' WHERE c.localgovcode=? AND c.shisetsucode=? AND c.shitsujyocode=?';
		$sql1.= ' ORDER BY combiskbno, combino, mencode';
		foreach ($recs as $key1 => $val1)
		{
			foreach ($val1['shitsujyo'] as $key2 => $val2)
			{
				$res3 = $this->con->getAll($sql1, array(_CITY_CODE_, $key1, $key2), DB_FETCHMODE_ASSOC);
				if (count($res3) == 0)
				{
					$row = array(
							'localgovcode' => _CITY_CODE_,
							'shisetsucode' => $val2['shisetsucode'],
							'shitsujyocode' => $val2['shitsujyocode'],
							'mencode' => 'ZZ',
							'combino' => 0,
							'combiname' => '',
							'teiin' => $val2['teiin']
						);
					$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']] = $row;
					$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['reserve_band'] = array();
					$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['mark'] = array();
					unset($res3);
					continue;
				}
				if (empty($rsv_purpose)) {
					$LastCombiNo = -1;
					foreach ($res3 as $row)
					{
						if ($row['combino'] == $LastCombiNo) {
							$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['mencode'] .= '-'.$row['mencode'];
							$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['teiin'] += $row['teiin'];
							continue;
						}
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']] = $row;
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['reserve_band'] = array();
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['mark'] = array();
						$LastCombiNo = $row['combino'];
					}
					unset($res3);
					continue;
				}
				$sql2 = 'SELECT COUNT(*) FROM m_stjpurpose';
				$sql2.= ' WHERE localgovcode=? AND shisetsucode=?';
				$sql2.= ' AND shitsujyocode=? AND (combino=? OR combino=0)';
				$sql2.= " AND (mokutekicode='".implode("' OR mokutekicode='", $rsv_purpose)."')";
				$LastCombiNo = -1;
				foreach ($res3 as $row)
				{
					$aWhere = array(_CITY_CODE_, $row['shisetsucode'], $row['shitsujyocode'], $row['combino']);
					$num = $this->con->getOne($sql2, $aWhere);
					if ($num > 0) {
						if ($row['combino'] == $LastCombiNo) {
							$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['mencode'] .= '-'.$row['mencode'];
							$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['teiin'] += $row['teiin'];
							continue;
						}
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']] = $row;
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['reserve_band'] = array();
						$recs[$row['shisetsucode']]['shitsujyo'][$row['shitsujyocode']]['combi'][$row['combino']]['mark'] = array();
						$LastCombiNo = $row['combino'];
					}
				}
				unset($res3);
			}
		}
		$this->set_reserve_status($recs, $date_list, $date_from, $date_to);

		$MokutekiName = '';
		if (!empty($rsv_purpose)) {
			$aMokuteki = $this->oSC->get_purpose_name_array();
			foreach ($rsv_purpose as $val)
			{
				$MokutekiName .= ' '.$aMokuteki[$val];
			}
		}

		$template = 'rsv_02_02_01.tpl';
		if ($_SESSION[P_I]['searchMode'] == 2) {
			$template = 'rsv_02_02_02.tpl';
		}
		if (isset($_SESSION[P_I]['openerWindowMode'])) {
			$this->oSmarty->assign('openerWindowMode', $_SESSION[P_I]['openerWindowMode']);
			$this->oSmarty->assign('isOpenerWindowFlg', $_SESSION[P_I]['isOpenerWindowFlg']);
		}

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('MokutekiName', $MokutekiName);
		$this->oSmarty->assign('reserve_band_active', $time_area_flag);
		$this->oSmarty->assign('reserve_band_active_count', $time_area_count);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('date_list', $date_list);
		$this->oSmarty->display($template);
	}

	function get_date_array($date_from, $date_to, $dow_flag)
	{
		global $aWeekJ;

		$aDay = array();

		$aHolidays = $this->get_holidays();
		$i = 0;
		for ($i = $date_from; $i < $date_to; $i += 86400)
		{
			$w = date('w', $i);
			if ($dow_flag[$w] == 1) {
				$tday = array();
				$tday['date'] = $i;
				$tday['dateLink'] = date('Ymd', $i);
				$tday['dateView'] = date('n/j', $i).(_ROOM_STATUS_ALL_DAY_?'<br>':'').'('.$aWeekJ[$w].')';
				$tday['HolidayFlg'] = 0;
				if (isset($aHolidays[date('Ymd', $i)])) {
					$tday['HolidayFlg'] = 1;
				}
				$aDay[] = $tday;
			}
		}
		return $aDay;
	}

	function get_holidays()
	{
		$sql = "SELECT heichouholiday FROM m_holiday";
		$sql.= " WHERE holiflg='1' ORDER BY heichouholiday";
		$res = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val['heichouholiday']] = 1;
		}
		unset($res);
		return $recs;
	}

	function get_shisetsu_by_purpose($purpose)
	{
		$sql = 'SELECT shisetsucode, shitsujyocode, combino';
		$sql.= ' FROM m_stjpurpose WHERE localgovcode=?';
		$sql.= " AND (mokutekicode='".implode("' OR mokutekicode='", $purpose)."')";
		return $this->con->getAll($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);
	}

	function get_allowed_shisetsu(&$aPurpose, &$pShisetsu)
	{
		if (empty($aPurpose)) return;

		$recs = array();	
		foreach ($aPurpose as $val)
		{
			if (!in_array($val['shisetsucode'], $recs)
				&& array_key_exists($val['shisetsucode'], $pShisetsu)) {
				$recs[$val['shisetsucode']] = $pShisetsu[$val['shisetsucode']];
			}
		}
		$pShisetsu = $recs;
		unset($recs);
	}

	function get_allowed_shitsujyo(&$aPurpose, $ShisetsuCode)
	{
		$recs = array();
		foreach ($aPurpose as $val)
		{
			if ($ShisetsuCode == $val['shisetsucode']) {
				if (!in_array($val['shitsujyocode'], $recs)) {
					$recs[] = $val['shitsujyocode'];	
				}
			}
		}
		return $recs;
	}

	function set_reserve_status(&$recs, &$date_list, $date_from, $date_to)
	{
		$aSys = $this->oSC->get_system_parameters();

		foreach ($recs as $key1 => $val1)
		{
			foreach ($val1['shitsujyo'] as $key2 => $val2)
			{
				foreach ($val2['combi'] as $key3 => $val3)
				{
					$menCode = explode('-', $val3['mencode']);
					$oRES = new reserve_status($this->con, _CITY_CODE_, $key1, $key2, $menCode, false);
					$aTimeKoma = array();
					$aTimeMask = array();
					$dateFrom = date('Ymd', $date_from);
					$dateTo = date('Ymd', $date_to);
					$oRES->get_timetable_data($dateFrom, $dateTo, $aTimeKoma, $aTimeMask);
					$reserve_band = &$recs[$key1]['shitsujyo'][$key2]['combi'][$key3]['reserve_band'];
					$mark = &$recs[$key1]['shitsujyo'][$key2]['combi'][$key3]['mark'];
					foreach ($date_list as $i => $day)
					{
						$reserve_band[$i] = array(0, 0, 0, 'all' => 0);
						$UseDate = $day['dateLink'];
						$ptn = $oRES->make_timetable($aTimeKoma, $aTimeMask, $UseDate, _PRIVILEGE_TIME_);
						$oRES->get_reserved_user($ptn, $UseDate);
						$aAPNStatus = array('am' => 0, 'pm' => 0, 'nt' => 0, 'all' => 0);
						$aAPN = array('am' => 0, 'pm' => 0, 'nt' => 0, 'all' => 0);
						$aAki = array('am' => 0, 'pm' => 0, 'nt' => 0, 'all' => 0);
						$kyukan = false;
						foreach ($ptn as $koma)
						{
							$kyukan = $koma['reserved'] == 2 ? true : false;
							$komaFrom = substr($koma['From'], 0, 4);
							if ($aSys['amfrom'] <= $komaFrom && $komaFrom <= $aSys['amto']) {
								if ($koma['reserved'] != 0) ++$aAki['am'];
								++$aAPN['am'];
							}
							if ($aSys['pmfrom'] <= $komaFrom && $komaFrom <= $aSys['pmto']) {
								if ($koma['reserved'] != 0) ++$aAki['pm'];
								++$aAPN['pm'];
							}
							if ($aSys['ntfrom'] <= $komaFrom && $komaFrom <= $aSys['ntto']) {
								if ($koma['reserved'] != 0) ++$aAki['nt'];
								++$aAPN['nt'];
							}
							if ($koma['reserved'] != 0) ++$aAki['all'];
							++$aAPN['all'];
						}	
						foreach ($aAPN as $key => $val)
						{
							if ($aAki[$key] == 0) {
								$aAPNStatus[$key] = 0;
							} elseif ($aAPN[$key] == $aAki[$key]) {
								$aAPNStatus[$key] = 1;
							} else {
								$aAPNStatus[$key] = 2;
							}
						}
						$reserve_band[$i][0] = $aAPNStatus['am'];
						$reserve_band[$i][1] = $aAPNStatus['pm'];
						$reserve_band[$i][2] = $aAPNStatus['nt'];
						$reserve_band[$i]['all'] = $aAPNStatus['all'];

						if ($kyukan) {
							$reserve_band[$i][0] = 3;
							$reserve_band[$i][1] = 3;
							$reserve_band[$i][2] = 3;
							$reserve_band[$i]['all'] = 3;
							$this->status_mark[3] = mb_substr($koma['Mark'], 0, 1, 'UTF-8');
						}

						if ($aAPN['am'] == 0) $reserve_band[$i][0] = 4;
						if ($aAPN['pm'] == 0) $reserve_band[$i][1] = 4;
						if ($aAPN['nt'] == 0) $reserve_band[$i][2] = 4;
						if ($aAPN['all'] == 0) $reserve_band[$i]['all'] = 4;

						$mark[$i][0] = $this->status_mark[$reserve_band[$i][0]];
						$mark[$i][1] = $this->status_mark[$reserve_band[$i][1]];
						$mark[$i][2] = $this->status_mark[$reserve_band[$i][2]];
						$mark[$i]['all'] = $this->status_mark[$reserve_band[$i]['all']];
					}
					unset($aTimeKoma);
					unset($aTimeMask);
				}
			}
		}
	}
}
?>
