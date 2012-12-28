<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設管理共通クラス
 *
 *  facility.class.php
 */

class facility
{
	protected $con = null;
	private $lcd = '';

	//-------------------------------------------------------------------
	// コンストラクタ
	//-------------------------------------------------------------------
	function __construct(&$con)
	{
		$this->con = $con;
		$this->lcd = _CITY_CODE_;
	}

	//------------------------------------------------------------
	// コード名称マスタ情報取得
	//------------------------------------------------------------
	function get_codename_options($kbnName='YoyakuKbn')
	{
		$sql = 'SELECT code, codename, upddate
			FROM m_codename WHERE localgovcode=? AND codeid=?
			ORDER BY code, upddate';
		$aWhere = array($this->lcd, $kbnName);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 施設データを取得
	//------------------------------------------------------------
	function get_shisetsu_data($scd)
	{
		$sql = 'SELECT * FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=?';
		$aWhere = array($this->lcd, $scd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 施設名取得
	//------------------------------------------------------------
	function get_shisetsu_name($scd)
	{
		$sql = 'SELECT shisetsuname FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=?';
		$aWhere = array($this->lcd, $scd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 施設分類データを取得
	//------------------------------------------------------------
	function get_shisetsuclass_data($ccd)
	{
		$sql = 'SELECT * FROM m_shisetsuclass WHERE localgovcode=? AND shisetsuclasscode=?';
		$aWhere = array(_CITY_CODE_, $ccd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 施設クラスマスタ取得
	//------------------------------------------------------------
	function get_shisetsuclass_options()
	{
		$sql = "SELECT shisetsuclasscode,shisetsuclassname
			FROM m_shisetsuclass ORDER BY shisetsuclasscode";
		$res = $this->con->getAll($sql);
		$recs = array();
		foreach($res as $val) $recs[$val['0']] = $val['1'];
		return $recs;
	}

	//------------------------------------------------------------
	// 室場情報取得
	//------------------------------------------------------------
	function get_shitsujyo_header($scd, $rcd)
	{
		$sql = "SELECT s.shisetsuname,";
		$sql.= " t.shitsujyoname, t.shitsujyokbn, t.appdatefrom";
		$sql.= " FROM m_shitsujyou t";
		$sql.= " JOIN m_shisetsu s";
		$sql.= " USING (localgovcode, shisetsucode)";
		$sql.= " WHERE t.localgovcode=? AND t.shisetsucode=? AND t.shitsujyocode=?";

		$aWhere = array($this->lcd, $scd, $rcd);

		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 室場データを取得
	//------------------------------------------------------------
	function get_shitsujyo_data($scd, $rcd)
	{
		$sql = 'SELECT * FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->lcd, $scd, $rcd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 室場名を取得
	//------------------------------------------------------------
	function get_shitsujyo_name($scd, $rcd)
	{
		$sql = 'SELECT shitsujyoname FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->lcd, $scd, $rcd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 面情報を取得
	//------------------------------------------------------------
	function get_men_data($scd, $rcd, $mcd)
	{
		$sql = 'SELECT * FROM m_men WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND mencode=?';
		$aWhere = array($this->lcd, $scd, $rcd, $mcd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 面リストを取得
	//------------------------------------------------------------
	function get_men_data_list($scd, $rcd)
	{
		$sql = 'SELECT * FROM m_men WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->lcd, $scd, $rcd);
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 面名を取得
	//------------------------------------------------------------
	function get_men_name($scd, $rcd, $mcd)
	{
		$sql = 'SELECT menname FROM m_men WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND mencode=?';
		$aWhere = array($this->lcd, $scd, $rcd, $mcd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 面組合せデータを取得
	//------------------------------------------------------------
	function get_mencombination_data($scd, $rcd, $cno=0)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$sql = "SELECT DISTINCT c.combiname, c.combino, c.combiskbno,
				c.openflg, c.openkbn, c.openkbn_disable,
				c.mencode, m.menname
			FROM m_mencombination c 
			JOIN m_men m 
			USING (localgovcode, shisetsucode, shitsujyocode, mencode)
			WHERE c.localgovcode=? AND c.shisetsucode=? AND c.shitsujyocode=?";
		if ($cno != 0) {
			$sql.= " AND c.combino=?";
			array_push($aWhere, $cno);
		}
		$sql.= " ORDER BY combino, mencode";
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 面組合せ情報を作成
	//------------------------------------------------------------
	function make_mencombination_info(&$res)
	{
		$recs = array();
		$i = 0;
		foreach ($res as $row)
		{
			if ($i == 0) $recs = $row;
			else $recs['menname'] .= '&nbsp;'.$row['menname'];
			++$i;
		}
		return $recs;
	}

	//------------------------------------------------------------
	// 面組合せリストを作成
	//------------------------------------------------------------
	function make_mencombination_list($res)
	{
		$recs = array();
		$num = 0;
		foreach ($res as $row)
		{
			if ($num == $row['combino']) {
				$recs[$num]['menname'] .= '&nbsp;'.$row['menname'];
			} else {
				$num = $row['combino'];
				$recs[$num] = $row;
			}
		}
		return $recs;
	}

	//------------------------------------------------------------
	// 面組合せ名を取得
	//------------------------------------------------------------
	function get_mencombination_name($scd, $rcd, $cno)
	{
		$sql = "SELECT DISTINCT combiname
			FROM m_mencombination
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?";
		$aWhere = array($this->lcd, $scd, $rcd, $cno);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// スケジュールデータを取得
	//------------------------------------------------------------
	function get_yoyakuscheduleptn($scd, $rcd)
	{
		$sql = 'SELECT * FROM m_yoyakuscheduleptn WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->lcd, $scd, $rcd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function check_time($str)
	{
		if (!preg_match("/^[0-9]{4}$/", $str)) {
			return false;
		} else {
			$hh = intval(substr($str,0,2));
			$mm = intval(substr($str,2,2));
			if (!is_numeric($str) || $hh < 0 || $hh > 23 || $mm < 0 || $mm > 59) {
				return false;
			} else {
				return true;
			}
		}
	}
}
?>
