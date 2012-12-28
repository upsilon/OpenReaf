<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  time_schedule.class.php
 */

class time_schedule
{
	protected $con = null;
	protected $lcd = '';
	protected $scd = '';
	protected $rcd = '';
	private $Crec = array();

	function __construct(&$con, $lcd, $scd, $rcd, $kyukanflg=true)
	{
		$this->con = $con;
		$this->lcd = $lcd;
		$this->scd = $scd;
		$this->rcd = $rcd;

		if ($kyukanflg) $this->set_closedday_data();
	}

	/*
	 * unixtime を hhmmss 形式に変換
	 */
	function hhmmss($t)
	{
		$h = intval($t/3600);
		$m = intval($t%3600/60);
		$s = intval($t%3600%60/60);
		return sprintf('%02d%02d%02d', $h, $m, $s);
	}

	/*
	 * タイムスケジュールパターン取得
	 *
	 * @param int $allkoma: 時間割データをそのまま使用するとき
	 *
	 * @return array : key コマ区分 val From(開始),To(終了)
	 */
	function get_time_schedule_ptn($UseDate, $allkoma = false)
	{
		$sql = "SELECT kaijoutime, heijoutime, komatanitime, komatanitimekbn, komakbn, komatimefrom, komatimeto, komaname, komaclass
			FROM m_stjtimetable
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			AND appdatefrom<=?
			AND (haishidate>? OR haishidate='' OR haishidate IS NULL)
			AND monthdayfrom<=? AND monthdayto>=?
			ORDER BY komakbn ";
		$aWhere = array($this->lcd, $this->scd, $this->rcd,
				$UseDate, $UseDate,
				substr($UseDate, 4, 4), substr($UseDate, 4, 4));
		$Koma = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (count($Koma) == 0) return array();

		$Srec = array();
		if (!$allkoma) {
			$sql = "SELECT * FROM m_stjuserrestime
				WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? 
				AND appdatefrom<=?
				AND (haishidate>? OR haishidate='' OR haishidate IS NULL)
				AND monthdayfrom<=? AND monthdayto>=?";
			$Srec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		}

		$useFrom = '0000';
		$useTo = '2400';

		if ($Srec) {
			$wnum = date('w', strtotime($UseDate));
			$UserResTimeKey = 'userrestime'.$wnum;

			if ($Srec['useholidayflg'] == '1') {
				$HoliFlg = $this->getHoliFlg($UseDate);
				if ($HoliFlg == '1') {
					$UserResTimeKey = 'userrestime7';
				}
			}
			if (!empty($Srec[$UserResTimeKey.'from'])) {
				$useFrom = $Srec[$UserResTimeKey.'from'];
			}
			if (!empty($Srec[$UserResTimeKey.'to'])) {
				$useTo = $Srec[$UserResTimeKey.'to'];
			}
		}
		unset($Srec);

		$recs = array();
		$count = 1;
		if ($Koma[0]['komaclass'] == '1') {
			$koma = $Koma[0];
			$kaijou = substr($koma['kaijoutime'], 0, 2)*3600 + substr($koma['kaijoutime'], 2, 2)*60;
			$heijou = substr($koma['heijoutime'], 0, 2)*3600 + substr($koma['heijoutime'], 2, 2)*60;
			$usefrom = substr($useFrom, 0, 2)*3600 + substr($useFrom, 2, 2)*60;
			$useto = substr($useTo, 0, 2)*3600 + substr($useTo, 2, 2)*60;

			$unit = ($koma['komatanitimekbn'] == '2') ? 60 : 3600;
			$unitTime = $koma['komatanitime'] * $unit;

			for ($t = $kaijou; $t < $heijou; $t += $unitTime)
			{
				if ($usefrom <= $t && $t+$unitTime <= $useto) {
					$keyStr = sprintf('%02d', $count);
					$recs[$keyStr] = array('KomaClass' => $koma['komaclass'],
								'KomaName' => $koma['komaname'],
								'From' => $this->hhmmss($t),
								'To' => $this->hhmmss($t+$unitTime));
				}
				++$count;
			}
		} else {
			foreach ($Koma as $koma)
			{
				if ($koma['komakbn'] == '00') continue;
				if ($useFrom.'00' <= $koma['komatimefrom'] && $koma['komatimeto'] <= $useTo.'00') {
					$keyStr = sprintf('%02d', $count);
					$recs[$keyStr] = array('KomaClass' => $koma['komaclass'],
								'KomaName' => $koma['komaname'],
								'From' => $koma['komatimefrom'],
								'To' => $koma['komatimeto']);
				}
				++$count;
			}
		}
		unset($Koma);
		return $recs;
	}

	function get_timetable_data($startDate, $endDate, &$aTimeKoma, &$aTimeMask)
	{
		$sql = "SELECT * FROM m_stjtimetable
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			AND appdatefrom<=?
			AND (haishidate>? OR haishidate='' OR haishidate IS NULL)
			ORDER BY appdatefrom, monthdayfrom, komakbn ";
		$aWhere = array($this->lcd, $this->scd, $this->rcd,
				$endDate, $startDate);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rows = array();
		foreach ($res as $val)
		{
			$rows[$val['appdatefrom']][$val['monthdayfrom']][$val['monthdayto']][] = $val;
		}
		foreach ($rows as $AppDateFrom => $val1)
		{
			foreach ($val1 as $MonthDayFrom => $val2)
			{
				foreach ($val2 as $MonthDayTo => $val3)
				{
					$aTimeKoma[] = array(	'AppDateFrom' => $AppDateFrom,
								'MonthDayFrom' => $MonthDayFrom,
								'MonthDayTo' => $MonthDayTo,
								'Koma' => $this->make_komatime($val3)
							);
				}
			}
		}
		unset($rows);

		$sql = "SELECT * FROM m_stjuserrestime
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? 
			AND appdatefrom<=?
			AND (haishidate>? OR haishidate='' OR haishidate IS NULL)
			ORDER BY appdatefrom, monthdayfrom";
		$aTimeMask = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function make_komatime(&$Koma)
	{
		$recs = array();
		$count = 1;
		if ($Koma[0]['komaclass'] == '1') {
			$koma = $Koma[0];
			$kaijou = substr($koma['kaijoutime'], 0, 2)*3600 + substr($koma['kaijoutime'], 2, 2)*60;
			$heijou = substr($koma['heijoutime'], 0, 2)*3600 + substr($koma['heijoutime'], 2, 2)*60;

			$unit = ($koma['komatanitimekbn'] == '2') ? 60 : 3600;
			$unitTime = $koma['komatanitime'] * $unit;

			for ($t = $kaijou; $t < $heijou; $t += $unitTime)
			{
				$keyStr = sprintf('%02d', $count);
				$recs[$keyStr] = array('KomaClass' => $koma['komaclass'],
							'KomaName' => $koma['komaname'],
							'From' => $this->hhmmss($t),
							'To' => $this->hhmmss($t+$unitTime));
				++$count;
			}
		} else {
			foreach ($Koma as $koma)
			{
				if ($koma['komakbn'] == '00') continue;
				$keyStr = sprintf('%02d', $count);
				$recs[$keyStr] = array('KomaClass' => $koma['komaclass'],
							'KomaName' => $koma['komaname'],
							'From' => $koma['komatimefrom'],
							'To' => $koma['komatimeto']);
				++$count;
			}
		}
		return $recs;
	}

	function make_timetable(&$aTimeKoma, &$aTimeMask, $UseDate, $allkoma = false)
	{
		$monthday = substr($UseDate, 4, 4);
		$komatime = array();
		foreach ($aTimeKoma as $val)
		{
			if ($val['AppDateFrom'] <= $UseDate
				&& $val['MonthDayFrom'] <= $monthday
				&& $val['MonthDayTo'] >= $monthday) {
				$komatime = $val['Koma'];
				break;
			}
		}
		if (empty($komatime)) return array();
		if ($allkoma) return $komatime;

		$timemask = array();
		foreach ($aTimeMask as $val)
		{
			if ($val['appdatefrom'] <= $UseDate
				&& $val['monthdayfrom'] <= $monthday
				&& $val['monthdayto'] >= $monthday) {
				$timemask = $val;
				break;
			}
		}
		if (!empty($timemask)) {
			$useFrom = '000000';
			$useTo = '240000';
			$wnum = date('w', strtotime($UseDate));
			$UserResTimeKey = 'userrestime'.$wnum;

			if ($timemask['useholidayflg'] == '1') {
				$HoliFlg = $this->getHoliFlg($UseDate);
				if ($HoliFlg == '1') {
					$UserResTimeKey = 'userrestime7';
				}
			}
			if (!empty($timemask[$UserResTimeKey.'from'])) {
				$useFrom = $timemask[$UserResTimeKey.'from'].'00';
			}
			if (!empty($timemask[$UserResTimeKey.'to'])) {
				$useTo = $timemask[$UserResTimeKey.'to'].'00';
			}
			foreach ($komatime as $komaKbn => $val)
			{
				if ($val['From'] < $useFrom || $useTo < $val['To']) {
					unset($komatime[$komaKbn]);
				}
			}
		}
		return $komatime;
	}

	/*
	 * 休館判定
	 *
	 * @return int 0:休館でない 2:休館 17:祝日休館 100以上:申込不可
	 */
	function KyukanHantei($UseDate)
	{
		// 申込不可日
		$rst = $this->getUnavailableDay($UseDate);
		if ($rst > 0) return $rst + 100;
	
		if (empty($this->Crec)) return 2;

		$Crec = $this->Crec;

		if ($Crec['exception_day'] != '') {
			$pdate = substr($UseDate, 4, 4);
			if (preg_match('/'.$pdate.'/', $Crec['exception_day'])) return 0;
		}
		$Crec['kyukan'] = false;

		// 定期休館
		$this->checkClosedDay($Crec, $UseDate);

		$ptime = strtotime($UseDate);
		$HoliFlg = $this->getHoliFlg($UseDate);

		// 祝日の場合
		if ($HoliFlg == '1') {
			$kyukan = $this->checkHoliday($Crec, $ptime);
			return $kyukan ? 17:0;
		} else {
			if ($Crec['kyukan']) return 2;
		}

		// 振替休日
		if ($Crec['closeddaychgflg'] == 1) {
			// 昨日
			$kyukan = $this->checkSubstituteHoliday($Crec, $ptime, 1);
			return $kyukan ? 2:0;
		}
		return 0;
	}

	/*
	 * 振替休日判定
	 *
	 * @param int $usedate:利用日（UnixTime）
	 * @param int $sub_day:遡る日数
	 *
	 * @return boolean 休館の場合はtrueを返す
	 */
	function checkSubstituteHoliday(&$Crec, $usedate, $sub_day)
	{
		$ptime = $usedate - $sub_day * 86400;
		$pdate = date('Ymd', $ptime);
		$HoliFlg = $this->getHoliFlg($pdate);
		$this->checkClosedDay($Crec, $pdate);

		if ($Crec['kyukan'] && $HoliFlg == '1') {
			return $this->checkHoliday($Crec, $ptime, 1);
		}
		return false;
	}

	/*
	 * 祝日判定
	 *
	 * @param array $Crec
	 * @param int $usedate:利用日（UnixTime）
	 * @param int $mode : 0:通常 1:振替休日チェック
	 *
	 * @return boolean
	 */
	function checkHoliday(&$Crec, $usedate, $mode = 0)
	{
		$day_num = date('w', $usedate);

		if ($Crec['holiclosedflg'] == 1) {
			// 祝日休館
			return true;
		} elseif ($Crec['holiclosedflg'] == 2 && $day_num != 0 && $day_num != 6) {
			// 土日以外なら休館
			return true;
		} elseif ($Crec['holiclosedflg'] == 3 && $day_num != 0) {
			// 日以外なら休館
			return true;
		} elseif ($Crec['holiclosedflg'] == 4) {
			// 休館設定に従う
			return $Crec['kyukan'];
		} elseif ($mode == 1) {
			return true;
		}
		// 祝日に休館しない
		return false;
	}

	/*
	 * 固定休館日判定
	 *
	 * $Crec['kyukan'] = trueなら休館
	 *
	 * @return array $Crec
	 */
	function checkClosedDay(&$Crec, $UseDate)
	{
		$tDate = getdate(strtotime($UseDate));
		$weekNo = ceil($tDate['mday'] / 7);
		$weekday = strtolower(substr($tDate['weekday'], 0, 3));
		$Crec['kyukan'] = true;

		// 休館日1
		if (($Crec['maishu1'] == 1 || $Crec['dai'.$weekNo.'shu1'] == 1) && $Crec[$weekday.'1'] == 1) return;

		// 休館日2
		if (($Crec['maishu2'] == 1 || $Crec['dai'.$weekNo.'shu2'] == 1) && $Crec[$weekday.'2'] == 1) return;

		// 休館日3
		if ($Crec['monthfirst3'] == 1 && $tDate['mday'] < 8 && $Crec[$weekday.'3'] == 1) return;
		$nextw = getdate($tDate[0] + 7 * 86400);
		if ($Crec['monthfainal3'] == 1 && $tDate['mon'] != $nextw['mon'] && $Crec[$weekday.'3'] == 1) return;

		// 固定休館日
		$day = substr($UseDate, 6, 2);
		for ($i = 1; $i <= 3; ++$i)
		{
			if ($Crec['koteiclosedday'.$i] == $day) return;
		}

		$Crec['kyukan'] = false;
	}

	function set_closedday_data()
	{
		$sql = 'SELECT * FROM m_closedday 
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?'; 
		$aWhere = array($this->lcd, $this->scd, $this->rcd);
		$this->Crec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function getHoliFlg($UseDate)
	{
		$sql = "SELECT holiflg FROM m_holiday 
			WHERE localgovcode=? AND heichouholiday=?";
		return $this->con->getOne($sql, array($this->lcd, $UseDate));
	}

	function getUnavailableDay($UseDate)
	{
		$pdate = substr($UseDate, 4, 4);
		$sql = "SELECT yoyakukbn FROM m_unavailableday 
			WHERE localgovcode=? AND shisetsucode=? 
			AND shitsujyocode=? AND closedday=?";
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $pdate);
		return intval($this->con->getOne($sql,$aWhere));
	}
}
?>
