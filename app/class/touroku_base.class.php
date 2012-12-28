<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約登録基本クラス
 *
 *  touroku_base.class.php
 */

class touroku_base
{
	protected $oDB = null;
	protected $con = null;
	protected $updid = '';
	private $lcd = '';

	function __construct(&$oDB, $updid)
	{
		$this->oDB = $oDB;
		$this->con = $oDB->getCon();
		$this->updid = $updid;
		$this->lcd = _CITY_CODE_;
	}

	//-------------------------------------------------------------------
	// データを予約番号で更新
	//-------------------------------------------------------------------
	function update_by_yoyakunum($table_name, &$dataset, $YoyakuNum)
	{
		$where = "localgovcode='".$this->lcd
			."' AND yoyakunum='".$YoyakuNum."'";
		$rs = $this->con->autoExecute($table_name, $dataset, DB_AUTOQUERY_UPDATE, $where);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// 予約データ更新
	//-------------------------------------------------------------------
	function update_yoyaku_by_code(&$dataset, $YoyakuNum, $scd, $rcd, $mcd)
	{
		$where = "localgovcode='".$this->lcd
			."' AND shisetsucode='".$scd
			."' AND shitsujyocode='".$rcd
			."' AND mencode='".$mcd
			."' AND yoyakunum='".$YoyakuNum."'";
		$rs = $this->con->autoExecute('t_yoyaku', $dataset, DB_AUTOQUERY_UPDATE, $where);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// データを予約番号で削除
	//-------------------------------------------------------------------
	function delete_by_yoyakunum($table_name, $YoyakuNum)
	{
		$sql = "DELETE FROM {$table_name}
			WHERE localgovcode=? AND yoyakunum=?";
		$aWhere = array($this->lcd, $YoyakuNum);
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// 抽選データ削除
	//-------------------------------------------------------------------
	function delete_pulloutyoyaku($YoyakuNum)
	{
		$sql = 'DELETE FROM t_pulloutyoyaku 
			WHERE localgovcode=? AND pulloutyoyakunum=?';
		$aWhere = array($this->lcd, $YoyakuNum);
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// 予約管理データ更新
	//-------------------------------------------------------------------
	function update_yoyakukanri_flg(&$dataset, $flg)
	{
		$sql = "SELECT * FROM t_yoyakukanri
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			AND mencode=? AND usedate=? FOR UPDATE";
		$aWhere = array($dataset['localgovcode'], $dataset['shisetsucode'],
				$dataset['shitsujyocode'], $dataset['mencode'],
				$dataset['usedate']);
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (empty($rec)) return 0;

		for ($i = 1; $i < 51; ++$i)
		{
			$kbn = sprintf('%02d', $i);
			$timeFrom = $rec['usetimefrom'.$kbn];
			$timeTo = $rec['usetimeto'.$kbn];
			if (!$timeFrom) break;
			if ($timeFrom >= $dataset['usetimefrom'] && $timeTo <= $dataset['usetimeto']) {
				$rec['komaflg'.$kbn] = $flg;
			}
		}
		$rec['upddate'] = date('Ymd');
		$rec['updtime'] = date('His');
		$rec['updid'] = $this->updid;

		$where = "localgovcode='".$dataset['localgovcode']
			."' AND shisetsucode='".$dataset['shisetsucode']
			."' AND shitsujyocode='".$dataset['shitsujyocode']
			."' AND mencode='".$dataset['mencode']
			."' AND usedate='".$dataset['usedate']."'";
		$rs = $this->con->autoExecute('t_yoyakukanri', $rec, DB_AUTOQUERY_UPDATE, $where);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// 予約キャンセル処理
	//-------------------------------------------------------------------
	function cancel_yoyaku($YoyakuNum, $CancelCode, $Bikou)
	{
		$BihinNum = '';

		$this->con->autoCommit(false);

		// 予約申請テーブル削除
		$rc = $this->insert_fee_history($YoyakuNum, $BihinNum);
		if ($rc < 0) {
			$this->con->rollback();
			return $rc;
		}
		if ($rc > 0) {
			$rc = $this->delete_by_yoyakunum('t_yoyakufeeshinsei', $YoyakuNum);
			if ($rc < 0) {
				$this->con->rollback();
				return $rc;
			}
			if (trim($BihinNum) != '') {
				// 備品予約取消
				$this->cancel_bihin($BihinNum);
			}
		}

		// 予約テーブル削除
		$rc = $this->insert_yoyaku_history($YoyakuNum, $CancelCode, $Bikou);
		if ($rc < 0) {
			$this->con->rollback();
			return $rc;
		}
		if ($rc > 0) {
			$rc = $this->delete_by_yoyakunum('t_yoyaku', $YoyakuNum);
			if ($rc < 0) {
				$this->con->rollback();
				return $rc;
			}
		}

		// 抽選テーブル削除処理
		$rc = $this->insert_lots_history($YoyakuNum);
		if ($rc < 0) {
			$this->con->rollback();
			return $rc;
		}
		if ($rc > 0) {
			$rc = $this->delete_pulloutyoyaku($YoyakuNum);
			if ($rc < 0) {
				$this->con->rollback();
				return $rc;
			}
		}

		$this->con->commit();

		return true;
	}

	//-------------------------------------------------------------------
	// 該当する予約テーブルのデータを履歴テーブルに格納する
	//-------------------------------------------------------------------
	function insert_yoyaku_history($YoyakuNum, $CancelCode, $Bikou)
	{
		$sql = 'SELECT *, usedatefrom AS usedate
			FROM t_yoyaku WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array($this->lcd, $YoyakuNum);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (empty($res)) return 0;

		foreach ($res as $val)
		{
			if (_YOYAKUKANRI_TABLE_ && $val['honyoyakukbn'] != '04') {
				$rc = $this->update_yoyakukanri_flg($val, 0);
				if ($rc < 0) return $rc;
			}

			unset($val['usedate']);

			if ($CancelCode == '00') {
				$val['utensinflg'] = '1';
			}
			$val['canceljiyucode'] = $CancelCode;
			$val['cancelstaffid'] = $this->updid;
			$val['lstupddate'] = date('Ymd');
			$val['lstupdtime'] = date('His');
			$val['lstupdid'] = $this->updid;
			if ($Bikou) $val['bikou'] = $Bikou;
			$rs = $this->con->autoExecute('h_yoyaku', $val, DB_AUTOQUERY_INSERT);
			$rc = $this->oDB->check_error($rs);
			if ($rc < 0) return $rc;
		}
		return true;
	}

	//-------------------------------------------------------------------
	// 予約申請履歴テーブルへ移動
	//-------------------------------------------------------------------
	function insert_fee_history($YoyakuNum, &$BihinNum)
	{
		$paykbn_conv = array(0, 0, 2, 5, 5, 5, 6, 7, 8);

		$sql = 'SELECT * FROM t_yoyakufeeshinsei WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array($this->lcd, $YoyakuNum);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (empty($row)) return 0;

		$BihinNum = $row['bihinyoyakunum'];
		$row['paykbn'] = $paykbn_conv[$row['paykbn']];
		$row['lstupddate'] = date('Ymd');
		$row['lstupdtime'] = date('His');
		$row['lstupdid'] = $this->updid;
		$rs = $this->con->autoExecute('h_fee', $row, DB_AUTOQUERY_INSERT);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//-------------------------------------------------------------------
	// 抽選履歴テーブルへ移動
	//-------------------------------------------------------------------
	function insert_lots_history($YoyakuNum)
	{
		$aWhere = array($this->lcd, $YoyakuNum);
		$sql = 'SELECT * FROM t_pulloutyoyaku Where localgovcode=? AND pulloutyoyakunum=?';
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (empty($res)) return 0;

		foreach($res as $val)
		{
			$val['lstupddate'] = date('Ymd');
			$val['lstupdtime'] = date('His');
			$val['lstupdid'] = $this->updid;
			$rs = $this->con->autoExecute('h_pullout', $val, DB_AUTOQUERY_INSERT);
			$rc = $this->oDB->check_error($rs);
			if ($rc < 0) return $rc;
		}
		return true;
	}

	//-------------------------------------------------------------------
	// 備品予約取消
	//-------------------------------------------------------------------
	function cancel_bihin($YoyakuNum)
	{
		$dataset = array();
		$dataset['yoyakustatus'] = '3';
		$dataset['upddate'] = time();
		$dataset['updid'] = $this->updid;
		$where = "localgovcode='"._CITY_CODE_
			."' AND yoyakunum='".$YoyakuNum."'";

		$rc = $this->oDB->update('t_bihinyoyaku', $dataset, $where);
		if ($rc < 0) return false;
		return true;
	}

	//--------------------------------------------------------------------
	// 未登録者情報の登録
	//--------------------------------------------------------------------
	function insert_unregisted_user($YoyakuNum, $info)
	{
		$dataset = array();
		$dataset['yoyakunum'] = $YoyakuNum;
		$dataset['username'] = $info['UnregUserName'];
		$dataset['address'] = $info['UnregAddress'];
		$dataset['telno'] = $info['UnregTel'];
		$dataset['contactno'] = $info['UnregContact'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $this->updid;

		$rs = $this->con->autoExecute('t_unregister', $dataset, DB_AUTOQUERY_INSERT);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return $rc;
		return true;
	}

	//--------------------------------------------------------------------
	// 当選確定数のチェック
	//--------------------------------------------------------------------
	function check_fix_limit($YoyakuNum, $PersonKbn)
	{
		$sql = "SELECT p.*, s.pulloutfixlimitflg, s.pulloutfixlimitkbn,
		s.pulloutfixlimitkojin, s.pulloutfixlimitdantai, c.shisetsuclasscode
		FROM t_pulloutyoyaku p 
		JOIN m_yoyakuscheduleptn s
		USING (localgovcode, shisetsucode, shitsujyocode)
		JOIN m_shisetsu c
		USING (localgovcode, shisetsucode)
		WHERE p.localgovcode=? AND p.pulloutyoyakunum=?
		ORDER BY shitsujyocode, mencode";
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC); 

		if ($row['pulloutfixlimitflg'] == '0') {
			return true;
		}
		elseif ($row['pulloutfixlimitflg'] == '1') {
			if ($row['pulloutfixlimitkbn'] == '1') {
				$sql = "SELECT COUNT(DISTINCT pulloutyoyakunum)";
			} else {
				$sql = "SELECT DISTINCT pullOutyoyakunum, komasu";
			}
			$sql.= " FROM t_pulloutyoyaku"
				." WHERE userid=? AND localgovcode=? AND shisetsucode=?"
				." AND shitsujyocode=? AND pulloutjisshidate=?"
				." AND hitfixappdate<>'' AND pulloutjouKyoukbn='3'";
			$aWhere = array($row['userid'], $row['localgovcode'],
					$row['shisetsucode'], $row['shitsujyocode'], $row['pulloutjisshidate']);
		}
		elseif($row['pulloutfixlimitflg'] == '2') {
			if ($row['pulloutfixlimitkbn'] == '1') {
				$sql = "SELECT COUNT(DISTINCT pulloutyoyakunum)";
			} else {
				$sql = "SELECT DISTINCT pulloutyoyakunum, komasu";
			}
			$sql.= " FROM t_pulloutyoyaku"
				." WHERE userid=? AND localgovcode=? AND shisetsucode=?"
				." AND pulloutjisshidate=?"
				." AND hitfixappdate<>'' AND pulloutjoukyoukbn='3'";
			$aWhere = array($row['userid'], $row['localgovcode'],
					$row['shisetsucode'], $row['pulloutjisshidate']);
		}
		elseif($row['pulloutfixlimitflg'] == '3') {
			if ($row['pulloutfixlimitkbn'] == '1') {
				$sql = "SELECT COUNT(DISTINCT p.pulloutyoyakunum)";
			} else {
				$sql = "SELECT DISTINCT p.pulloutyoyakunum, p.komasu";
			}
			$sql.= " FROM t_pulloutyoyaku p"
				." JOIN m_shisetsu s USING (localgovcode, shisetsucode)"
				." WHERE p.userid=? AND p.localgovcode=? AND s.shisetsuclasscode=?"
				." AND p.pulloutjisshidate=?"
				." AND p.hitfixappdate<>'' AND p.pulloutjoukyoukbn='3'";
			$aWhere = array($row['userid'], $row['localgovcode'],
					$row['shisetsuclasscode'], $row['pulloutjisshidate']);
		}
		$count = 0;
		if ($row['pulloutfixlimitkbn'] == '1') {
			$count = $this->con->getOne($sql, $aWhere)+1;
		} else {
			$rst = $this->con->query($sql, $aWhere);
			$count = 0;
			while ($row = $rst->fetchRow(DB_FETCHMODE_ASSOC))
			{
				$count += $row['komasu'];
			}
			$count += $row['komasu'];
		}
		if($PersonKbn == '1') {
			if($row['pulloutfixlimitkojin'] < $count) {
				return false;
			}
		} else {
			if($row['pulloutfixlimitdantai'] < $count) {
				return false;
			}
		}
		return true;
	}

	//--------------------------------------------------------------------
	// 当選確定処理
	//--------------------------------------------------------------------
	function fix_win($YoyakuNum)
	{
		$sql = "SELECT s.kariyoyakuflg FROM m_yoyakuscheduleptn s
			JOIN t_pulloutyoyaku p
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE p.localgovcode=? AND p.pulloutyoyakunum=?";
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$KariYoyakuFlg = $this->con->getOne($sql, $aWhere);

		$this->con->autoCommit(false);

		$dataset = array();
		$dataset['hitfixappdate'] = date('Ymd'); 
		$dataset['hitfixapptime'] = date('His');
		$dataset['upddate'] = date('Ymd'); 
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $this->updid;
		$where = "localgovcode='"._CITY_CODE_
			."' AND pulloutyoyakunum='".$YoyakuNum
			."' AND pulloutjoukyoukbn='3'";
		$rs = $this->con->autoExecute('t_pulloutyoyaku', $dataset, DB_AUTOQUERY_UPDATE, $where);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			$this->con->rollback();
			return false;
		}

		$dataset = array();
		$dataset['appdate'] = date('Ymd'); 
		$dataset['apptime'] = date('His');
		$dataset['upddate'] = date('Ymd'); 
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $this->updid;
		$rc = $this->update_by_yoyakunum('t_yoyaku', $dataset, $YoyakuNum);
		if ($rc < 0) {
			$this->con->rollback();
			return false;
		}

		$this->con->commit();
		return true;
	}

	/*
	 * 申込可否を判断する
	 * 4 = 抽選申込不可
	 * 6 = 予約不可
	 * 10 = 予約可
	 * 11 = 抽選（受付中）
	 * 上記戻り値が変更された場合、completeYoyaku()のerrmsgを確認のこと。
	 */
	function check_duplicate_lot(&$ses)
	{
		$menSql = " AND (mencode='".implode("' OR mencode='", $ses['mencode'])."')";
		$sql = "SELECT COUNT(DISTINCT pulloutyoyakunum)
			FROM t_pulloutyoyaku
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? ".$menSql." AND usedate=?
			AND usetimefrom=? AND usetimeto=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $ses['shisetsucode'],
				$ses['shitsujyocode'],
				$ses['usedate'], $ses['usetimefrom'],
				$ses['usetimeto'], $ses['userid']);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res == 0) return 11;

		return 4;
	}

	function check_duplicate_yoyaku(&$ses, $without_self=false)
	{
		$aWhere = array(_CITY_CODE_, $ses['shisetsucode'],
				$ses['shitsujyocode'], $ses['usedate'],
				$ses['usetimeto'], $ses['usetimefrom']);

		$sql = "SELECT COUNT(DISTINCT yoyakunum) FROM t_yoyaku
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND usedatefrom=?
			AND usetimefrom<? AND usetimeto>?
			AND honyoyakukbn<>'04' AND shinsakbn<>'2'";
		if ($without_self) {
			$sql.= " AND yoyakunum<>?";
			array_push($aWhere, $ses['yoyakunum']);
		}
		$menSql = " AND (mencode='".implode("' OR mencode='", $ses['mencode'])."')";
		$st = $sql.$menSql;

		$res = $this->con->getOne($st, $aWhere);
		if ($res != 0) return 6;

		if (isset($ses['Fuzoku'])) {
			$menSql = " AND mencode='ZZ'";
			$st = $sql.$menSql;
			foreach ($ses['Fuzoku'] as $val)
			{
				$aWhere[2] = $val;
				$res = $this->con->getOne($st, $aWhere);
				if ($res != 0) return 6;
			}
		}

		return 10;
	}

	function check_ninzu(&$req, &$ses, $detailFlg, $zero_accept=true)
	{
		global $aNinzu;

		$msg = '';
		$UseNinzu = $req['useninzu'];
		$ninzu_arr = array();

		foreach ($aNinzu as $key => $val)
		{
			if (isset($req[$key])) $ninzu_arr[$key] = array($req[$key], $val[1]);
		}

		$sql = "SELECT teiin FROM m_shitsujyou
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=?";

		if ($ses['combino'] != 0) {
			$menSql = " AND (mencode='".implode("' OR mencode='", $ses['mencode'])."')";
			$sql = "SELECT SUM(teiin) FROM m_men
				WHERE localgovcode=? AND shisetsucode=?
				AND shitsujyocode=? ".$menSql;
		}
		$aWhere = array(_CITY_CODE_, $ses['shisetsucode'], $ses['shitsujyocode']);
		$checkTotalTeiin = $this->con->getOne($sql, $aWhere);

		if ($detailFlg)
		{
			$checkTotal = 0;
			foreach ($ninzu_arr as $key => $val)
			{
				if (is_numeric($val[0]) == false) {
					$msg.= $aNinzu[$key].'は半角数字で入力してください。<br>';
				} elseif ($val[1]) {
					$checkTotal += $val[0];
				}
			}
			if ($msg == '') {
				if ($checkTotal == 0 && !$zero_accept) {
					$msg.= '利用人数を入力してください。<br>';
				} elseif ($checkTotal > $checkTotalTeiin) {
					$msg.= '合計した利用人数が利用可能な最大人数を超えています。<br>';
				}
			}
		} else {
			if (is_numeric($UseNinzu) == false) {
				$msg.= '利用人数は半角数字で入力してください。<br>';
			} elseif (empty($UseNinzu) && !$zero_accept) {
				$msg.= '利用人数を入力してください。<br>';
			} elseif (intval($UseNinzu) > $checkTotalTeiin) {
				$msg.= '入力した利用人数が利用可能な最大人数を超えています。<br>';
			}
		}
		return $msg;
	}

	//------------------------------------------------------------
	// キャンセルマスターより値を取得する
	//------------------------------------------------------------
	function get_cancel_options($rate=false)
	{
		$sql = "SELECT cancelcode, canceljiyuname, rate FROM m_canceljiyucode ORDER BY cancelcode";
		$res = $this->con->getAll($sql);
		$recs = array();
		foreach($res as $val)
		{
			$recs[$val[0]] = $val[1];
			if ($rate) {
				$recs[$val[0]].= '-還付率【'.$val[2].'%】';
			}
		}
		return $recs;
	}

	//------------------------------------------------------------
	// 室場利用目的情報取得
	//------------------------------------------------------------
	function get_stj_purpose_options($scd, $rcd, $cno)
	{
		$sql = 'SELECT DISTINCT m.mokutekicode, m.mokutekiname, m.mokutekiskbcode
			FROM m_mokuteki m
			JOIN m_stjpurpose p USING (localgovcode, mokutekicode)
			WHERE p.localgovcode=? AND p.shisetsucode=? AND p.shitsujyocode=? AND (p.combino=? OR p.combino=0)
			ORDER BY mokutekiskbcode, mokutekicode';
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array('00' => '--');
		foreach ($res as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 料金区分情報取得
	//------------------------------------------------------------
	function get_stj_feekbn_options($scd, $rcd, $cno, $usedate)
	{
		$MonthDay = substr($usedate, 4, 4);

		$wday = strtolower(date('D', strtotime($usedate))).'flg';
		$sql = "SELECT holiflg FROM m_holiday WHERE localgovcode=? AND heichouholiday=? ";
		$HoliFlg = $this->con->getOne($sql, array(_CITY_CODE_, $usedate));
		if ($HoliFlg == '1') $wday = 'holiflg';

		$sql = 'SELECT * FROM m_stjfee
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?
			AND monthdayfrom<=? AND monthdayto>=?
			AND '.$wday."= 1 AND appdatefrom<=?
			AND (haishidate>? OR haishidate='' OR haishidate IS NULL)";
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno,
				$MonthDay, $MonthDay, $usedate, $usedate);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (empty($res)) return array();

		$aFeeKbnBase = $this->get_feekbn_options();

		$recs = array();
		foreach ($res as $key => $val)
		{
			if (preg_match("/^feekbn/", $key)) {
				if (intval($val) > 0) {
					$recs[$val] = $aFeeKbnBase[$val];
				}
			}
		}
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 料金区分情報取得
	//------------------------------------------------------------
	function get_feekbn_options()
	{
		$sql = 'SELECT feekbn, feekbnname FROM m_feekbn
			WHERE localgovcode=? ORDER BY feekbn';
		$aWhere = array(_CITY_CODE_);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 割増情報取得
	//------------------------------------------------------------
	function get_extracharge_options($scd, $rcd)
	{
		$sql = 'SELECT extracharge FROM m_shitsujyou';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=?';
		$sql.= ' AND shitsujyocode=?';

		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$value = $this->con->getOne($sql, $aWhere);

		if ($value == '') return array();

		$sql = 'SELECT extracode, extraname, rate';
		$sql.= ' FROM m_extracharge';
		$sql.= " WHERE localgovcode=? AND (extracode='".str_replace(',', "' OR extracode='", $value)."')";
		$sql.= ' ORDER BY extracode';
		$res = $this->con->getAll($sql, array(_CITY_CODE_));
		$recs = array();
		foreach ($res as $val)
		{
			$recs[$val[2].','.$val[0]] = $val[1].'【'.$val[2].'%】';
		}
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 付属室場リスト取得
	//------------------------------------------------------------
	function get_fuzoku_options($scd, $rcd, $cno, $usedate, $useTimeFrom, $useTimeTo)
	{
		$aWhere = array(_CITY_CODE_, $usedate);

		$sql = 'SELECT DISTINCT
			f.shisetsucode, f.shitsujyocode, f.fuzokucode,
			s.shitsujyoname, s.genapplyflg, s.shitsujyoskbcode
			FROM m_fuzokushitsujyou f
			JOIN m_shitsujyou s
			ON f.localgovcode=s.localgovcode
			AND f.shisetsucode=s.shisetsucode
			AND f.fuzokucode=s.shitsujyocode
			WHERE f.localgovcode=? AND s.appdatefrom<=?';
		if ($scd) $sql.= " AND f.shisetsucode='{$scd}'";
		if ($rcd) $sql.= " AND f.shitsujyocode='{$rcd}'";
		if ($scd && $rcd) $sql.= " AND (f.combino=0 OR f.combino={$cno})";
		$sql.= ' ORDER BY shisetsucode, shitsujyoskbcode, shitsujyocode';
		$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if ($scd == '' || $rcd == '' || $useTimeFrom == '' || $useTimeTo == '') return $recs;

		$sql = 'SELECT COUNT(DISTINCT yoyakunum)
			FROM t_yoyaku
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND mencode=? AND usedatefrom=?
			AND usetimefrom<? AND usetimeto>?';

		$timeFrom = $useTimeFrom.'00';
		$timeTo = $useTimeTo.'00';

		foreach ($recs as $key => $val)
		{
			$aWhere = array(_CITY_CODE_, $scd,
					$val['fuzokucode'], 'ZZ',
					$usedate,
					$timeTo, $timeFrom);
			$num = $this->con->getOne($sql, $aWhere);
			$recs[$key]['arr_flg'] = $num;
		}
		return $recs;
	}

	//------------------------------------------------------------
	// 減免リスト取得
	//------------------------------------------------------------
	function get_all_genmen($scd, $rcd, $usedate, $userid)
	{
		$sql = 'SELECT genmen, genapplyflg FROM m_shitsujyou';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=?';
		$sql.= ' AND shitsujyocode=?';

		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$aUsrGen = array();
		$aSinGen = array();
		$aStjGen = array();

		if (preg_match('/1/', $row['genapplyflg'])) {
			$aUsrGen = $this->get_user_genmen($userid, $usedate);
		}
		if (preg_match('/2/', $row['genapplyflg'])) {
			$aSinGen = $this->get_shinsei_genmen();
		}
		if (preg_match('/3/', $row['genapplyflg']) && $row['genmen'] != '') {
			$sql = "SELECT koteigencode AS gen_code, rate,";
			$sql.= " koteigenname AS gen_name,";
			$sql.= " '3' AS gen_type, '室場減免' AS gen_type_name";
			$sql.= " FROM m_genmen";
			$sql.= " WHERE localgovcode=? AND (koteigencode='".str_replace(',', "' OR koteigencode='", $row['genmen'])."')";
			$sql.= " ORDER BY gen_code";
			$aStjGen = $this->con->getAll($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);
		}

		$aGen = array();
		$aGen = array_merge($aSinGen, $aStjGen);
		$aGen = array_merge($aGen, $aUsrGen);
		$recs = array();
		foreach ($aGen as $val)
		{
			$recs[$val['rate'].','.$val['gen_type'].','.$val['gen_code']] = $val['gen_type_name'].'-'.$val['gen_name'].'【'.$val['rate'].'%】';
		}
		unset($aUsrGen);
		unset($aStjGen);
		unset($aSinGen);
		unset($aGen);
		return $recs;
	}

	function get_user_genmen($userid, $usedate)
	{
		$sql = "SELECT a.koteigencode AS gen_code, b.rate,";
		$sql.= " b.koteigenname AS gen_name,";
		$sql.= " '1' AS gen_type, '利用者減免' AS gen_type_name";
		$sql.= " FROM m_usrgenmen a";
		$sql.= " JOIN m_genmen b USING (localgovcode, koteigencode)";
		$sql.= " WHERE a.localgovcode=? AND a.userid=?";
		$sql.= " AND (a.appday<=? OR a.keizokuflg='1') AND a.limitday>=?";
		$sql.= " ORDER BY gen_code";
		$aWhere = array(_CITY_CODE_, $userid, $usedate, $usedate);
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_shinsei_genmen()
	{
		$sql = "SELECT singencode AS gen_code, rate,";
		$sql.= " singenname AS gen_name,";
		$sql.= " '2' AS gen_type, '申請減免' AS gen_type_name";
		$sql.= " FROM m_singenmen";
		$sql.= " WHERE localgovcode=?";
		$sql.= " ORDER BY gen_code";
		$aWhere = array(_CITY_CODE_);
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_user_count($userid, $scd, $rcd, $usedate)
	{
		$userCount = array(0, 0, 0, 0);
		$useYM = substr($usedate,0,6);

		$aWhere = array(_CITY_CODE_, $scd, $useYM, $userid);
		$sql = "SELECT COUNT(DISTINCT yoyakunum) FROM t_yoyaku
			WHERE localgovcode=? AND shisetsucode=?
			AND SUBSTRING(usedatefrom, 1, 6)=? AND userid=?";
		$userCount[0] = $this->con->getOne($sql,$aWhere);

		$sql = "SELECT COUNT(DISTINCT pulloutyoyakunum) FROM t_pulloutyoyaku
			WHERE localgovcode=? AND shisetsucode=?
			AND SUBSTRING(usedate, 1, 6)=? AND userid=?";
		$userCount[2] = $this->con->getOne($sql,$aWhere);

		$aWhere = array(_CITY_CODE_, $scd, $rcd, $useYM, $userid);
		$sql = "SELECT COUNT(DISTINCT yoyakunum) FROM t_yoyaku
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			AND SUBSTRING(usedatefrom, 1, 6)=? AND userid=?";
		$userCount[1] = $this->con->getOne($sql,$aWhere);

		$sql = "SELECT COUNT(DISTINCT pulloutyoyakunum) FROM t_pulloutyoyaku
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			AND SUBSTRING(usedate, 1, 6)=? AND userid=?";
		$userCount[3] = $this->con->getOne($sql,$aWhere);
		return $userCount;
	}

	function check_lots_period($scd, $rcd, $mcd, $usedate)
	{
		$sql = 'SELECT pulloutflg, pulloutukekbn, pulloutukekikan
			FROM m_yoyakuscheduleptn
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if ($rec['pulloutflg'] == 1) {
			if ($mcd[0] != 'ZZ') {
				$sql = "SELECT DISTINCT pulloutukemnflg FROM m_men
					WHERE localgovcode=? AND shisetsucode=?
					AND shitsujyocode=? AND (mencode='".implode("' OR mencode='", $mcd)."')";
				$flgs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC|DB_FETCHMODE_FLIPPED);
				if (empty($flgs['pulloutukemnflg'])) {
					return 0;
				} elseif (count($flgs['pulloutukemnflg']) != 1 || $flgs['pulloutukemnflg'][0] != '2') {
					return 0;
				}
			}
		
			$useMonth = intval(substr($usedate, 4, 2));

			if ($rec['pulloutukekbn'] == 2) {
				$ukeVal = explode(',', $rec['pulloutukekikan']);
				if (!in_array($useMonth, $ukeVal)) return 0;
			}

			$sql = 'SELECT pulloutfromday, pulloutfromtime, pulloutlimitday, pulloutlimittime
				FROM t_monthpulloutdate
				WHERE localgovcode=? AND shisetsucode=?
				AND shitsujyocode=? AND month=?';
			$aWhere = array(_CITY_CODE_, $scd, $rcd, $useMonth);
			$aMonth = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
			$start_entry = $this->getMatchTime($aMonth['pulloutfromday'],$usedate,$aMonth['pulloutfromtime']);
			$stop_entry = $this->getMatchTime($aMonth['pulloutlimitday'],$usedate,$aMonth['pulloutlimittime'])+59;

			if ($start_entry < time() && time() <= $stop_entry) {
				return 1;
			}
		}
		return 0;
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
		if ($setMonth > intval(substr($useDate,4,2))) {
			--$setYear;
		}
		return mktime($hh, $mm, 0, $setMonth, intval(substr($setDate,2,2)), $setYear);
	}

	function check_user_validate($scd, $rcd, $userid, $yoyakukbn)
	{
		$sql = "SELECT kojindankbn, userareakbn
			FROM m_user WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $userid);
		$user = $this->con->getRow($sql,$aWhere,DB_FETCHMODE_ASSOC);

		$sql = "SELECT yoyakukojindanflg, pulloutkojindanflg,
			yoyakuareapriorityflg, pulloutareapriorityflg
			FROM m_shitsujyou 
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?"; 
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$shitsujyou = $this->con->getRow($sql,$aWhere,DB_FETCHMODE_ASSOC);
		if ($user && $shitsujyou) {
			if ($yoyakukbn == '01') { //抽選
				if ($user['kojindankbn'] == '1') { //1:個人
					if ($shitsujyou['pulloutkojindanflg'] == '2') {
						return '当室場の抽選の対象は団体のみです。';
					}
				} else { //2:団体
					if ($shitsujyou['pulloutkojindanflg'] == '1') {
						return '当室場の抽選の対象は個人のみです。';
					}
				}
				if ($user['userareakbn'] == '02') { //02:市外
					if ($shitsujyou['pulloutareapriorityflg'] == '1') {
						return '当室場の抽選の対象は市内の利用者のみです。';
					}
				}
			} elseif ($yoyakukbn == '02') { //予約
				if ($user['kojindankbn'] == '1') { //1:個人
					if ($shitsujyou['yoyakukojindanflg'] == '2') {
						return '当室場の一般予約の対象は団体のみです。';
					}
				} else { //2:団体
					if ($shitsujyou['yoyakukojindanflg'] == '1') {
						return '当室場の一般予約の対象は個人のみです。';
					}
				}
				if ($user['userareakbn'] == '02') { //02:市外
					if ($shitsujyou['yoyakuareapriorityflg'] == '1') {
						return '当室場の一般予約の対象は市内の利用者のみです。';
					}
				}
			}
		}
		return '';
	}

	function check_receipt_status($total, $fee)
	{
		$st = 0;

		if ($total == 0) $st = 2;
		elseif ($fee == 0) $st = 1;
		elseif ($total == $fee) $st = 4;
		elseif ($total > $fee) $st = 3;
		elseif ($total < $fee) $st = 5;

		return $st;
	}

	function accept_fee_data(&$req, &$rec, $index=-1)
	{
		$kinshu = '';
		$fee = 0;
		if ($index < 0) {
			$kinshu = $req['Kinshu'];
			$fee = $req['receiptFee'];
		} else {
			$kinshu = $req['Kinshu'][$index];
			$fee = $req['receiptFee'][$index];
		}

		$this->con->autoCommit(false);
		$dataset = array(
				'useukeflg' => '1',
				'honyoyakukbn' => '02',
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);
		$rc = $this->update_by_yoyakunum('t_yoyaku', $dataset, $rec['yoyakunum']);
		if ($rc < 0) {
			$this->con->rollback();
			return 0;
		}

		$paykbn = $this->check_receipt_status($rec['TotalFee'], $fee);
		$dataset = array(
				'paykbn' => $paykbn,
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);
		$rc = $this->update_by_yoyakunum('t_yoyakufeeshinsei', $dataset, $rec['yoyakunum']);
		if ($rc < 0) {
			$this->con->rollback();
			return 0;
		}

		$this->delete_by_yoyakunum('t_yoyakufeeuketsuke', $rec['yoyakunum']);

		$dataset = array();
		$dataset['localgovcode'] = $rec['localgovcode'];
		$dataset['shisetsucode'] = $rec['shisetsucode'];
		$dataset['receptdate'] = date('Ymd');
		$dataset['uketime'] = date('His');
		$dataset['yoyakunum'] = $rec['yoyakunum'];
		$dataset['receptnum'] = '01';
		$dataset['userid'] = $rec['userid'];
		$dataset['shisetsufee'] = $rec['TotalFee'];
		$dataset['tax'] = $rec['TotalTax'];

		switch ($kinshu) {
			case '01':
				$dataset['cash'] = $fee;
				break;
			case '02':
				$dataset['chg'] = $fee;
				break;
			case '03':
				$dataset['ticket'] = $fee;
				break;
			case '04':
				$dataset['kouzafurikomi'] = $fee;
				break;
			case '05':
				$dataset['others'] = $fee;
				break;
		}

		$dataset['receptid'] = $_SESSION['userid'];
		$dataset['receptplace'] = $req['ReceptPlace'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		$rc = $this->oDB->insert('t_yoyakufeeuketsuke', $dataset);
		if ($rc < 0) {
			$this->con->rollback();
			return 0;
		}
		$this->con->commit();
		return $paykbn;
	}
}
?>
