<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  reserve_status.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/time_schedule.class.php';

class reserve_status extends time_schedule
{
	private $mcd = array();
	private $ysp = array();
	private $NowDate = '';
	private $status_mark = array(
			1 => array(1, 0, '-', '-', '#ffffff'),
			2 => array(2, 0, '休館', '休館', _COLOR_CLOSE_),
			4 => array(4, 0, '抽選（締切）', '締切', _COLOR_LOT_),
			6 => array(6, 0, '×', '×', _COLOR_RESERVED_),
			9 => array(9, 0, '確認中', '確認中', _COLOR_LOT_),
			10 => array(10, 0, '○', '○', _COLOR_VACANCY_),
			11 => array(11, 0, '抽選', '抽選', _COLOR_LOT_),
			12 => array(12, 0, '△', '△', _COLOR_VACANCY_),
			17 => array(17, 0, '休館', '休館', _COLOR_CLOSE_)
			);

	function __construct(&$con, $lcd, $scd, $rcd, $mcd, $yspflg=true)
	{
		parent::__construct($con, $lcd, $scd, $rcd);
		$this->mcd = $mcd;

		if ($yspflg) $this->set_schedule_ptn();

		$this->NowDate = date('Ymd');
	}

	function set_schedule_ptn()
	{
		$sql = "SELECT t.openkbn, t.ipnchgflg1, t.ipnchgflg2,
			t.yoyakukojindanflg, t.pulloutkojindanflg,
			t.yoyakuareapriorityflg, t.pulloutareapriorityflg,
			t.yoyakudispkoma, t.pulloutdispkoma, t.genapplyflg,
			t.msg1, t.msg2, p.*,
			s.showdanjyoninzuflg, s.showoutofserviceflg, s.shinsaflg, s.fractionflg
			FROM m_shitsujyou t
			JOIN m_yoyakuscheduleptn p
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN m_shisetsu s
			USING (localgovcode, shisetsucode)
			WHERE t.localgovcode=? and t.shisetsucode=? and t.shitsujyocode=?";
		$aWhere = array($this->lcd, $this->scd, $this->rcd);
		$this->ysp = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if ($this->ysp['pulloutflg'] == 1 && $this->mcd[0] != 'ZZ') {
			$sql = "SELECT DISTINCT pulloutukemnflg FROM m_men 
				WHERE localgovcode=? AND shisetsucode=? 
				AND shitsujyocode=? ".$this->MenSql($this->mcd);
			$aWhere = array ($this->lcd, $this->scd, $this->rcd);
			$flgs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC|DB_FETCHMODE_FLIPPED);
			if (empty($flgs['pulloutukemnflg'])) {
				$this->ysp['pulloutflg'] = 0;
			} elseif (count($flgs['pulloutukemnflg']) != 1 || $flgs['pulloutukemnflg'][0] != '2') {
				$this->ysp['pulloutflg'] = 0;
			}
		}
	}

	function set_combi_openkbn($cno)
	{
		$sql = "SELECT DISTINCT openkbn FROM m_mencombination";
		$sql.= " WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=? AND openkbn_disable='0'";
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $cno);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res != '') $this->ysp['openkbn'] = $res;
	}

	function get_schedule_ptn()
	{
		return $this->ysp;
	}

	//----------------------------------------------------------------
	// コマ情報に空き/予約済みの記録
	//
	// @param  	$aTimeKoma	: コマ情報
	//	 	$UseDate	:  利用日
	//
	// @return 	$aTimeKoma[i]['reserved'] : 予約済み:1 空き:0
	// 	 	$aTimeKoma[i]['HonYoyakuKbn']: 予約状況
	// 	 	$aTimeKoma[i]['YoyakuKbn']: 予約区分
	// 		$aTimeKoma[i]['Mark']: 予約済みの表示マーク
	// 	 	$aTimeKoma[i]['YoyakuNum']: 予約番号
	// 	 	$aTimeKoma[i]['UserID']: 利用者ID
	//----------------------------------------------------------------
	function get_reserved_user(&$aTimeKoma, $UseDate)
	{
		$res = array();
		$ykbnName = '休館';
		$ykbn = '03';
		$closed = $this->KyukanHantei($UseDate);
		switch ($closed) {
		case 0:
			$sql = "SELECT yoyakunum, userid,
				usetimefrom, usetimeto, honyoyakukbn, yoyakukbn
				FROM t_yoyaku
				WHERE localgovcode=? AND shisetsucode=? 
				AND shitsujyocode=? ".$this->MenSql($this->mcd)."
				AND usedatefrom=? AND honyoyakukbn<>'04'
				AND shinsakbn<>'2'";
			$aWhere = array($this->lcd, $this->scd, $this->rcd, $UseDate);
			$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
			break;
		case 2:
		case 17:
			break;
		default:
			$ykbnName = $this->getYoyakuKbnCodeName(sprintf('%02d', $closed-100));
			break;
		}

		foreach ($aTimeKoma as $key => $koma)
		{
			$aTimeKoma[$key]['YoyakuNum'] = '';
			$aTimeKoma[$key]['UserID'] = '';

			if ($closed != 0) {
				$aTimeKoma[$key]['reserved'] = 2;
				$aTimeKoma[$key]['HonYoyakuKbn'] = '02';
				$aTimeKoma[$key]['YoyakuKbn'] = $ykbn;
				$aTimeKoma[$key]['Mark'] = $ykbnName;
				continue;
			}

			$aTimeKoma[$key]['reserved'] = 0;
			$aTimeKoma[$key]['HonYoyakuKbn'] = '';
			$aTimeKoma[$key]['YoyakuKbn'] = '02';
			$aTimeKoma[$key]['Mark'] = $this->status_mark[10][2];

			foreach ($res as $val)
			{
				if ($val['usetimefrom'] <= $koma['From'] && $val['usetimeto'] >= $koma['To']) {
					$aTimeKoma[$key]['reserved'] = 1;
					$aTimeKoma[$key]['HonYoyakuKbn'] = $val['honyoyakukbn'];
					$aTimeKoma[$key]['YoyakuKbn'] = $val['yoyakukbn'];

					if ($val['yoyakukbn'] == '02') {
						$aTimeKoma[$key]['Mark'] = $this->status_mark[6][2];
						$aTimeKoma[$key]['YoyakuNum'] = $val['yoyakunum'];
						$aTimeKoma[$key]['UserID'] = $val['userid'];
						break;
					} else {
						$aTimeKoma[$key]['Mark'] = $this->getYoyakuKbnCodeName($val['yoyakukbn']);
					}
				}
			}
		}
		unset($res);
	}

	//----------------------------------------------------------------
	// 1日分の予約空き状況を返す。
	//
	// @return array : '-':1 '休館':2 '×':6 '○':10 '抽選':11 '△':12
	//----------------------------------------------------------------
	function put_day_status(&$aTimeKoma, $UseDate)
	{
		$pindex = count($aTimeKoma);
		if ($pindex == 0) return array(1, '-', '');

		$monthkey = intval(substr($UseDate, 4, 2)) - 1;
		$openkbn_arr = explode(',', $this->ysp['openkbn']);
		if ($openkbn_arr[$monthkey] == '0') return array(1, '-', '');

		//---------- 時間制限 -------------
		if ($UseDate == $this->NowDate) {
			$komaKey = array_keys($aTimeKoma);
			$resTimeTo = $aTimeKoma[$komaKey[$pindex-1]]['To'];
			if (intval(date('His')) >= $resTimeTo) return array(1, '-', '');
		}

		//---------- 休館の判定 -------------
		$kyukan = $this->KyukanHantei($UseDate);
		switch ($kyukan) {
			case  0:
				break;
			case  2:
				return array(2, $this->status_mark[2][2], '');
			case 17:
				return array(17, '祝祭日', '');
			default:
				$codeName = $this->getYoyakuKbnCodeName(sprintf('%02d', $kyukan-100));
				return array($kyukan, $codeName, '');
		}

		$chusen_flg = 0;	// 0:なし 11:抽選 4:締切 9:確定中
		//---------- 期間外の判定 -------------
		$rst = $this->check_term($UseDate);
		switch ($rst[0]) {
			case  1:
				return array(1, '-', '');
			case  4:
			case  9:
			case 11:
				$chusen_flg = $rst[0];
				break;
		}

		//---------- 市内／市外、個人／団体の制限 -------------
		$poyFlg = ($chusen_flg == 11) ? 1 : 2;
		if (!$this->checkRestriction($poyFlg)) return array(1, '-', '');

		//---------- 予約テーブルのチェック -------------
		$sql = "SELECT DISTINCT yoyakukbn, usetimefrom, usetimeto, combino
			FROM t_yoyaku 
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? ".$this->MenSql($this->mcd)
			." AND usedatefrom=? AND honyoyakukbn<>'04'
			AND shinsakbn<>'2'
			ORDER BY usetimefrom, yoyakukbn";
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $UseDate);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$res_num = count($res);

		$finalStatusArr = array();

		$NowTime = date('His');
		foreach ($aTimeKoma as $key => $koma)
		{
			if ($UseDate == $this->NowDate) {
				if (intval($koma['From']) < intval($NowTime)) continue;
			}
			$finalStatusArr[$key] = 0;
			if ($res_num == 0) continue;
			foreach ($res as $val)
			{
				if ($val['usetimefrom'] <= $koma['From'] && $val['usetimeto'] >= $koma['To']) {
					$code = intval($val['yoyakukbn']);
					$finalStatusArr[$key] = $code;

					if ($code == 2) break;
				}
			}
		}
		if (empty($finalStatusArr)) return array(1, '-', '');

		if (in_array(0, $finalStatusArr)) {
			switch ($chusen_flg) {
			case 11:
				return array(11, $this->status_mark[11][2], $UseDate);
			case 4:
				return array(4, $this->status_mark[4][2], '');
			case 9:
				return array(9, $this->status_mark[9][2], $UseDate);
			default:
				if (in_array(2, $finalStatusArr)) {
					return array(12, $this->status_mark[12][2], $UseDate);
				} else {
					return array(10, $this->status_mark[10][2], $UseDate);
				}
			}
		} elseif (in_array(2, $finalStatusArr)) { //×
			switch ($chusen_flg) {
			case 4:
				return array(4, $this->status_mark[4][2], '');
			case 9:
				return array(9, $this->status_mark[9][2], $UseDate);
			default:
				return array(6, $this->status_mark[6][2], $UseDate);
			}
		} elseif (in_array(5, $finalStatusArr)) { //開放
			$codeName = $this->getYoyakuKbnCodeName('05');
			return array(105, $codeName, $UseDate);
		} elseif (in_array(3, $finalStatusArr)) { //休館
			return array(2, $this->status_mark[2][2], '');
		} else {
			$finalStatusArr = array_values($finalStatusArr);
			$code = $finalStatusArr[0];
			$codeName = $this->getYoyakuKbnCodeName(sprintf('%02d', $code));
			return array($finalStatusArr[0]+100, $codeName, '');
		}
	}

	/**
	 * 期間判定
	 *
	 * @return int : 0 = 期間内 1 = 期間後 2 = 期間前 3 = 抽選期間前
	 *		4 = 抽選締切 9 = 確定期間 11 = 抽選申込期間
	 */
	function check_term($UseDate)
	{
		$Prec = $this->ysp;
		$showFlg = $Prec['showoutofserviceflg'] == '0' ? 1 : 0;
		$pulloutflg = 0;
		$useMonth = intval(substr($UseDate,4,2));

		if ($Prec['pulloutflg'] == 1) {
			$pulloutflg = 1;
			if ($Prec['pulloutukekbn'] == 2) {
				$ukeVal = explode(',', $Prec['pulloutukekikan']);
				if (!in_array($useMonth, $ukeVal)) $pulloutflg = 0;
			}
		}

		if ($pulloutflg == 1 && $this->NowDate <= $UseDate) {
			$sql = "SELECT * FROM t_monthpulloutdate
				WHERE localgovcode=? AND shisetsucode=? 
				AND shitsujyocode=? AND month=?";
			$aWhere = array($this->lcd, $this->scd, $this->rcd, $useMonth);
			$aMonth = $this->con->getRow($sql,$aWhere,DB_FETCHMODE_ASSOC);
			$StartDT = $this->getMatchTime($aMonth['pulloutfromday'],$UseDate,$aMonth['pulloutfromtime']);
			$EndDT = $this->getMatchTime($aMonth['pulloutlimitday'],$UseDate,$aMonth['pulloutlimittime'])+59;
			$OpenDT = $this->getMatchTime($aMonth['pulloutopenfromday'],$UseDate,$aMonth['pulloutopenfromtime']);
			$FixLimtDT = 0;
			if ($Prec['fixflg'] == '1') {
				$FixLimtDT = $this->getMatchTime($aMonth['pulloutfixlimitday'],$UseDate,$aMonth['pulloutfixlimittime']);
			}

			if (time() < $StartDT) {
				return array($showFlg, 3, $StartDT);
			} elseif ($StartDT < time() && time() <= $EndDT) {
				return array(11, 11, $EndDT);
			} elseif ($EndDT < time() && time() <= $OpenDT) {
				return array(4, 4, $OpenDT);
			} elseif ($Prec['fixflg'] == '1' &&
				$OpenDT < time() && time() <= $FixLimtDT) {
				return array(9, 9, $FixLimtDT);
			}
		}

		$StartDT = $this->get_start_date($UseDate);
		$EndDT = $this->get_end_date($UseDate);

		if (time() < $StartDT) {
			return array($showFlg, 2, $StartDT);
		} elseif ($StartDT < time() && time() <= $EndDT) {
			return array(0, 0, $EndDT);
		} elseif ($EndDT < time() && $this->NowDate <= $UseDate) {
			return array(0, 1, $EndDT);
		}
		return array(1, 1, $EndDT);
	}

	function getMatchTime($setDate, $useDate, $setTime='')
	{
		$hh = 0;
		$mm = 0;
		if ($setTime != '') {
			$hh = intval(substr($setTime,0,2));
			$mm = intval(substr($setTime,2,2));
		}
		$setYear = intval(substr($useDate,0,4));
		$setMonth = intval(substr($setDate,0,2));
		if ($setMonth > intval(substr($useDate,4,2))) --$setYear;
		return mktime($hh, $mm, 0, $setMonth, intval(substr($setDate,2,2)), $setYear);
	}

	/**
	 * コマ単位の予約可否を判断する
	 * (戻り)
	 * 1 = 期間外
	 * 2 = 休館
	 * 4 = 抽選（締切）
	 * 5 = 優先事項により予約不可
	 * 6 = 予約不可
	 * 9 = 当選確定期間
	 * 10 = 予約可
	 * 11 = 抽選
	 *
	 * @return unknown
	 */
	function put_koma_status(&$aTimeKoma, $UseDate, $FuzokuCode=NULL)
	{
		$rst = array(0, 0);
		$res = array();
		$compe = array();
		$ykbnName = '';

		$shitsujyocode = $FuzokuCode === NULL ? $this->rcd : $FuzokuCode;

		$pindex = count($aTimeKoma);

		if ($pindex == 0) {
			$rst[0] = 1;
		} else {
			//---------- 休館の判定 -------------
			$closed = $this->KyukanHantei($UseDate);
			if ($closed == 0) {
				//---------- 期間外の判定 -------------
				$rst = $this->check_term($UseDate);
				if ($rst[0] != 1) {
					$sql = "SELECT yoyakukbn, usetimefrom, usetimeto, combino
						FROM t_yoyaku 
						WHERE localgovcode=? AND shisetsucode=?
						AND shitsujyocode=? ".$this->MenSql($this->mcd)."
						AND usedatefrom=? AND honyoyakukbn<>'04'
						AND shinsakbn<>'2'
						ORDER BY usetimefrom";
					$aWhere = array($this->lcd, $this->scd, $shitsujyocode, $UseDate);
					$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
				}
				if ($rst[0] == 9 || $rst[0] == 11) {
					$sql = "SELECT DISTINCT pulloutyoyakunum, usetimefrom, usetimeto, combino
						FROM t_pulloutyoyaku
						WHERE localgovcode=? AND shisetsucode=?
						AND shitsujyocode=? ".$this->MenSql($this->mcd)." AND usedate=?";
					if ($rst[0] == 9) {
						$sql.= " AND pulloutjoukyoukbn='3'";
					}
					$sql.= " ORDER BY usetimefrom";
					$aWhere = array($this->lcd, $this->scd, $shitsujyocode, $UseDate);
					$compe = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
				}
			} elseif ($closed > 100) {
				$ykbnName = $this->getYoyakuKbnCodeName(sprintf('%02d', $closed-100));
			}
		}

		$monthkey = intval(substr($UseDate, 4, 2)) - 1;
		$openkbn_arr = explode(',', $this->ysp['openkbn']);

		$NowTime = date('His');
		foreach ($aTimeKoma as $key => $koma)
		{
			$mark = $this->status_mark[10];
			if ($openkbn_arr[$monthkey] == '0') {
				$mark = $this->status_mark[1];
			} elseif ($UseDate == $this->NowDate && intval($koma['From']) < intval($NowTime)) {
				$mark = $this->status_mark[1];
			} elseif ($rst[0] == 1) {
				$mark = $this->status_mark[1];
			} elseif ($closed > 100) {
				$mark = array($closed, 0, $ykbnName, $ykbnName, _COLOR_CLOSE_);
			} elseif ($closed > 0) {
				$mark = $this->status_mark[$closed];
			} elseif ($rst[0] == 4) {
				$mark = $this->status_mark[4];
			} else {
				foreach ($res as $val)
				{
					if ($val['usetimefrom'] <= $koma['From'] && $val['usetimeto'] >= $koma['To']) {
						if ($val['yoyakukbn'] == '02') {
							$mark = $this->status_mark[6];
							break;
						} else {
							$ykbnName = $this->getYoyakuKbnCodeName($val['yoyakukbn']);
							$mark = array(intval($val['yoyakukbn'])+100, 0, $ykbnName, $ykbnName, _COLOR_CLOSE_);
						}
					}
				}
				if ($rst[0] == 9 || $rst[0] == 11) {
					$competitive = 0;
					foreach ($compe as $val)
					{
						if ($val['usetimefrom'] <= $koma['From'] && $val['usetimeto'] >= $koma['To']) {
							++$competitive;
						}
					}
					if ($mark[0] == 10) {
						$mark = $this->status_mark[$rst[0]];
					} elseif ($competitive != 0) {
						$mark = $this->status_mark[$rst[0]];
					}
					if ($mark[0] == 11 && $rst[0] == 11) {
						$mark[1] = $competitive;
						$mark[2].= '('.$competitive.')';
					}
				}
			}
			$aTimeKoma[$key]['mark'] = $mark;
		}
		unset($res);
		unset($compe);
	}

	//
	// MenCodeをSQL文に変換
	//
	function MenSql($MenCode)
	{
		return " AND (mencode='".implode("' OR mencode='", $MenCode)."')";
	}

	function getYoyakuKbnCodeName($Code)
	{
		$sql = "SELECT codename FROM m_codename 
			WHERE localgovcode=? AND codeid='YoyakuKbn' AND code=?"; 
		return $this->con->getOne($sql,array($this->lcd, $Code));
	}

	//
	// 予約受付開始日
	//
	function get_start_date($UseDate)
	{
		$Prec = $this->ysp;

		//利用者の市内/市外
		$UserAreaKbn = '01';
		if (isset($_SESSION['UID'])) {
			$UserAreaKbn = $_SESSION['USERAREAKBN'];
		}

		$ResStartMon = 'ippan'; //市内
		$ResStartDay = 'ippan';
		$ResFromMon = 'ippan';
		$ResFromDay = 'ippan';
		$ResFromTime = 'ippan';
		$ResStartFlg = 'ippan';

		if ($UserAreaKbn == '02') { //市外
			$ResStartMon .= 'shigai';
			$ResStartDay .= 'shigai';
			$ResFromMon .= 'shigai';
			$ResFromDay .= 'shigai';
			$ResFromTime .= 'shigai';
			$ResStartFlg .= 'shigai';
		}
		$ResStartMon .= 'resstartmon';
		$ResStartDay .= 'resstartday';
		$ResFromMon .= 'resfrommon';
		$ResFromDay .= 'resfromday';
		$ResFromTime .= 'resfromtime';
		$ResStartFlg .= 'resstartflg';

		$StartFlg = $Prec[$ResStartFlg];

		$usedate = strtotime($UseDate);
		$useYear = date('Y', $usedate);
		$useMonth = date('n', $usedate);
		$useDay = date('j', $usedate);
		$useTime = intval(substr($Prec[$ResFromTime],0,2))*3600
				+intval(substr($Prec[$ResFromTime],2,2))*60;

		$FromDT = 0;
		if ($StartFlg == '1') { //月前
			$useMonth -= $Prec[$ResStartMon];
			$last_day = date('t', mktime(0, 0, 0, $useMonth, 1, $useYear));
			if ($last_day < $useDay) $useDay = $last_day;
			$FromDT = mktime(0, 0, 0, $useMonth, $useDay, $useYear) - $Prec[$ResStartDay] * 86400;
		} elseif ($StartFlg == '2') { //日前
			$FromDT = $usedate - $Prec[$ResStartDay] * 86400;
		} elseif ($StartFlg == '3') { //開始日指定
			$useMonth -= $Prec[$ResFromMon];
			$last_day = date('t', mktime(0, 0, 0, $useMonth, 1, $useYear));
			$useDay = $Prec[$ResFromDay] ? $Prec[$ResFromDay] : 1;
			if ($last_day < $useDay) $useDay = $last_day;
			$FromDT = mktime(0, 0, 0, $useMonth, $useDay, $useYear);
		}
		$FromDT += $useTime;
		if ($Prec['ipnchgflg1'] == 2 || $Prec['ipnchgflg2'] == 2) {
			while (true)
			{
				$FromDate = date('Ymd', $FromDT);
				$HoliFlg = '';
				$Crec = 0;
				if ($Prec['ipnchgflg1'] == 2) {
					$HoliFlg = $this->getHoliFlg($FromDate);
				}
				if ($Prec['ipnchgflg2'] == 2) {
					$Crec = $this->KyukanHantei($FromDate);
				}
				if ($HoliFlg == '0' || $Crec != 0) {
					$FromDT += 86400;
				} else {
					break;
				}
			}
		}
		return $FromDT;
	}

	//
	// 予約受付締切日
	//
	function get_end_date($UseDate)
	{
		$Prec = $this->ysp;

		//利用者の市内/市外
		$UserAreaKbn = '01';
		if (isset($_SESSION['UID'])) {
			$UserAreaKbn = $_SESSION['USERAREAKBN'];
		}

		$ResLimitMon = 'ippan'; //市内
		$ResLimitDay = 'ippan';
		$ResToMon = 'ippan';
		$ResToDay = 'ippan';
		$ResToTime = 'ippan';
		$ResLimitFlg = 'ippan';

		if ($UserAreaKbn == '02') { //市外
			$ResLimitMon .= 'shigai';
			$ResLimitDay .= 'shigai';
			$ResToMon .= 'shigai';
			$ResToDay .= 'shigai';
			$ResToTime .= 'shigai';
			$ResLimitFlg .= 'shigai';
		}
		$ResLimitMon .= 'reslimitmon';
		$ResLimitDay .= 'reslimitday';
		$ResToMon .= 'restomon';
		$ResToDay .= 'restoday';
		$ResToTime .= 'restotime';
		$ResLimitFlg .= 'reslimitflg';

		$LimitFlg = $Prec[$ResLimitFlg];

		$usedate = strtotime($UseDate);
		$useYear = date('Y', $usedate);
		$useMonth = date('n', $usedate);
		$useDay = date('j', $usedate);
		$useTime = intval(substr($Prec[$ResToTime],0,2))*3600
				+intval(substr($Prec[$ResToTime],2,2))*60+59;

		$LimitDT = 0;
		if ($LimitFlg == '1') { //月前
			$useMonth -= $Prec[$ResLimitMon];
			$last_day = date('t', mktime(0, 0, 0, $useMonth, 1, $useYear));
			if ($last_day < $useDay) $useDay = $last_day;
			$LimitDT = mktime(0, 0, 0, $useMonth, $useDay, $useYear) - $Prec[$ResLimitDay] * 86400;
		} elseif ($LimitFlg == '2') { //日前
			$LimitDT = $usedate - $Prec[$ResLimitDay] * 86400;
		} elseif ($LimitFlg == '3') { //締切日指定
			$useMonth -= $Prec[$ResToMon];
			$last_day = date('t', mktime(0, 0, 0, $useMonth, 1, $useYear));
			$useDay = $Prec[$ResToDay] ? $Prec[$ResToDay] : 1;
			if ($last_day < $useDay) $useDay = $last_day;
			$LimitDT = mktime(0, 0, 0, $useMonth, $useDay, $useYear);
		}
		$LimitDT += $useTime;
		return $LimitDT;
	}

	//
	// 制限check
	//
	// $poyFlg  1:抽選 2:予約
	//
	function checkRestriction($poyFlg)
	{
		if (!isset($_SESSION['UID'])) return 2;

		$UserAreaKbn = $_SESSION['USERAREAKBN']; //市内/市外 01:市内 02:市外
		$KojinDanKbn = $_SESSION['KOJINDANKBN']; //団体/個人 1:個人 2:団体
		if ($UserAreaKbn != '02') $UserAreaKbn = '01';
		if ($KojinDanKbn != '1') $KojinDanKbn = '2';

		$Prec = $this->ysp;

		$target = ($poyFlg == 1) ? 'pullout' : 'yoyaku';

		$area_result = true;
		if ($Prec[$target.'areapriorityflg'] != '0') {
			$area_result = intval($Prec[$target.'areapriorityflg']) == intval($UserAreaKbn)? true:false;
		}
		$kojindan_result = true;
		if ($Prec[$target.'kojindanflg'] != '0') {
			$kojindan_result = intval($Prec[$target.'kojindanflg']) == intval($KojinDanKbn)? true:false;
		}
		return ($area_result && $kojindan_result)? 1:0;
	}

	function putRestrictionMsg($poyFlg)
	{
		$Prec = $this->ysp;

		$target = ($poyFlg == 1) ? 'pullout' : 'yoyaku';

		$areaflg = intval($Prec[$target.'areapriorityflg']);
		$kojindanflg = intval($Prec[$target.'kojindanflg']);
		if ($kojindanflg > 2) $kojindanflg = 2;

		return 3 * $areaflg + $kojindanflg;
	}
}
?>
