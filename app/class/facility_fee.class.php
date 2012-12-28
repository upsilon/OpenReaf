<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金設定クラス
 *
 *  facility_fee.class.php
 */

class facility_fee
{
	private $oDB = null;
	private $con = null;
	private $err = array();
	private $lcd = '';
	private $scd = '';
	private $cno = 0;
	private $tcd = 0;
	private $apd = '';
	private $prfr = '';
	private $prto = '';

	//-------------------------------------------------------------------
	// コンストラクタ
	//-------------------------------------------------------------------
	function __construct(&$oDB, &$req)
	{
		$this->oDB = $oDB;
		$this->con = $oDB->getCon();
		$this->lcd = _CITY_CODE_;
		$this->scd = $req['scd'];
		$this->rcd = $req['rcd'];
		$this->cno = $req['cno'];
		$this->tcd = empty($req['tcd']) ? 0 : $req['tcd'];
		$this->apd = isset($req['apd']) ? $req['apd'] : '';
		$this->prfr = isset($req['prfr']) ? $req['prfr'] : '';
		$this->prto = isset($req['prto']) ? $req['prto'] : '';
	}

	function get_stj_fee_data()
	{
		$sql = 'SELECT * FROM m_stjfee';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';
		$sql.= ' AND appdatefrom=? AND monthdayfrom=? AND monthdayto=?';
		$sql.= ' AND tourokuno=? ORDER BY timefrom';
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->cno, $this->apd, $this->prfr, $this->prto, $this->tcd);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$para = $res[0];
		unset($para['timefrom']);
		unset($para['timeto']);
		$feeTourokuKbn = $res[0]['feetourokukbn'];
		for ($i = 0; $i < 10; ++$i)
		{
			$para['feekbn'][$i] = '';
			$para['minfee'][$i] = '';
			$para['flatfee'][$i] = '';
			for ($j = 0; $j < _MAX_KOMA_; ++$j) $para['fee'][$j][$i] = '';
		}
		for ($i = 0; $i < _MAX_KOMA_; ++$i)
		{
			$para['timefrom'][$i] = '';
			$para['timeto'][$i] = '';
		}
		if ($feeTourokuKbn == '2') {
			foreach ($res as $val)
			{
				$rec_kind = trim($val['timefrom']);
				if ($rec_kind == 'min') {
					for ($i = 0; $i < 10; ++$i)
					{
						$idx = sprintf('%02d', $i + 1);
						$para['feekbn'][$i] = $val['feekbn'.$idx];
						$para['minfee'][$i] = (int)$val['fee'.$idx];
					}
				} elseif ($rec_kind == 'flat') {
					for ($i = 0; $i < 10; ++$i)
					{
						$idx = sprintf('%02d', $i + 1);
						$para['flatfee'][$i] = (int)$val['fee'.$idx];
					}
				}
			}
		} else {
			$n = 0;
			foreach ($res as $val)
			{
				$rec_kind = trim($val['timefrom']);
				if ($rec_kind == 'min') {
					for ($i = 0; $i < 10; ++$i)
					{
						$idx = sprintf('%02d', $i + 1);
						$para['feekbn'][$i] = $val['feekbn'.$idx];
						$para['minfee'][$i] = (int)$val['fee'.$idx];
					}
				} else {
					$para['timefrom'][$n] = substr($val['timefrom'], 0, 4);
					$para['timeto'][$n] = substr($val['timeto'], 0, 4);
					for ($i = 0; $i < 10; ++$i)
					{
						$idx = sprintf('%02d', $i + 1);
						$para['fee'][$n][$i] = (int)$val['fee'.$idx];
					}
					++$n;
				}
			}
		}
		unset($res);
		return $para;
	}

	function delete_stj_fee($tcd='')
	{
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->cno, $this->apd, $this->prfr, $this->prto);
		$sql = 'DELETE FROM m_stjfee';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';
		$sql.= ' AND appdatefrom=? AND monthdayfrom=? AND monthdayto=?';
		if ($tcd != '') {
			$sql.= ' AND tourokuno=?';
			array_push($aWhere, $tcd);
		}
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;
		return true;
	}

	function get_feekbn_options()
	{
		$sql = 'SELECT feekbn, feekbnname FROM m_feekbn
			WHERE localgovcode=? ORDER BY feekbn';
		$aWhere = array(_CITY_CODE_);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	function set_tab_index(&$para)
	{
		$tabCount = 0;
		for ($i = 0; $i < 10; ++$i)
		{
			$para['feekbnindex'][$i] = $tabCount;
			++$tabCount;
			$para['minfeeindex'][$i] = $tabCount;
			++$tabCount;
			$para['flatfeeindex'][$i] = $tabCount;
			++$tabCount;
		}
		for ($i = 0; $i < _MAX_KOMA_; ++$i)
		{
			$para['timefromindex'][$i] = $tabCount;
			++$tabCount;
			$para['timetoindex'][$i] = $tabCount;
			++$tabCount;
		}
		for ($i = 0; $i < 10; ++$i)
		{
			for ($j = 0; $j < _MAX_KOMA_; ++$j)
			{
				$para['feeindex'][$j][$i] = $tabCount;
				++$tabCount;
			}
		}
	}

	function insert_stj_fee(&$req, $num=-1)
	{
		$touroku_number = $this->tcd;
		if ($num >= 0) $touroku_number = $num + 1;

		$dataset = $this->oDB->make_base_dataset($req, 'm_stjfee');
		$dataset['localgovcode'] = $this->lcd;
		$dataset['shisetsucode'] = $this->scd;
		$dataset['shitsujyocode'] = $this->rcd;
		$dataset['combino'] = $this->cno;
		$dataset['tourokuno'] = $touroku_number;
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		unset ($dataset['timefrom']);
		unset ($dataset['timeto']);

		$dataset['timefrom'] = 'min';
		foreach ($req['minfee'] as $key => $val)
		{
			$idx = sprintf('%02d', $key + 1);
			$dataset['fee'.$idx] = intval($val);
			$dataset['feekbn'.$idx] = $req['feekbn'][$key];
		}
		$rc = $this->oDB->insert('m_stjfee', $dataset);
		if ($rc < 0) return false;

		if ($req['feetourokukbn'] == '2') {
			$dataset['timefrom'] = 'flat';
			foreach ($req['flatfee'] as $key => $val)
			{
				$idx = sprintf('%02d', $key + 1);
				$dataset['fee'.$idx] = intval($val);
			}
			$rc = $this->oDB->insert('m_stjfee', $dataset);
			if ($rc < 0) return false;
		} else {
			foreach ($req['timefrom'] as $key => $val)
			{
				if ($req['timefrom'][$key] != '') {
					$dataset['timefrom'] = $val.'00';
					$dataset['timeto'] = $req['timeto'][$key].'00';
					foreach ($req['fee'][$key] as $key2 => $val2)
					{
						$idx = sprintf('%02d', $key2 + 1);
						$dataset['fee'.$idx] = intval($val2);
					}
					$rc = $this->oDB->insert('m_stjfee', $dataset);
					if ($rc < 0) return false;
				}
				
			}
		}
		return true;
	}

	function get_max_number()
	{
		$sql = 'SELECT max(tourokuno) FROM m_stjfee
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?
			AND appdatefrom=? AND monthdayfrom=?';
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->cno,
				$this->apd, $this->prfr);
		$n = $this->con->getOne($sql, $aWhere);
		return intval($n);
	}

	function expire_stj_fee(&$req)
	{
		$dataset = array();
		$dataset['haishidate'] = $req['HaishiDate'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$where = "localgovcode='".$this->lcd
			."' AND shisetsucode='".$this->scd
			."' AND shitsujyocode='".$this->rcd
			."' AND appdatefrom='".$this->apd
			."' AND monthdayfrom='".$this->prfr
			."' AND monthdayto='".$this->prto."'";

		$rc = $this->oDB->update('m_stjfee', $dataset, $where);
		if ($rc < 0) return false;
		return true;
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data($req)
	{
		$msg = '';

		//適用開始日
		if (trim($req['appdatefrom']) == '') {
			$msg.= '適用開始日を入力してください。<br>';
			$this->err['AppDateFrom'] = 1;
		} elseif (checkdate(substr($req['appdatefrom'],4,2),substr($req['appdatefrom'],6,2),substr($req['appdatefrom'],0,4)) == false) {
			$msg.= '適用開始日が正しくありません。<br>';
			$this->err['AppDateFrom'] = 1;
		}
		//設定期間
		if (trim($req['monthdayfrom']) == '') {
			$msg.= '料金設定期間(開始日)を入力してください。<br>';
			$this->err['MonthDayFrom'] = 1;
		} elseif (!checkdate(substr($req['monthdayfrom'],0,2),substr($req['monthdayfrom'],2,2),2010)) {
			$msg.= "料金設定期間(開始日)が正しくありません。";
			$this->err['MonthDayFrom'] = 1;
		}
		if (trim($req['monthdayto']) == '') {
			$msg.= '料金設定期間(終了日)を入力してください。<br>';
			$this->err['MonthDayTo'] = 1;
		} elseif (!checkdate(substr($req['monthdayto'],0,2),substr($req['monthdayto'],2,2),2010))
		{
			$msg.= "料金設定期間(終了日)が正しくありません。";
			$this->err['MonthDayFrom'] = 1;
		}
		if (empty($this->err['MonthDayFrom']) && empty($this->err['MonthDayTo'])) {
			if (intval($req['monthdayto']) == intval($req['monthdayfrom'])) {
				$msg.= '料金設定期間の開始日と終了日が同じになっています。<br>';
				$this->err['MonthDayFrom'] = 1;
				$this->err['MonthDayTo'] = 1;
			} elseif (intval($req['monthdayfrom']) > intval($req['monthdayto'])) {
				$msg.= '料金設定期間の開始日が終了日を超えています。<br>';
				$this->err['MonthDayFrom'] = 1;
				$this->err['MonthDayTo'] = 1;
			}
			if (empty($this->err['MonthDayFrom']) && empty($this->err['MonthDayTo'])) {
				list($dCheck,$errDate) = $this->_validateDuplicateKikan($req);
				if (!$dCheck) {
					$msg.= "料金設定期間が他の設定と重複しています。(" . $errDate[0] . ")<br>";
					$this->err['MonthDayFrom'] = 1;
				}
			}
		}
		// 曜日の判定
		if (!isset($req['sunflg']) && !isset($req['monflg'])
			&& !isset($req['tueflg']) && !isset($req['wedflg'])
			&& !isset($req['thuflg']) && !isset($req['friflg'])
			&& !isset($req['satflg']) && !isset($req['holiflg'])) {
			$msg.= "曜日を指定してください。<br>";
			$this->err['SunFlg'] = 1;
		}
		// 他のデータの曜日と判定
		$aDWeek = $this->_getDuplicateWeekArray($req);
		if (isset($req['sunflg']) && $aDWeek['sunflg']) {
			$msg.= "日曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['monflg']) && $aDWeek['monflg']) {
			$msg.= "月曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['tueflg']) && $aDWeek['tueflg']) {
			$msg.= "火曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['wedflg']) && $aDWeek['wedflg']) {
			$msg.= "水曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['thuflg']) && $aDWeek['thuflg']) {
			$msg.= "木曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['friflg']) && $aDWeek['friflg']) {
			$msg.= "金曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['satflg']) && $aDWeek['satflg']) {
			$msg.= "土曜日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}
		if (isset($req['holiflg']) && $aDWeek['holiflg']) {
			$msg.= "祝祭日は既に登録されています。<br>";
			$this->err['SunFlg'] = 1;
		}

		if ($req['minimumusetimeflg'] == '1') {
			if ($req['minimumusetime'] == '') {
				$msg.= "最低利用時間(分)を入力してください。<br>";
				$this->err['MinimumUseTime'] = 1;
			} elseif (!preg_match("/^[0-9]+$/", $req['minimumusetime'])) {
				$msg.= "最低利用時間(分)は数値を入力してください。<br>";
				$this->err['MinimumUseTime'] = 1;
			} 
		}
		if ($req['komaunitflg'] == '1') {
			if ($req['komaunit'] == '') {
				$msg.= "利用コマ単位のコマ数を入力してください。<br>";
				$this->err['KomaUnit'] = 1;
			} elseif (!preg_match("/^[0-9]+$/", $req['komaunit'])) {
				$msg.= "利用コマ単位のコマ数は数値を入力してください。";
				$this->err['KomaUnit'] = 1;
			} elseif ($req['komaunit'] == '0') { 
				$msg.= "利用コマ単位のコマ数は1以上を入力してください。";
				$this->err['KomaUnit'] = 1;
			} 
		}
		if ($req['feeunitflg']=='1') {
			if ($req['feeunit']=='') {
				$msg.= "単位コマ料金のコマ数を入力してください。";
				$this->err['FeeUnit'] = 1;
			} elseif (!preg_match("/^[0-9]+$/", $req['feeunit'])) {
				$msg.= "単位コマ料金のコマ数は数値を入力してください。";
				$this->err['FeeUnit'] = 1;
			} elseif ($req['feeunit']=='0') { 
				$msg.= "単位コマ料金のコマ数は1以上を入力してください。";
				$this->err['FeeUnit'] = 1;
			} 
		}
		if ($req['minimumusefeeflg'] == '1') {
			// 最低利用料金
			$num = count($req['feekbn']);
			for($i = 0; $i < $num; ++$i)
			{
				if ($req['feekbn'][$i] != '0' && $req['minfee'][$i] == '') {
					$msg.= "最低利用料金の".($i+1)."列目を入力してください。<br>";
					$this->err['MinFee'][$i] = 1;
				} elseif (!$this->isNum($req['minfee'][$i])) {
					$msg.= "最低利用料金の".($i+1)."列目は数値を入力してください。<br>";
					$this->err['MinFee'][$i] = 1;
				} elseif ($req['minfee'][$i]>=1000000) {
					$msg.= "最低利用料金の".($i+1)."列目は 1,000,000円より小さい金額を入力してください。<br>";
					$this->err['MinFee'][$i] = 1;
				}
			}
		}			

		if ($req['feetourokukbn'] == '2') {
			// 固定料金
			$num = count($req['feekbn']);
			for($i = 0; $i < $num; ++$i)
			{
				if ($req['feekbn'][$i] != '0' && $req['flatfee'][$i] == '') {
					$msg.= "固定料金の".($i+1)."列目を入力してください。<br>";
					$this->err['FlatFee'][$i] = 1;
				} elseif (!$this->isNum($req['flatfee'][$i])) {
					$msg.= "固定料金の".($i+1)."列目は数値を入力してください。<br>";
					$this->err['FlatFee'][$i] = 1;
				} elseif ($req['flatfee'][$i]>=1000000) {
					$msg.= "固定料金の".($i+1)."列目は 1,000,000円より小さい金額を入力してください。<br>";
					$this->err['FlatFee'][$i] = 1;
				}
			}
		} else {
			$l_idx = 0;
			$num = count($req['feekbn']);
			for($i = 0; $i < $num; ++$i)
			{
				$d_flg = 0;

				if ($req['timefrom'][$i] != '') $d_flg = 1;
				if ($req['timeto'][$i] != '') $d_flg = 1;
				for($j = 0; $j < $num; ++$j)
				{
					if ($req['feekbn'][$j] != '0' && $req['fee'][$i][$j] != '') $d_flg = 1;
				}
				if ($d_flg == 0) continue;

				if ($req['timefrom'][$i] == '') {
					$msg.= "開始時刻(".($i+2)."行目)を入力してください。<br>";
					$this->err['TimeFrom'][$i] = 1;
				} elseif (strlen($req['timefrom'][$i]) !=4 ) {
					$msg.= "開始時刻(".($i+2)."行目)の時刻は4桁で入力してください。<br>";
					$this->err['TimeFrom'][$i] = 1;
				}
				if ($req['timeto'][$i] == '') {
					$msg.= "終了時刻(".($i+2)."行目)を入力してください。<br>";
					$this->err['TimeTo'][$i] = 1;
				} elseif (strlen($req['timeto'][$i]) != 4) {
					$msg.= "終了時刻(".($i+2)."行目)の時刻は4桁で入力してください。<br>";
					$this->err['TimeTo'][$i] = 1;
				}
				if (empty($this->err['TimeFrom'][$i]) && empty($this->err['TimeTo'][$i])) {
					if ($req['timefrom'][$i] >= $req['timeto'][$i]) {
						$msg.= "開始時刻(".($i+2)."行目)が終了時刻を超えています。<br>";
						$this->err['TimeFrom'][$i] = 1;
						$this->err['TimeTo'][$i] = 1;
					}
					if ($req['feetourokukbn']=='1') {
						// 時間重複チェック
						$chk = $this->check_duplicate_time($req, $i);
						if ($chk) {
							$msg.= "時刻(".($i+2)."行目)がその他の時刻と重複しています。<br>";
							$this->err['TimeFrom'][$i] = 1;
							$this->err['TimeTo'][$i] = 1;
						}
					}
				}

				for($j = 0; $j < $num; ++$j)
				{
					if ($req['feekbn'][$j]<>'0' AND $req['fee'][$i][$j]=='') {
						$msg.= "料金(".($i+2)."行目,".($j+1)."列目)を入力してください。<br>";
						$this->err['Fee'][$i][$j] = 1;
					} elseif (!$this->isNum($req['fee'][$i][$j])) {
						$msg.= "料金(".($i+2)."行目,".($j+1)."列目) 数値を入力してください。<br>";
						$this->err['Fee'][$i][$j] = 1;
					} elseif ($req['fee'][$i][$j] >= 1000000) {
						$msg.= "料金(".($i+2)."行目,".($j+1)."列目) 1,000,000円より小さい金額を入力してください。<br>";
						$this->err['Fee'][$i][$j] = 1;
					}
				}
				++$l_idx;
			}
			if ($l_idx==0 && $req['feetourokukbn'] != '2') {
				$msg.= "料金データを入力してください。<br>";
				$this->err['FeeTourokuKbn'] = 1;
			}
		}
		return $msg;
	}
	
	// 時間重複チェック
	function check_duplicate_time(&$req, $index)
	{
		$timeFrom = $req['timefrom'][$index];
		$timeTo = $req['timeto'][$index];
		
		if (!$timeFrom || !$timeTo) return false;
		// 全体とチェック
		$num = count($req['fee']);
		for($i = 0; $i < $num; ++$i)
		{
			// 他の時間帯とチェック
			if ($req['timefrom'][$i]<>'' AND $req['timeto'][$i]<>'' AND $i != $index) {
				$chk = $this->is_duplicate_time($timeFrom, $timeTo, $req['timefrom'][$i], $req['timeto'][$i]);
				// 重複時間あり
				if ($chk) return true;
			}
		}
		// 重複時間なし
		return false;
	}	
	// 2つの時間帯の重複チェック用
	function is_duplicate_time($time1From, $time1To, $time2From, $time2To)
	{
		if (($time1From > $time2From && $time1From < $time2To)
			|| ($time1To > $time2From && $time1To < $time2To)
			|| ($time1From == $time2From && $time1To == $time2To)
			|| (($time1From <= $time2From && $time1To > $time2From)
				&& ($time1From < $time2To && $time1To >= $time2To))
		) { 
			// 重複
			return true;
		}
		return false;
	}
	
	// 他の期間との重複を判定します
	function _validateDuplicateKikan(&$req)
	{
		$sql = "SELECT monthdayfrom, monthdayto FROM m_stjfee 
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=? AND appdatefrom=?
			AND NOT (monthdayfrom=? AND monthdayto=? AND tourokuno=?)
			GROUP BY monthdayfrom, monthdayto";
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->cno, $req['appdatefrom'], $this->prfr, $this->prto, $this->tcd);
		$rs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (count($rs) == 0) return array (true,null);
		$count=0;
		$aDate = array();
		foreach($rs as $value)
		{
			// 重複チェック
			if ($this->is_duplicate_period($req['monthdayfrom'], $req['monthdayto'], $value['monthdayfrom'], $value['monthdayto']))
			{
				++$count;
				array_push($aDate,$value['monthdayfrom'].'-'.$value['monthdayto']);
			}
		}
		if ($count) { return array(false,$aDate);}
		return array(true,null);
	}
	
	// 2つの時間帯の重複チェック用
	function is_duplicate_period($From1, $To1, $From2, $To2)
	{
		// 全く同一の場合はOK
		if ($From1 == $From2 && $To1 == $To2) return false;

		if (($From1 >= $From2 && $From1 <= $To2)
			|| ($To1 >= $From2 && $To1 <= $To2)
			|| (($From1 <= $From2 && $To1 >= $From2)
				&& ($From1 <= $To2 && $To1 >= $To2))
		) { 
			// 重複
			return true;
		}
		return false;
	}	
	
	// 他の同一日の曜日設定のセットを取得します。
	function _getDuplicateWeekArray(&$req)
	{
		$sql = "SELECT SUM(sunflg) AS sunflg,
				SUM(monflg) AS monflg,
				SUM(tueflg) AS tueflg,
				SUM(wedflg) AS wedflg,
				SUM(thuflg) AS thuflg,
				SUM(friflg) AS friflg,
				SUM(satflg) AS satflg,
				SUM(holiflg) AS holiflg  
			FROM m_stjfee 
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?
			AND appdatefrom=? AND monthdayfrom=? AND monthdayto=?";
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->cno, $req['appdatefrom'], $req['monthdayfrom'], $req['monthdayto']);
		// 期間に変更がなければ、登録番号も条件追加
		if ($this->prfr == $req['monthdayfrom'] && $this->prto == $req['monthdayto']) {
			$sql .= " AND tourokuno<>?";
			array_push($aWhere, $this->tcd);
		}
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}
	
	//------------------------------------------------------------
	// 数値チェック
	//------------------------------------------------------------
	function isNum($num)
	{
		if (strlen($num) == 0) return true;
		// マイナス対応
		if (substr($num,0,1) == '-') {
			$checkNum = substr($num,1,strlen($num));
			if (!$checkNum) return false;
		} else {
			$checkNum = $num;
		}
		
		if (!preg_match("/^[0-9.]*$/", $checkNum)) return false;
		return true;
	}

	function check_haishi_date(&$req)
	{
		$msg = '';

		if (trim($req['HaishiDate']) == '') {
			$msg.= '廃止日を入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		} elseif (!preg_match('/^[0-9]{8}$/', $req['HaishiDate'])) {
			$msg.= '廃止日は8桁の半角数字で入力してください。<br>';
			$this->err['HaishiDate'] = 1;
		}
		if (empty($this->err['HaishiDate'])) {
			if (checkdate(substr($req['HaishiDate'],4,2), substr($req['HaishiDate'],6,2), substr($req['HaishiDate'],0,4)) == false) {
				$msg.= '廃止日が正しくありません。<br>';
				$this->err['HaishiDate'] = 1;
			} elseif ($req['HaishiDate'] < $req['appdatefrom']) {
				$msg = '廃止日が適用開始日以前の日付です。<br>';
				$this->err['HaishiDate'] = 1;
			}
		}
		return $msg;
	}
}
?>
