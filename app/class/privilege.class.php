<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  privilege.class.php
 */

class privilege
{
	private $staffid = '';
	private $lcd = '';
	private $con = null;

	function __construct($strStaffId, $con)
	{
		$this->staffid = $strStaffId ;
		$this->lcd = _CITY_CODE_;
		$this->con = $con;
	}

	// 部署コード取得
	function get_busho_options()
	{
		$sql = 'SELECT bushocode, bushoname
			FROM m_busho ORDER BY bushocode';
		$res = $this->con->getAll($sql);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//
	// 職員マスタより該当職員のデータを取得する。
	//
	function get_staff_data()
	{
		$sql = 'SELECT * FROM m_staff';
		$sql.= ' WHERE localgovcode=? AND staffid=?';
		return $this->con->getRow($sql, array($this->lcd, $this->staffid), DB_FETCHMODE_ASSOC);
	}

	//-----------------------------------------------------------------
	// 該当ページを表示するのに必要な権限を返す。
	//-----------------------------------------------------------------
	function get_page_type()
	{
		// 該当ページのファイル名を取得する。
		$strFileName = basename($_SERVER["PHP_SELF"]);
		
		// ページ権限リスト情報を読み込む
		$ary = parse_ini_file(dirname(__FILE__).'/'._PRIVILEGE_FILE_NAME_);

		// 該当ページの表示権限を返す。
		return $ary[$strFileName];
	}

	//-------------------------------------------------------------
	// ページ表示可否をチェックする。
	//   ・職員権限とページ表示に必要な権限を比較する。
	//-------------------------------------------------------------
	function check_view_page(&$aPrivilege)
	{
		// このページを表示するのに必要な権限を取得する。
		$strPageType = $this->get_page_type();
		
		$flag = FALSE;

		foreach ($aPrivilege as $key => $val)
		{
			if (strlen($val) > 0 && $val != 'FORBIDDEN') {
				$pattern = "/{$key}/";
				if (preg_match($pattern, $strPageType)) {
					$flag = TRUE ; // 表示OK
					break;
				}
			}
		}

		// 該当するページタイプがなければ無条件に表示される。
		$ary = array('stf', 'usr', 'fcl', 'rsv', 'fee', 'use');
		if (in_array($strPageType, $ary) == FALSE) {
			$flag = TRUE;
		}
		return $flag ;
	}

	//-----------------------------------------------------------------
	// m_staffshisetsu 職員の該当施設・室場データから
	// 施設・室場検索条件SQLを作成し、返却する
	//-----------------------------------------------------------------
	function getStaffShitsujyoSql($priCode='', $scd=null)
	{
		$ary = $this->get_staffshisetsu($scd) ;
		if (empty($ary)) return array();

		$priName = '';
		if ($priCode) $priName = $priCode.'.';

		$aWhere = array();
		$strSql = '';
		foreach($ary as $value)
		{
			$rcd = $value['shitsujyocode'];
			if ($strSql) $strSql.=' OR ';
			$str = $priName.'shisetsucode=?';
			if ($rcd) $str = '('.$str.' AND '.$priName.'shitsujyocode=?)';
			$strSql .= $str;
			array_push($aWhere, $value['shisetsucode']);
			if ($rcd) array_push($aWhere, $value['shitsujyocode']);
		}
		$strSql = ' ('.$strSql.') ';
		return array($strSql, $aWhere);
	}

	//-----------------------------------------------------------------
	// m_staffshisetsu 職員施設マスタから該当職員のデータを取得する。
	//-----------------------------------------------------------------
	function get_staffshisetsu($scd=null)
	{
		$aWhere = array($this->lcd, $this->staffid);
		$sql = 'SELECT shisetsucode, shitsujyocode
			FROM m_staffshisetsu
			WHERE localgovcode=? AND staffid=?';
		if ($scd) {
			$sql.= ' AND shisetsucode=?';
			array_push($aWhere, $scd);
		}
		$sql.= ' ORDER BY shisetsucode, shitsujyocode';

		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$recs = array();
		foreach ($res as $val)
		{
			if ($val['shisetsucode'] == '') continue;
			if (array_key_exists($val['shisetsucode'], $recs) && $val['shitsujyocode'] != '') continue;
			$index = $val['shisetsucode'].$val['shitsujyocode'];
			$recs[$index] = $val;
		}
		unset($res);

		return $recs;
	}

	//-----------------------------------------------------------------
	// 『室場』セレクトボックスを作成するためのSQL文を作成する。
	//-----------------------------------------------------------------
	function makeSqlShitsujyoSelectbox($without_fuzoku=false, $opt=null)
	{
		$ary = $this->get_staffshisetsu() ;
		if (empty($ary)) return '';

		// 施設コードが引数として与えられていた場合
		// この施設コードも表示対象として加える。
		if (isset($opt)) {
			foreach ($opt as $val)
			{
				$index = $val['shisetsucode'].$val['shitsujyocode'];
				$ary[$index]['shisetsucode'] = $val['shisetsucode'];
				$ary[$index]['shitsujyocode'] = $val['shitsujyocode'];
			}
		}

		$stWhere = '';
		foreach ($ary as $val)
		{
			if ($stWhere != '') $stWhere.= ' OR ';
			if ($val['shitsujyocode'] == '') {
				$stWhere.= "shisetsucode='".$val['shisetsucode']."'";
			} else {
				$stWhere.= "(shisetsucode='".$val['shisetsucode']."' AND shitsujyocode='".$val['shitsujyocode']."')";
			}
		}
		$stWhere = ' AND ('.$stWhere.')';
		unset($ary);

		$sql = "SELECT shisetsucode, shitsujyocode, shitsujyoname, shitsujyoskbcode, appdatefrom
			FROM m_shitsujyou
			WHERE localgovcode='{$this->lcd}' {$stWhere}";
		$sql.= " AND shitsujyokbn<>'4'";
		if ($without_fuzoku) $sql.= " AND shitsujyokbn<>'3'";
		$sql.= " ORDER BY shitsujyoskbcode, appdatefrom, shitsujyocode";
		return $sql ;
	}

	//-----------------------------------------------------------------
	// 『施設』セレクトボックスで使用するデフォルト値を取得する。
	//-----------------------------------------------------------------
	function getDefaultShisetsuCode()
	{
		$sql = "SELECT s.shisetsucode, s.shisetsuskbcode FROM m_staff f
			JOIN m_shisetsu s ON f.localgovcode=s.localgovcode AND f.bushocode=s.rangebusyocode
			JOIN m_staffshisetsu t
			ON t.localgovcode=s.localgovcode AND t.shisetsucode=s.shisetsucode AND t.staffid=f.staffiD
			WHERE f.localgovcode=? AND f.staffid=?
			ORDER BY shisetsuskbcode, shisetsucode";
		$res = $this->con->getAll($sql, array($this->lcd, $this->staffid));
		if (empty($res)) return '';
		return $res[0][0];
	}

	//-----------------------------------------------------------------
	// 『施設』セレクトボックスで使用する配列
	//-----------------------------------------------------------------
	function get_shisetsu_list()
	{
		$sql = "SELECT DISTINCT t.shisetsucode, s.shisetsuname, s.shisetsuskbcode
			FROM m_staffshisetsu t
			JOIN m_staff f USING(localgovcode, staffid)
			JOIN m_shisetsu s USING(localgovcode, shisetsucode)
			WHERE f.localgovcode=? AND f.staffid=?
			ORDER BY shisetsuskbcode, shisetsucode";
		$res = $this->con->getAll($sql, array($this->lcd, $this->staffid));
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//-----------------------------------------------------------------
	// 『室場』セレクトボックスで使用する配列
	//-----------------------------------------------------------------
	function get_shitsujyo_list($scd)
	{
		if ($scd == '') return array();

		$priSql = $this->getStaffShitsujyoSql('', $scd);
		if (empty($priSql)) return array();

		$aWhere = array_merge(array($this->lcd), $priSql[1]);
		$sql = "SELECT shitsujyocode, shitsujyoname, shitsujyoskbcode
			FROM m_shitsujyou
			WHERE localgovcode=? AND {$priSql[0]}
			AND shitsujyokbn<'3'
			ORDER BY shitsujyoskbcode, shitsujyocode";
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//--------------------------------------------------------
	// 施設コードを指定し、室場・面組合情報を連結して取得
	//--------------------------------------------------------
	function get_shitsujyo_combi($scd=null)
	{
		$ary = $this->get_staffshisetsu() ;
		if (empty($ary)) return array();

		$aWhere = array($this->lcd);

		$sql = 'SELECT DISTINCT s.shisetsucode, s.shitsujyocode,
				CASE WHEN m.combino IS NULL
				THEN 0 ELSE m.combino END AS combino,
				m.combiname, s.shitsujyoname, s.genapplyflg,
				s.shitsujyoskbcode, m.combiskbno
			FROM m_shitsujyou s 
			LEFT OUTER JOIN m_mencombination m 
			ON (m.localgovcode=s.localgovcode
				AND m.shisetsucode=s.shisetsucode
				AND m.shitsujyocode=s.shitsujyocode) 
			WHERE s.shitsujyokbn<3 AND s.localgovcode=?
			AND DATE(s.appdatefrom)<=DATE(NOW())';
		if ($scd) {
			$sql.= ' AND s.shisetsucode=?';
			array_push($aWhere, $scd);
		}
		$sql.= ' ORDER BY shisetsucode, shitsujyoskbcode, shitsujyocode, combiskbno, combino';

		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$recs = array();
		foreach ($res as $val)
		{
			$key1 =$val['shisetsucode'];
			$key2 =$val['shisetsucode'].$val['shitsujyocode'];
			if (array_key_exists($key1, $ary) || array_key_exists($key2, $ary)) {
				$index = $val['shisetsucode'].$val['shitsujyocode'].$val['combino'];
				$recs[$index] = $val;
			}
		}
		unset($ary, $res);
		return $recs;
	}
}
?>
