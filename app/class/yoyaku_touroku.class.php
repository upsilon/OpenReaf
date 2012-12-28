<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約登録クラス
 *
 *  yoyaku_touroku.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/time_schedule.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_calc.class.php';
require OPENREAF_ROOT_PATH.'/app/class/touroku_base.class.php';

class yoyaku_touroku extends touroku_base
{
	private $LotDay = '';

	function __construct(&$oDB, $updid)
	{
		parent::__construct($oDB, $updid);
	}

	//---------------------------
	// 抽選日時取得
	//---------------------------
	function get_LotDay()
	{
		return $this->LotDay;
	}

	//
	// 予約番号発番
	//
	function emit_yoyaku_number()
	{
		$sql = "SELECT saibancode, saibanno, saibannolng, prefix, suffix";
		$sql.= " FROM m_saiban WHERE localgovcode=? AND saibancode=? FOR UPDATE";
		$aWhere = array(_CITY_CODE_, 'YoyakuNum');
		$where = "localgovcode='"._CITY_CODE_."' AND saibancode='YoyakuNum'";

		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec = $this->oDB->check_error($rec);

		$upData['saibanno'] = ++$rec['saibanno'];
		$upData['upddate'] = date('Ymd');
		$upData['updtime'] = date('His');
		$upData['updid'] = $this->updid;
		$this->con->autoExecute('m_saiban', $upData, DB_AUTOQUERY_UPDATE, $where);
		$num = $this->con->affectedRows();
		if ($num == 1)
		{
			$sStyle = "%0{$rec['saibannolng']}d";

			return $rec['prefix'].sprintf($sStyle, $rec['saibanno']).$rec['suffix'];
		}
		return false;
	}

	function set_yoyaku(&$ses, $YoyakuNum)
	{
		$checkArr = array();

		$genmen_flg = $ses['suuryotani'] == '' ? false : true;
		$extra_flg = $ses['surcharge'] == '' ? false : true;

		foreach ($ses['mencode'] as $val)
		{
			$checkArr[] = array('ShitsujyoCode' => $ses['shitsujyocode'],
						'MenCode' => $val,
						'CombiNo' => $ses['combino'],
						'Extra' => $extra_flg,
						'GenApplyFlg' => $genmen_flg);
		}
		foreach ($ses['Fuzoku'] as $key => $val)
		{
			$checkArr[] = array('ShitsujyoCode' => $val,
						'MenCode' => 'ZZ',
						'CombiNo' => 0,
						'Extra' => $ses['FuzokuExt'][$key],
						'GenApplyFlg' => $ses['FuzokuGen'][$key]);
		}

		$sql = 'SELECT feepaylimtkbn, feepaylimtday, ippanyoyakukbn
			FROM m_yoyakuscheduleptn
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $ses['shisetsucode'], $ses['shitsujyocode']);
		$prec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$y_data = $this->oDB->make_base_dataset($ses, 't_yoyaku');

		$y_data['shisetsupaylimitdate'] = $this->calc_FeePayLimitDay($ses['usedate'], $prec['feepaylimtkbn'], $prec['feepaylimtday']);

		$shinsaFlg = isset($ses['shinsaflg']) ? $ses['shinsaflg'] : 0;
		$yoyakuKbn = intval($ses['YoyakuKbn']);

		if ($yoyakuKbn > 2) {
			$y_data['shinsakbn'] = '0';
			$y_data['honyoyakukbn'] = '02';
		} else {
			if ($shinsaFlg == 1) {
				$y_data['shinsakbn'] = '4';
				$y_data['honyoyakukbn'] = '03';
			} elseif ($shinsaFlg == 2) {
				$y_data['shinsakbn'] = '4';
				$y_data['honyoyakukbn'] = '04';
			} else {
				$y_data['shinsakbn'] = '0';
				if ($prec['ippanyoyakukbn'] == 2) {
					$y_data['honyoyakukbn'] = '01';
				} else {
					$y_data['honyoyakukbn'] = '02';
				}
			}
		}
		$y_data['yoyakunum'] = $YoyakuNum;
		$y_data['usedatefrom'] = $ses['usedate'];
		$y_data['yoyakukbn'] = sprintf('%02d', $yoyakuKbn);
		$y_data['daikouid'] = $this->updid;
		$y_data['updid'] = $this->updid;
		$y_data['usekbn'] = $ses['FeeKbn'];

		$oFC = new fee_calc($this->con, $ses);
		$taxRate = $oFC->get_tax_rate();
		$genRate = 0;
		if (isset($ses['GenmenRate'])) {
			$genRate = $ses['GenmenRate'];
		} else {
			$tep = $oFC->get_user_gen();
			if ($tep) {
				$genRate = $tep['Rate'];
			}
		}
		$extRate = isset($ses['ExtraRate']) ? $ses['ExtraRate'] : 100;
		$feeRate = 1;
		if (isset($ses['OriginalFee']) && $ses['OriginalFee'] != 0) {
			$feeRate = round($ses['TotalBaseFee']/$ses['OriginalFee']);
		}

		$k_data = array();
		if (_YOYAKUKANRI_TABLE_ && $y_data['honyoyakukbn'] != '04') {
			$k_data['localgovcode'] = _CITY_CODE_;
			$k_data['usedate'] = $ses['usedate'];
			$k_data['shisetsucode'] = $ses['shisetsucode'];
			$k_data['updid'] = $this->updid;

			$oTS = new time_schedule($this->con, _CITY_CODE_, $ses['shisetsucode'], $ses['shitsujyocode'], false);
			$ptn = $oTS->get_time_schedule_ptn($ses['usedate'], true);
			foreach ($ptn as $kbn => $koma)
			{
				$k_data['usetimefrom'.$kbn] = $koma['From'];
				$k_data['usetimeto'.$kbn] = $koma['To'];
				$k_data['komaflg'.$kbn] = '0';
			}
			foreach ($ses['UTFrom'] as $kbn => $value)
			{
				$k_data['komaflg'.$kbn] = 1;
			}
		}
		$sql = 'SELECT * FROM t_yoyakukanri WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND mencode=? AND usedate=? FOR UPDATE';

		foreach ($checkArr as $val)
		{
			if (_YOYAKUKANRI_TABLE_ && $y_data['honyoyakukbn'] != '04') {
				$aWhere = array(_CITY_CODE_, $ses['shisetsucode'],
						$val['ShitsujyoCode'], $val['MenCode'],
						$ses['usedate']);
				$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
				if ($row) {
					foreach ($ses['UTFrom'] as $kbn => $value)
					{
						if ($row['komaflg'.$kbn] == 1) {
							return false;
						} else {
							$row['komaflg'.$kbn] = 1;
						}
					}
					$row['upddate'] = date('Ymd');
					$row['updtime'] = date('His');
					$row['updid'] = $this->updid;
					$where = "localgovcode='"._CITY_CODE_
						."' AND usedate='".$ses['usedate']
						."' AND shisetsucode='".$ses['shisetsucode']
						."' AND shitsujyocode='".$val['ShitsujyoCode']
						."' AND mencode='".$val['MenCode']."'";
					$this->con->autoExecute('t_yoyakukanri', $row, DB_AUTOQUERY_UPDATE, $where);
				} else {
					$k_data['shitsujyocode'] = $val['ShitsujyoCode'];
					$k_data['mencode'] = $val['MenCode'];
					$k_data['upddate'] = date('Ymd');
					$k_data['updtime'] = date('His');
					$this->con->autoExecute('t_yoyakukanri', $k_data, DB_AUTOQUERY_INSERT);
				}
				$num = $this->con->affectedRows();
$num = 1;
				if ($num != 1) return false;
			}

			$y_data['shitsujyocode'] = $val['ShitsujyoCode'];
			$y_data['mencode'] = $val['MenCode'];
			$y_data['combino'] = $val['CombiNo'];
			$fee = 0;
			if ($yoyakuKbn == 2) {
				$fee = $feeRate * $oFC->get_price($val['ShitsujyoCode'], $val['CombiNo']);
			}
			if ($val['Extra']) {
				$rate = -1*($extRate - 100);
				$fee = $oFC->calc_fee($fee, 0, $rate, $ses['fractionflg']);
			}
			$y_data['baseshisetsufee'] = $fee;
			$rate = $val['GenApplyFlg'] ? $genRate : 0;
			$y_data['shisetsufee'] = $oFC->calc_fee($fee, $taxRate, $rate, $ses['fractionflg']);
			$y_data['shisetsutax'] = $oFC->calc_tax($fee, $taxRate, $rate, $ses['fractionflg']);
			$y_data['appdate'] = date('Ymd');
			$y_data['apptime'] = date('His');
			$y_data['upddate'] = date('Ymd');
			$y_data['updtime'] = date('His');
			$this->con->autoExecute('t_yoyaku', $y_data, DB_AUTOQUERY_INSERT);
			$num = $this->con->affectedRows();
			if ($num != 1) return false;
		}
		return true;
	}

	function insert_lot(&$ses, $YoyakuNum)
	{
		$checkArr = array();

		$genmen_flg = $ses['suuryotani'] == '' ? false : true;
		$extra_flg = $ses['surcharge'] == '' ? false : true;

		foreach ($ses['mencode'] as $val)
		{
			$checkArr[] = array('ShitsujyoCode' => $ses['shitsujyocode'],
						'MenCode' => $val,
						'CombiNo' => $ses['combino'],
						'Extra' => $extra_flg,
						'GenApplyFlg' => $genmen_flg);
		}
		foreach ($ses['Fuzoku'] as $key => $val)
		{
			$checkArr[] = array('ShitsujyoCode' => $val,
						'MenCode' => 'ZZ',
						'CombiNo' => 0,
						'Extra' => $ses['FuzokuExt'][$key],
						'GenApplyFlg' => $ses['FuzokuGen'][$key]);
		}

		$y_data = $this->oDB->make_base_dataset($ses, 't_pulloutyoyaku');

		$useMonth = intval(substr($ses['usedate'],4,2));
		$sql = "SELECT * FROM t_monthpulloutdate WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND month=?";
		$aWhere = array(_CITY_CODE_, $ses['shisetsucode'], $ses['shitsujyocode'], $useMonth);
		$aMonth = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
       		if (!$aMonth) return false;

		$checkMonth = intval(substr($aMonth['pulloutday'],0,2));
		$usePullOutDay = '';
		if ($checkMonth > $useMonth) {
			$usePullOutDay = (substr($ses['usedate'],0,4)-1).$aMonth['pulloutday'];
		} else {
			$usePullOutDay = substr($ses['usedate'],0,4).$aMonth['pulloutday'];
		}

		$this->LotDay = $aMonth['pulloutday'];
		$y_data['pulloutjisshidate'] = $usePullOutDay;
		$y_data['pulloutjisshitime'] = $aMonth['pullouttime'];
		$y_data['pulloutyoyakunum'] = $YoyakuNum;
		$y_data['pulloutjoukyoukbn'] = '1';
		$y_data['daikouid'] = $this->updid;
		$y_data['updid'] = $this->updid;
		$y_data['usekbn'] = $ses['FeeKbn'];

		$oFC = new fee_calc($this->con, $ses);
		$taxRate = $oFC->get_tax_rate();
		$tep = $oFC->get_user_gen();
		$genRate = 0;
		if (isset($ses['GenmenRate'])) {
			$genRate = $ses['GenmenRate'];
		} else {
			$tep = $oFC->get_user_gen();
			if ($tep) {
				$genRate = $tep['Rate'];
			}
		}
		$extRate = isset($ses['ExtraRate']) ? $ses['ExtraRate'] : 100;
		$feeRate = 1;
		if (isset($ses['OriginalFee']) && $ses['OriginalFee'] != 0) {
			$feeRate = round($ses['TotalBaseFee']/$ses['OriginalFee']);
		}

		foreach ($checkArr as $val)
		{
			$y_data['shitsujyocode'] = $val['ShitsujyoCode'];
			$y_data['mencode'] = $val['MenCode'];
			$y_data['combino'] = $val['CombiNo'];
			$fee = $feeRate * $oFC->get_price($val['ShitsujyoCode'], $val['CombiNo']);
			if ($val['Extra']) {
				$rate = -1*($extRate - 100);
				$fee = $oFC->calc_fee($fee, 0, $rate, $ses['fractionflg']);
			}
			$y_data['baseshisetsufee'] = $fee;
			$rate = $val['GenApplyFlg'] ? $genRate : 0;
			$y_data['shisetsufee'] = $oFC->calc_fee($fee, $taxRate, $rate, $ses['fractionflg']);
			$y_data['shisetsutax'] = $oFC->calc_tax($fee, $taxRate, $rate, $ses['fractionflg']);
			$y_data['pulloutukedate'] = date('Ymd');
			$y_data['pulloutuketime'] = date('His');
			$y_data['upddate'] = date('Ymd');
			$y_data['updtime'] = date('His');
			$this->con->autoExecute('t_pulloutyoyaku', $y_data, DB_AUTOQUERY_INSERT);
			$num = $this->con->affectedRows();
			if ($num != 1) return false;
		}
		return true;
	}

	function insert_yoyaku_shinsei(&$ses, $YoyakuNum)
	{
		$y_data = $this->oDB->make_base_dataset($ses, 't_yoyakufeeshinsei');
		$y_data['localgovcode'] = _CITY_CODE_;
		$y_data['feesinkbn'] = $ses['FeeKbn'];
		$y_data['yoyakunum'] = $YoyakuNum;
		$y_data['basefee'] = $ses['TotalBaseFee'];
		$y_data['shisetsufee'] = $ses['TotalShisetsuFee'];
		$y_data['tax'] = $ses['TotalTax'];
		$y_data['suuryo'] = $ses['TotalFee'];
		$y_data['suuryotani'] = $ses['suuryotani'];
		$y_data['surcharge'] = $ses['surcharge'];
		$y_data['paykbn'] = $ses['TotalFee'] == 0 ? 2 : 1;
		$y_data['upddate'] = date('Ymd');
		$y_data['updtime'] = date('His');
		$y_data['updid'] = $this->updid;
		$this->con->autoExecute('t_yoyakufeeshinsei', $y_data, DB_AUTOQUERY_INSERT);
		$num = $this->con->affectedRows();
		if ($num != 1) return false;
		return true;
	}

	function calc_FeePayLimitDay($UseDate, $Kbn, $LimitDay)
	{
		$limit_day = '';

		switch ($Kbn) {
			case 1:
				//前払い
				$pday = strtotime($UseDate) - $LimitDay * 86400;
				$limit_day = date('Ymd', $pday);
				break;
			case 2:
			case 3:
				//当日払い
				$limit_day = $UseDate;
				break;
			case 4:
				//後払い
				$pday = strtotime($UseDate) + $LimitDay * 86400;
				$limit_day = date('Ymd', $pday);
				break;
			case 5:
				//申込日後払い
				$pday = time() + $LimitDay * 86400;
				$limit_day = date('Ymd', $pday);
				$limit_day = $UseDate < $limit_day ? $UseDate : $limit_day;
				break;
			case 6:
				//翌月払い
				$m = date('n') + 1;
				$y = date('Y');
				$pday = mktime(0, 0, 0, $LimitDay, $m, $y);
				$limit_day = date('Ymd', $pday);
				break;
		}
		return $limit_day;
	}
}
?>
