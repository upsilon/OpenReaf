<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  fee_calc.php
 */
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';

class fee_calc extends fee_base
{
	private $MonthDay = '';
	private $FeeKbn = '';

	//
	// コンストラクタ
	//
	// object con
	// array src
	// member	localgovcode
	//		shisetsucode
	//		shitsujyocode
	//		combino
	//		Fuzoku
	//		usedate
	//		UTFrom
	//		UTTo
	//		FeeKbn
	//		genapplyflg
	//		fractionflg
	//		userid
	//
	function __construct(&$con, &$src)
	{
		parent::__construct($con, $src);

		$this->MonthDay = substr($src['usedate'], 4, 4);
		$this->FeeKbn = isset($src['FeeKbn']) ? $src['FeeKbn'] : '';
	}

	//
	// 利用者減免率取得
	//
	function get_user_gen()
	{
		$sql = "SELECT g.rate, u.koteigencode 
			FROM m_usrgenmen u
			JOIN m_genmen g USING (localgovcode, koteigencode)
			WHERE u.localgovcode=? AND u.userid=?
			AND (u.appday<=? OR u.keizokuflg='1') AND u.limitday>=?";
		$aWhere = array($this->src['localgovcode'], $this->src['userid'], $this->src['usedate'], $this->src['usedate']);
		$row = $this->con->getRow($sql,$aWhere, DB_FETCHMODE_ASSOC);
		if ($row) {
			$rate = intval($row['rate']);
			return array('Rate'=>$rate,'KoteiGenCode'=>$row['koteigencode']);
		}
		return false;
	}

	function get_GenApplyFlg($rcd)
	{
		$sql = 'SELECT genapplyflg, genmen, extracharge FROM m_shitsujyou';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->src['localgovcode'], $this->src['shisetsucode'], $rcd);
		return $this->con->getRow($sql, $aWhere);
	}

	//
	// 料金検索条件
	//
	function getFeeCondition($wday)
	{
		$sql = "localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=? ";
		$sql .= " AND appdatefrom<=? AND (haishidate>? OR haishidate='' OR haishidate IS NULL)";
		$sql .= " AND monthdayfrom<=? AND monthdayto>=? ";
		$sql .= " AND " . $wday . " = 1 ";
		return $sql;
	}

	function extract_fee(&$res, $FeeKbn)
	{
		$fee = $res['fee01'];
		if (!empty($FeeKbn)) {
			foreach($res as $key => $val)
			{
				if (preg_match("/^feekbn/", $key)) {
					if ($val == $FeeKbn) {
						$fieldName = 'fee'.substr($key, 6, 2);
						$fee = $res[$fieldName];
						break;
					}
				}
			}
		}
		return floor($fee);
	}

	//
	// コマあたりの料金
	//
	function getFeePerKoma($mode, $rcd, $cno, $wday, $UseTimeFrom, $UseTimeTo)
	{
		$sql = "SELECT * FROM m_stjfee WHERE ";
		$sql.= $this->getFeeCondition($wday);

		$aWhere = array($this->src['localgovcode'],
				$this->src['shisetsucode'], $rcd, $cno,
				$this->src['usedate'], $this->src['usedate'],
				$this->MonthDay, $this->MonthDay); 

		switch ($mode) {
		case 1: // コマ毎の値段の場合
			$sql .= " AND timefrom<=? AND timeto>=? ";
			$sql .= " AND feetourokukbn='1'";
			array_push($aWhere, $UseTimeFrom, $UseTimeTo);
			break;
		case 3: // 時間がまたがった場合の料金
			$sql .= " AND timefrom=? AND timeto=? ";
			$sql .= " AND feetourokukbn='3'";
			array_push($aWhere, $UseTimeFrom, $UseTimeTo);
			break;
		default: // コマが一律の値段の場合
			$sql .= " AND timefrom='flat' AND (timeto IS NULL OR timeto='')";
			$sql .= " AND feetourokukbn='2'";
			break;
		}

		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (count($rec) == 0) return 0;

		return $this->extract_fee($rec, $this->FeeKbn);
	}

	function getFeeByMultiKoma($rcd, $cno, $wday)
	{
		$timecode = array_keys($this->src['UTFrom']);
		$komasu = count($this->src['UTFrom']);

		$sql = "SELECT * FROM m_stjfee WHERE ";
		$sql.= $this->getFeeCondition($wday);
		$sql.= " AND feetourokukbn='4' AND timefrom<>'min'";
		$sql.= " ORDER BY timefrom, timeto";

		$aWhere = array($this->src['localgovcode'],
				$this->src['shisetsucode'], $rcd, $cno,
				$this->src['usedate'], $this->src['usedate'],
				$this->MonthDay, $this->MonthDay); 
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val['timefrom']][$val['timeto']] = $this->extract_fee($val, $this->FeeKbn);
		}
		unset($res);

		$match = false;
		$timeFrom = '';
		$timeTo = '';
		$fee = 0;
		for ($i = 0; $i < $komasu; ++$i)
		{
			$key = $timecode[$i];
			$timeFrom = $this->src['UTFrom'][$key].'00';
			$timeTo = $this->src['UTTo'][$key].'00';
			if (isset($recs[$timeFrom][$timeTo])) {
				$fee += $recs[$timeFrom][$timeTo];
				$match = true;
			} else {
				$match = false;
				break;
			}
		}
		if ($match) return $fee;

		for ($i = $komasu-1; 0 <= $i; --$i)
		{
			$key = $timecode[$i];
			$timefrom = $this->src['UTFrom'][$key].'00';
			$timeTo = $this->src['UTTo'][$key].'00';
			if (isset($recs[$timefrom][$timeTo])) {
				$fee += $recs[$timefrom][$timeTo];
			} else {
				break;
			}
		}

		$oldvalue = 240000;
		$tmpfee = 0;
		foreach ($recs as $from => $rec)
		{
			foreach ($rec as $to => $val)
			{
				if ($from <= $timeFrom && $timeTo <= $to) {
					$tmpvalue = intval($to) - intval($from);
					if ($oldvalue > $tmpvalue) $tmpfee = $val;
					$oldvalue = $tmpvalue;
				}
			}
		}
		$fee += $tmpfee;
		return $fee;
	}

	//
	// 料金取得
	//
	function get_price($rcd, $cno)
	{
		$Fee = 0;

		$wday = strtolower(date('D', strtotime($this->src['usedate']))).'flg';
		$sql = "SELECT holiflg FROM m_holiday WHERE localgovcode=? AND heichouholiday=? ";
		$HoliFlg = $this->con->getOne($sql, array($this->src['localgovcode'], $this->src['usedate']));
		if ($HoliFlg == '1') $wday = 'holiflg';

		$sql = "SELECT * FROM m_stjfee WHERE ". $this->getFeeCondition($wday);
		$sql.= " AND timefrom='min' AND (timeto IS NULL OR timeto='') LIMIT 1";
		$aWhere = array($this->src['localgovcode'], $this->src['shisetsucode'],
				$rcd, $cno,
				$this->src['usedate'], $this->src['usedate'], 
				$this->MonthDay, $this->MonthDay);
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (count($rec) == 0) return 0;

		//最低利用料金
		if ($rec['minimumusefeeflg'] == 1) {
			$Fee = $this->extract_fee($rec, $this->FeeKbn);
		}

		//料金計算
		if ($rec['feetourokukbn'] == '1') {
			$tmpfee = 0;
			foreach ($this->src['UTFrom'] as $key => $val)
			{
				$useTimeFrom = $val.'00';
				$useTimeTo = $this->src['UTTo'][$key].'00';

				$tmpfee += $this->getFeePerKoma(1, $rcd, $cno,
						$wday, $useTimeFrom, $useTimeTo);
			}
			$Fee += $tmpfee;
		} elseif ($rec['feetourokukbn'] == '3') {
			$timecode = array_keys($this->src['UTFrom']);
			$koma = count($this->src['UTFrom']);
			$first_index = $timecode[0];
			$last_index =  $timecode[$koma - 1];
			$useTimeFrom = $this->src['UTFrom'][$first_index].'00';
			$useTimeTo = $this->src['UTTo'][$last_index].'00';
			$tmpfee = $this->getFeePerKoma(3, $rcd, $cno,
					$wday, $useTimeFrom, $useTimeTo);
			if ($tmpfee == 0) {
				foreach ($this->src['UTFrom'] as $key => $val)
				{
					$useTimeFrom = $val.'00';
					$useTimeTo = $this->src['UTTo'][$key].'00';

					$tmpfee += $this->getFeePerKoma(3, $rcd, $cno,
							$wday, $useTimeFrom, $useTimeTo);
				}
			}
			$Fee += $tmpfee;
		} elseif ($rec['feetourokukbn'] == '4') {
			$Fee += $this->getFeeByMultiKoma($rcd, $cno, $wday);
		} else {
			$koma = count($this->src['UTFrom']);
			$tmpfee = $this->getFeePerKoma(2, $rcd, $cno, $wday, '', '');
			if ($rec['feeunitflg'] == 1) {
				$koma = ceil($koma/$rec['feeunit']);
			}
			$tmpfee *= $koma;
			$Fee += $tmpfee;
		}
		return floor($Fee);
	}

	function get_shisetsu_fee($genInfo, $extra='')
	{
		$tmpGen = array(0, '', '');
		$genFee = 0;
		$nogenFee = 0;
		$extRate = 0;
		$genRate = 0;

		$this->src['suuryotani'] = '';
		if ($genInfo != '') {
			$tmpGen = explode(',', $genInfo);
			if (preg_match('/'.$tmpGen[1].'/', $this->src['genapplyflg'])) {
				$genRate = intval($tmpGen[0]);
				$this->src['suuryotani'] = $tmpGen[1].','.$tmpGen[2];
			}
		}

		$fee = $this->get_price($this->src['shitsujyocode'], $this->src['combino']);

		$this->src['surcharge'] = '';
		if ($extra != '') {
			$tmpExt = explode(',', $extra);
			$extRate = -1*(intval($tmpExt[0]) - 100);
			$this->src['surcharge'] = $tmpExt[1];
			$fee = $this->calc_fee($fee, 0, $extRate, $this->src['fractionflg']);
		}

		if ($genRate == 0) {
			$nogenFee = $fee;
		} else {
			$genFee = $fee;
		}
		if (count($this->src['Fuzoku']) > 0) {
			$this->src['FuzokuGen'] = array();
			$this->src['FuzokuExt'] = array();
			$i = 0;
			foreach($this->src['Fuzoku'] as $value)
			{
				$flg = $this->get_GenApplyFlg(strval($value));
				$this->src['FuzokuGen'][$i] = false;
				if ($genRate != 0 && preg_match('/'.$tmpGen[1].'/', $flg[0])) {
					$this->src['FuzokuGen'][$i] = true;
					if ($tmpGen[1] == '3' && !preg_match('/'.$tmpGen[2].'/', $flg[1])) {
						$this->src['FuzokuGen'][$i] = false;
					}
				}
				$this->src['FuzokuExt'][$i] = false;
				$fee = $this->get_price(strval($value), 0);

				if ($this->src['surcharge'] != '') {
					if (preg_match('/'.$this->src['surcharge'].'/', $flg[2])) {
						$this->src['FuzokuExt'][$i] = true;
						$fee = $this->calc_fee($fee, 0, $extRate, $this->src['fractionflg']);
					}
				}

				if ($this->src['FuzokuGen'][$i]) {
					$genFee += $fee;
				} else {
					$nogenFee += $fee;
				}
				++$i;
			}
		}

		$fee = $this->calc_fee($genFee, 0, $genRate, $this->src['fractionflg']) + $nogenFee;

		$rec = array();
		$rec['BaseFee'] = $genFee + $nogenFee;
		$rec['ShisetsuFee'] = $fee;
		return $rec;
	}
}
?>
