<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  時間割クラス
 *
 *  facility_timetable.class.php
 */

class facility_timetable
{
	private $oDB = null;
	private $con = null;
	private $err = array();
	private $lcd = '';
	private $scd = '';
	private $rcd = '';
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
		$this->apd = isset($req['apd']) ? $req['apd'] : '';
		$this->prfr = isset($req['prfr']) ? $req['prfr'] : '';
		$this->prto = isset($req['prto']) ? $req['prto'] : '';
	}

	function get_stj_timetable_data()
	{
		$sql = 'SELECT * FROM m_stjtimetable';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$sql.= ' AND appdatefrom=? AND monthdayfrom=? AND monthdayto=?';
		$sql.= ' ORDER BY komakbn';
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->apd, $this->prfr, $this->prto);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$para = $res[0];
		unset($para['komakbn']);
		unset($para['komaname']);

		for ($i = 0; $i < _MAX_KOMA_; ++$i)
		{
			$para['komakbn'][$i] = sprintf('%02d',$i);
			$para['komakbntimefrom'][$i] = '';
			$para['komakbntimeto'][$i] = '';
		}

		$komaClass = $res[0]['komaclass'];
		if ($komaClass == '1') {
			$para['komaname'][] = '';
			$para['komanametimefrom'][] = '';
			$para['komanametimeto'][] = '';
		} elseif ($komaClass == '2') {
			foreach ($res as $val)
			{
				$key = intval($val['komakbn']);
				$para['komakbntimefrom'][$key] = $val['komatimefrom'];
				$para['komakbntimeto'][$key] = $val['komatimeto'];
			}
			$para['komaname'][] = '';
			$para['komanametimefrom'][] = '';
			$para['komanametimeto'][] = '';
		} elseif ($komaClass == '3') {
			foreach ($res as $val)
			{
				$para['komaname'][] = $val['komaname'];
				$para['komanametimefrom'][] = $val['komatimefrom'];
				$para['komanametimeto'][] = $val['komatimeto'];
			}
		}
		unset($res);
		return $para;
	}

	function delete_stj_timetable()
	{
		$sql = 'DELETE FROM m_stjtimetable';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$sql.= ' AND appdatefrom=? AND monthdayfrom=? AND monthdayto=?';
		$aWhere = array($this->lcd, $this->scd, $this->rcd, $this->apd, $this->prfr, $this->prto);
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;
		return true;
	}

	function insert_stj_timetable(&$req)
	{
		$dataset = array();
		$dataset['localgovcode'] = $this->lcd;
		$dataset['shisetsucode'] = $this->scd;
		$dataset['shitsujyocode'] = $this->rcd;
		$dataset['appdatefrom'] = $req['appdatefrom'];
		$dataset['monthdayfrom'] = $req['monthdayfrom'];
		$dataset['monthdayto'] = $req['monthdayto'];
		$dataset['komaclass'] = $req['komaclass'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		if ($req['komaclass'] == '1') {
			//コマ割
			$dataset['kaijoutime'] = $req['kaijoutime'];
			$dataset['heijoutime'] = $req['heijoutime'];
			$dataset['komatanitime'] = $req['komatanitime'];
			$dataset['komatanitimekbn'] = $req['komatanitimekbn'];
			$dataset['komakbn'] = '00';
			$rc = $this->oDB->insert('m_stjtimetable', $dataset);
			if ($rc < 0) {
				return false;
			}
		} elseif ($req['komaclass'] == '2') {
			//時間割
			foreach ($req['komakbn'] as $key => $val)
			{
				if ($req['komakbntimefrom'][$key] != '' && $req['komakbntimeto'][$key] != '') {
					$dataset['komakbn'] = $val;
					$dataset['komatimefrom'] = $req['komakbntimefrom'][$key].'00';
					$dataset['komatimeto'] = $req['komakbntimeto'][$key].'00';
					$dataset['komaname'] = $val;
					if ($key == '00') {
						$dataset['komaname'] = '終日';
					}
					$rc = $this->oDB->insert('m_stjtimetable', $dataset);
					if ($rc < 0) return false;
				}
			}
		} elseif ($req['komaclass'] == '3') {
			//区分名
			$i = 1;
			foreach ($req['komaname'] as $key => $val)
			{
				if ($req['komanametimefrom'][$key] != '' && $req['komanametimeto'][$key] != '') {
					$dataset['komakbn'] = sprintf('%02d', $i);
					$dataset['komatimefrom'] = $req['komanametimefrom'][$key].'00';
					$dataset['komatimeto'] = $req['komanametimeto'][$key].'00';
					$dataset['komaname'] = $val;
					$rc = $this->oDB->insert('m_stjtimetable', $dataset);
					if ($rc < 0) return false;
					++$i;
				}
			}
		}
		return true;
	}

	function expire_stj_timetable(&$req)
	{
		$dataset = array();
		$dataset['haishidate'] = $req['HaishiDate'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updiD'] = $_SESSION['userid'];

		$where = "localgovcode='".$this->lcd
			."' AND shisetsucode='".$this->scd
			."' AND shitsujyocode='".$this->rcd
			."' AND appdatefrom='".$this->apd
			."' AND monthdayfrom='".$this->prfr
			."' AND monthdayto='".$this->prto."'";

		$rc = $this->oDB->update('m_stjtimetable', $dataset, $where);
		if ($rc < 0) return false;
		return true;
	}

	// 時間オーバーチェック
	function check_over_time($data, $index)
	{
		$timeFrom = $data['komakbntimefrom'][$index];
		if (!$timeFrom) return false;

		// 全体とチェック
		for($i=0; $i < _MAX_KOMA_; ++$i)
		{
			// 他の時間帯とチェック
			if ($data['komakbntimefrom'][$i]<>'' && $data['komakbntimeto'][$i]<>'' && $i > $index) {
				if ($timeFrom > $data['komakbntimefrom'][$i]) {
					return true;
				}
			}
		}
		// 重複時間なし
		return false;
	}

	// 時間オーバーチェック(コマ種別３の場合)
	function check_over_time3($data, $index)
	{
		$timeFrom =  $data['komanametimefrom'][$index];
		if (!$timeFrom) return false;

		// 全体とチェック
		$num = count($data['komaname']);
		for($i = 0; $i < $num; ++$i)
		{
			// 他の時間帯とチェック
			if ($data['komanametimefrom'][$i]<>'' && $data['komanametimeto'][$i]<>'' && $i > $index) {
				if ($timeFrom > $data['komanametimefrom'][$i]) {
					return true;
				}
			}
		}
		// 重複時間なし
		return false;
	}

	// 時間重複チェック
	function check_duplicate_time($data, $index)
	{
		$timeFrom = $data['komakbntimefrom'][$index];
		$timeTo = $data['komakbntimeto'][$index];
		if (!$timeFrom || !$timeTo) return false;

		// 全体とチェック
		for($i=0; $i< _MAX_KOMA_; ++$i)
		{
			// 終日は対象外
			if ($i != 0) {
				// 他の時間帯とチェック
				if ($data['komakbntimefrom'][$i]<>'' && $data['komakbntimeto'][$i]<>'' && $i != $index) {
					$chk = $this->is_duplicate_time($timeFrom, $timeTo, $data['komakbntimefrom'][$i], $data['komakbntimeto'][$i]);
					// 重複時間あり
					if ($chk) return true;
				}
			}
		}
		// 重複時間なし
		return false;
	}

	// 時間重複チェック(コマ種別３）
	function check_duplicate_time3($data, $index)
	{
		$timeFrom = $data['komanametimefrom'][$index];
		$timeTo = $data['komanametimeto'][$index];
		if (!$timeFrom || !$timeTo) return false;

		// 全体とチェック
		$num = count($data['komaname']);
		for($i = 0; $i < $num; ++$i)
		{
			// 他の時間帯とチェック
			if ($data['komanametimefrom'][$i]<>'' && $data['komanametimeto'][$i]<>'' && $i != $index) {
				$chk = $this->is_duplicate_time($timeFrom, $timeTo, $data['komanametimefrom'][$i], $data['komanametimeto'][$i]);
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
			&& ($time1From < $time2To && $time1To >= $time2To)))
		{
			// 重複
			return true;
		}
		return false;
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req, $mode)
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
			$msg.= '設定期間(開始日)を入力してください。<br>';
			$this->err['MonthDayFrom'] = 1;
		} elseif (!preg_match('/^[0-9]{4}$/', $req['monthdayfrom']) || checkdate(substr($req['monthdayfrom'],0,2),substr($req['monthdayfrom'],2,2),2010) == false) {
			$msg.= '設定期間(開始日)が正しくありません。<br>';
			$this->err['MonthDayFrom'] = 1;
		}
		if (trim($req['monthdayto']) == '') {
			$msg.= '設定期間(終了日)を入力してください。<br>';
			$this->err['MonthDayTo'] = 1;
		}
		elseif (!preg_match('/^[0-9]{4}$/', $req['monthdayto']) || checkdate(substr($req['monthdayto'],0,2),substr($req['monthdayto'],2,2),2010) == false) {
			$msg.= '設定期間(終了日)が正しくありません。<br>';
			$this->err['MonthDayTo'] = 1;
		}
		if (empty($this->err['MonthDayFrom']) && empty($this->err['MonthDayTo'])) {
			if (intval($req['monthdayto']) == intval($req['monthdayfrom'])) {
				$msg.= '設定期間の開始日と終了日が同じになっています。<br>';
				$this->err['MonthDayFrom'] = 1;
				$this->err['MonthDayTo'] = 1;
			} elseif (intval($req['monthdayfrom']) > intval($req['monthdayto'])) {
				$msg.= '設定期間の開始日が終了日を超えています。<br>';
				$this->err['MonthDayFrom'] = 1;
				$this->err['MonthDayTo'] = 1;
			}
			if (empty($this->err['MonthDayFrom']) && empty($this->err['MonthDayTo'])) {
				$sql = "SELECT COUNT(updid) FROM m_stjtimetable
					WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
					AND ((monthdayfrom<=? AND monthdayto>=?)
						OR (monthdayfrom<=? AND monthdayto>=?)
						OR (monthdayfrom>=? AND monthdayto<=?))
					AND appdatefrom<=?
					AND (haishidate>? OR haishidate='' OR haishidate IS NULL)";
				$aWhere = array(
					_CITY_CODE_, $req['scd'], $req['rcd'],
					$req['monthdayfrom'], $req['monthdayfrom'],
					$req['monthdayto'], $req['monthdayto'],
					$req['monthdayfrom'], $req['monthdayto'],
					$req['appdatefrom'], $req['appdatefrom']
				);
				if ($mode == 'mod') {
					$sql.=" AND (monthdayfrom<>? AND monthdayto<>?)";
					array_push($aWhere,$req['prfr']);
					array_push($aWhere,$req['prto']);
				}
				$res = $this->con->getOne($sql, $aWhere);
				if ($res > 0) {
					$msg.= '設定期間が他の時間割と重複しています。<br>';
					$this->err['MonthDayFrom'] = 1;
					$this->err['MonthDayTo'] = 1;
				}
			}
		}

		if ($req['komaclass'] == '1')
		{
			if ($req['kaijoutime'] == '') {
				$msg.= '開場時刻を入力してください。<br>';
				$this->err['KaijouTime'] = 1;
			} elseif (!preg_match('/^[0-9]{4}$/', $req['kaijoutime'])) {
				$msg.= '開場時刻の時刻は4桁の半角数字で入力してください。<br>';
				$this->err['KaijouTime'] = 1;
			}
			if ($req['heijoutime'] == '') {
				$msg.= '閉場時刻を入力してください。<br>';
				$this->err['HeijouTime'] = 1;
			} elseif (!preg_match('/^[0-9]{4}$/', $req['heijoutime'])) {
				$msg.= '閉場時刻の時刻は4桁の半角数字で入力してください。<br>';
				$this->err['HeijouTime'] = 1;
			}
			if (empty($this->err['KaijouTime']) && empty($this->err['HeijouTime'])) {
				if (intval($req['kaijoutime']) > intval($req['heijoutime'])) {
					$msg.= '開場時刻が閉場時刻を越えています。<br>';
					$this->err['KaijouTime'] = 1;
					$this->err['HeijouTime'] = 1;
				} elseif (intval($req['kaijoutime']) == intval($req['heijoutime'])) {
					$msg.= '開場時刻と閉場時刻が同じです。<br>';
					$this->err['KaijouTime'] = 1;
					$this->err['HeijouTime'] = 1;
				}
			}
			if ($req['komatanitime'] == '') {
				$msg.= 'コマ単位時間を入力してください。<br>';
				$this->err['KomaTaniTime'] = 1;
			} else {
				if (!preg_match("/^[0-9]+$/", $req['komatanitime'])) {
					$msg.= 'コマ単位時間は半角数字を入力してください。<br>';
					$this->err['KomaTaniTime'] = 1;
				} elseif ($req['komatanitime'] == 0) {
					$msg.= 'コマ単位時間は1以上の整数値を入力してください。<br>';
					$this->err['KomaTaniTime'] = 1;
				}
			}
		}
		elseif ($req['komaclass'] == '2')
		{
			$idx=0;
			for($i=0; $i<_MAX_KOMA_; ++$i)
			{
				$lenFrom = strlen($req['komakbntimefrom'][$i]);
				$lenTo = strlen($req['komakbntimeto'][$i]);
				if ($lenFrom != 0) {
					if($lenTo == 0) {
						$msg.= sprintf('%02d', $i)."の終了時刻を入力してください。<br>";
						$this->err['KomaKbnTimeTo'][$i] = 1;
					}
					if (!preg_match('/^[0-9]{4}$/', $req['komakbntimefrom'][$i])) {
						$msg.= sprintf('%02d', $i)."の開始時刻は4桁の半角数字で入力してください。<br>";
						$this->err['KomaKbnTimeFrom'][$i] = 1;
					} elseif (intval($req['komakbntimefrom'][$i]) >= 2400) {
						$msg.= sprintf('%02d', $i).'の開始時刻を2400未満で入力しください。<br>';
						$this->err['KomaKbnTimeFrom'][$i] = 1;
					}
				}
				if ($lenTo != 0) {
					if($lenFrom == 0) {
						$msg.= sprintf('%02d', $i)."の開始時刻を入力してください。<br>";
						$this->err['KomaKbnTimeFrom'][$i] = 1;
					}
					if (!preg_match('/^[0-9]{4}$/', $req['komakbntimeto'][$i])) {
						$msg.= sprintf('%02d', $i)."の終了時刻は4桁の半角数字で入力してください。<br>";
						$this->err['KomaKbnTimeTo'][$i] = 1;
					} elseif (intval($req['komakbntimeto'][$i]) > 2400) {
						$msg.= sprintf('%02d', $i).'の終了時刻を2400以下で入力しください。<br>';
						$this->err['KomaKbnTimeTo'][$i] = 1;
					}
				}
				if ($lenFrom != 0 && $lenTo != 0 && empty($this->err['KomaKbnTimeFrom'][$i]) && empty($this->err['KomaKbnTimeFrom'][$i])) {
					if ($req['komakbntimefrom'][$i] >= $req['komakbntimeto'][$i]) {
						$msg.= sprintf('%02d', $i)."の開始時刻が終了時刻を超えています。<br>";
						$this->err['KomaKbnTimeFrom'][$i] = 1;
					}
					if ($i != 0) {
						// 時間重複チェック
						$chk = $this->check_duplicate_time($req, $i);
						if ($chk) {
							$msg.= sprintf('%02d', $i)."の時刻が他の区分の時刻と重複しています。<br>";
							$this->err['KomaKbnTimeFrom'][$i] = 1;
							$this->err['KomaKbnTimeTo'][$i] = 1;
						}
						// 時間オーバーチェック
						$chk = $this->check_over_time($req, $i);
						if ($chk) {
							$msg.= sprintf('%02d', $i)."の時刻が後方の時刻を超えています。<br>";
							$this->err['KomaKbnTimeFrom'][$i] = 1;
							$this->err['KomaKbnTimeTo'][$i] = 1;
						}
					}
				}
				if ($req['komakbntimefrom'][$i] != '' && $req['komakbntimeto'][$i] != '') {
					$idx++;
				}
			}
			if ($idx == 0) {
				$msg.= "時間割データを入力してください。<br>";
				$this->err['komakbn'] = 1;
			}
		}
		elseif ($req['komaclass'] == '3')
		{
			$lines = count($req['komaname']);
			$idx = 0;
			for($i=0; $i<$lines; ++$i)
			{
				$lenFrom = strlen($req['komanametimefrom'][$i]);
				$lenTo = strlen($req['komanametimeto'][$i]);
				if ($req['komaname'][$i] == '' && ($lenFrom  != 0 || $lenTo != 0)) {
					$msg.= "区分名(".($i+1)."行目)を入力してください。<br>";
					$this->err['KomaName'][$i] = 1;
				}
				if ($lenFrom != 0) {
					if($lenTo == 0) {
						$msg.= "終了時刻(".($i+1)."行目)を入力してください。<br>";
						$this->err['KomaNameTimeTo'][$i] = 1;
					}
					if (!preg_match('/^[0-9]{4}$/', $req['komanametimefrom'][$i])) {
						$msg.= "開始時刻(".($i+1)."行目)は4桁の半角数字で入力してください。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
					} elseif (intval($req['komanametimefrom'][$i]) >= 2400) {
						$msg.= "開始時刻(".($i+1)."行目)はを2400未満で入力しください。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
					}
				}
				if ($lenTo != 0) {
					if($lenFrom == 0) {
						$msg.= "開始時刻(".($i+1)."行目)を入力してください。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
					}
					if (!preg_match('/^[0-9]{4}$/', $req['komanametimeto'][$i])) {
						$msg.= "終了時刻(".($i+1)."行目)は4桁の半角数字で入力してください。<br>";
						$this->err['KomaNameTimeTo'][$i] = 1;
					} elseif (intval($req['komanametimeto'][$i]) > 2400) {
						$msg.= "終了時刻(".($i+1)."行目)はを2400以下で入力しください。<br>";
						$this->err['KomaNameTimeTo'][$i] = 1;
					}
				}
				if ($lenFrom != 0 && $lenTo != 0 && empty($this->err['KomaNameTimeFrom'][$i]) && empty($this->err['KomaNameTimeFrom'][$i])) {
					if ($req['komanametimefrom'][$i] >= $req['komanametimeto'][$i]) {
						$msg.= "開始時刻(".($i+1)."行目)が終了時刻の後になっています。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
					}
					// 時間重複チェック
					$chk = $this->check_duplicate_time3($req, $i);
					if ($chk) {
						$msg.= "時刻(".($i+1)."行目)がその他の時刻と重複しています。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
						$this->err['KomaNameTimeTo'][$i] = 1;
					}
					// 時間オーバーチェック
					$chk = $this->check_over_time3($req, $i);
					if ($chk) {
						$msg.= "時刻(".($i+1)."行目)が後方の時刻を超えています。<br>";
						$this->err['KomaNameTimeFrom'][$i] = 1;
						$this->err['KomaNameTimeTo'][$i] = 1;
					}
				}
				if ($req['komanametimefrom'][$i] != '' && $req['komanametimeto'][$i] != '') {
					$idx++;
				}
			}
			if ($idx == 0) {
				$msg.= "区分指定のデータを入力してください。<br>";
				$this->err['KomaName'] = 1;
			}
			if ($lines > 26) {
				$msg.= "26行を超える区分指定はできません。<br>";
				$this->err['KomaName'] = 1;
			}
		}
		return $msg;
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
