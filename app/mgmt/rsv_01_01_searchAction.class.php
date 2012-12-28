<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約状況検索
 *
 *  rsv_01_01_searchAction.class.php
 *  rsv_01_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';

define('P_I', 'rsv_01_01');

class rsv_01_01_searchAction extends adminAction
{
	private $oSC = null;
	private $aListStatus = array(1=>'予約のみ', 2=>'取消のみ');

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);

		$_SESSION['BACK_OP'] = P_I;
	}

	function execute()
	{
		global $aHonYoyakuKbn, $aPayKbn;

		$message = '';
		$p = array();
		$results = array();

		$this->set_header_info();

		if (isset($_GET['back'])) {
			$p = $_SESSION[P_I];
		} elseif (empty($_POST)) {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
		} else {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
			$p = $_POST;
		}

		$aMokuteki = $this->oSC->get_purpose_name_array();
		$aShisetsu = $this->oPrivilege->get_shisetsu_list();
		if (!isset($p['ShisetsuCode'])) {
			$p['ShisetsuCode'] = '';
		}

		$array_court = '[';
		$sql = $this->oPrivilege->makeSqlShitsujyoSelectbox(true);
		if ($sql != '') {
			$res = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
			foreach ($res as $val)
			{
				$array_court .= "[\"${val['shisetsucode']}\",\"${val['shitsujyocode']}\",\"${val['shitsujyoname']}\"],";
			}	
			unset($res);
		}
		$array_court .= " []] ";

		if (!isset($p['ShitsujyoCode'])) {
			$p['ShitsujyoCode'] = '';
		}

		$p['dateFrom'] = empty($p['FromYear']) ? mktime(0, 0, 0, date('n'), 1, date('Y')) : mktime(0, 0, 0, intval($p['FromMonth']), intval($p['FromDay']), intval($p['FromYear']));
		$p['dateTo'] = empty($p['ToYear']) ? mktime(0, 0, 0, date('n'), date('t'), date('Y')) : mktime(0, 0, 0, intval($p['ToMonth']), intval($p['ToDay']), intval($p['ToYear']));

		if (isset($p['searchBtn'])) {
			$res = $this->get_db_info($p);
			$results = $this->remake_data($res, $p, $aShisetsu);
			unset($res);
			$_SESSION[P_I] = $p;
		}

		$payKbn = $aPayKbn;
		unset($payKbn[0]);

		$honyoyakuKbn = $aHonYoyakuKbn;
		unset($honyoyakuKbn['03']);
		unset($honyoyakuKbn['04']);

		$this->oSmarty->assign('p', $p);
		$this->oSmarty->assign('aHonYoyakuKbn', $honyoyakuKbn);
		$this->oSmarty->assign('aPayKbn', $payKbn);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('array_court', $array_court);
		$this->oSmarty->assign('aMokuteki', $aMokuteki);
		$this->oSmarty->assign('aListStatus', $this->aListStatus);
		$this->oSmarty->assign('results', $results);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->display('rsv_01_01.tpl');
	}

	function make_sql_string(&$pSql)
	{
		$sqlarr = explode('?', $pSql[0]);
		foreach ($pSql[1] as $k =>$v) $sqlarr[$k] .= "'$v'";
		$prisql = implode('', $sqlarr);
		return $prisql;
	}

	function get_db_info(&$p)
	{
		$sql = "SELECT y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.yoyakunum, y.honyoyakukbn, y.yoyakukbn, y.escapeflg,
			y.userid, u.namesei, s.shitsujyoname, s.shitsujyokbn,
			f.suuryo, 'Y' AS class
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			JOIN m_user u
			USING (localgovcode, userid)";

		$sql2 = "SELECT y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.yoyakunum, y.honyoyakukbn, y.yoyakukbn, y.escapeflg,
			y.userid, u.namesei, s.shitsujyoname, s.shitsujyokbn,
			f.suuryo, 'H' AS class
			FROM h_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN h_fee f
			USING (localgovcode, yoyakunum)
			JOIN m_user u
			USING (localgovcode, userid)";

		$orderBy = " ORDER BY usedatefrom, usetimefrom, shisetsucode,
				shitsujyokbn, shitsujyocode, combino";

		$listMode = 0;
		if (isset($p['ListStatus'])) {
			if (count($p['ListStatus']) == 1) {
				$listMode = $p['ListStatus'][0];
			}
		}

		$where = " WHERE f.localgovcode='"._CITY_CODE_."' ";

		//施設
		if ($p['ShisetsuCode']) {
			if ($p['ShitsujyoCode']) {
				$where .= "AND f.shisetsucode = '{$p['ShisetsuCode']}' ";
				$where .= "AND f.shitsujyocode = '{$p['ShitsujyoCode']}' ";
			} else {
				$priSql =  $this->oPrivilege->getStaffShitsujyoSql('f', $p['ShisetsuCode']);
				$where .= 'AND '.$this->make_sql_string($priSql).' ';
			}
		} else {
			$priSql =  $this->oPrivilege->getStaffShitsujyoSql('f');
			if ($priSql) {
				$where .= 'AND '.$this->make_sql_string($priSql).' ';
			} else {
				return array();
			}
		}
		//利用者ID
		if (strlen($p['UserIDFrom']) > 0 && strlen($p['UserIDTo']) == 0) {
			if (isset($p['PartialMatchFlg'])) {
				$where .= "AND y.userid LIKE '{$p['UserIDFrom']}%' ";
			} else {
				$where .= "AND y.userid = '{$p['UserIDFrom']}' ";
			}
		} elseif (strlen($p['UserIDFrom']) > 0 && strlen($p['UserIDTo']) > 0) {
			if ($p['UserIDFrom'] > $p['UserIDTo']) {
				$tmpFrom = $p['UserIDFrom'];
				$tmpTo = $p['UserIDTo'];
				$p['UserIDFrom'] = $tmpTo;
				$p['UserIDTo'] = $tmpFrom;
			}
			$where .= "AND y.userid >= '{$p['UserIDFrom']}' AND y.UserID <= '{$p['UserIDTo']}' ";
		} elseif (strlen($p['UserIDFrom']) == 0 && strlen($p['UserIDTo']) > 0) {
			$where .= "AND y.userid = '{$p['UserIDTo']}' ";
		}
		if (strlen($p['Name']) > 0) {
			$where .= "AND (u.namesei LIKE '%".$p['Name']."%' ";
			$where .= "OR u.nameseikana LIKE '%".$p['Name']."%') ";
		}
		//予約状況
		if (isset($p['HonYoyakuKbn'])) {
			$where .= "AND (y.honyoyakukbn='";
			$where .= implode("' OR y.honyoyakukbn='", $p['HonYoyakuKbn']);
			$where .= "') ";
		}
		//来場状況
		if (isset($p['EscapeFlg'])) {
			$where .= "AND y.escapeflg='1'";
		}
		//電話番号
		if (strlen($p['TelNo1']) > 0 || strlen($p['TelNo2']) > 0 || strlen($p['TelNo3']) > 0) {
			$where .= "AND ((u.telno11 ='".$p['TelNo1']."' AND u.telno12 = '".$p['TelNo2']."' AND u.telno13 = '".$p['TelNo3']."') OR (u.telno21 ='".$p['TelNo1']."' AND u.telno22 = '".$p['TelNo2']."' AND u.telno23 = '".$p['TelNo3']."')) ";
		}
		//利用日
		$useDateFrom = date('Ymd', $p['dateFrom']);
		$where .= "AND f.usedate >= '{$useDateFrom}' ";
		$useDateTo = date('Ymd', $p['dateTo']);
		$where .= "AND f.usedate <= '{$useDateTo}' ";
		//目的
		if ($p['MokutekiCode']) {
			$where .= "AND y.mokutekicode = '{$p['MokutekiCode']}' ";
		}

		if (strlen($p['YoyakuNum']) > 0) {
			$listMode = 0;
			$where = " WHERE f.localgovcode='"._CITY_CODE_."' ";
			$where.= "AND f.yoyakunum = '{$p['YoyakuNum']}' ";

			$priSql =  $this->oPrivilege->getStaffShitsujyoSql('f');
			if ($priSql) {
				$where .= 'AND '.$this->make_sql_string($priSql).' ';
			} else {
				return array();
			}
		}

		switch ($listMode) {
		case 1:
			$sql.= $where.$orderBy;
			break;
		case 2:
			$sql = $sql2.$where.$orderBy;
			break;
		default:
			$sql.= $where.' UNION '.$sql2.$where.$orderBy;
			break;
		}
		return $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	}

	function remake_data(&$aData, &$p, &$shisetsuNameArr)
	{
		global $aPayKbn;

		$aCombi = $this->oSC->get_combi_name_array();

		$aLimitPayKbn = array();
		if (isset($p['PayKbn'])) {
			foreach ($p['PayKbn'] as $val)
			{
				$aLimitPayKbn[$val] = 1;
			}
		}

		$res = array();
		foreach ($aData as $val)
		{
			if (array_key_exists($val['yoyakunum'], $res)) {
				if ($val['shitsujyokbn'] == '3') {
					$res[$val['yoyakunum']]['shitsujyoname'] .= '<br>'.$val['shitsujyoname'];
				}
				continue;
			}
			$oRS = new receipt_status($this->con, $val['yoyakunum'], $val['honyoyakukbn'], $val['suuryo']);
			$receiptStatus = $oRS->getReceiptStatus($val['class']=='H');
			if (isset($p['PayKbn']) && strlen($p['YoyakuNum']) == 0) {
				if (!isset($aLimitPayKbn[$receiptStatus])) continue;
			}

			$val['PayKbn'] = $receiptStatus;
			$val['PayKbnName'] = $aPayKbn[$receiptStatus];

			$val['YoyakuKbnName'] = $this->oSC->get_HonYoyakuKbn_name($val['honyoyakukbn']);
			$val['ShisetsuName'] = $shisetsuNameArr[$val['shisetsucode']];
			if ($val['combino'] != 0) {
				$val['shitsujyoname'] .= '&nbsp;'.$aCombi[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			if ($val['userid'] === _UNREGISTED_USER_ID_) {
				$UnregUserName = $this->oSC->get_unregisted_user_name($val['yoyakunum']);
				if ($UnregUserName != '') {
					$val['namesei'] .= '&nbsp;('.$UnregUserName.')';
				}
			}
			$val['UseDateView'] = $this->oSC->getDateView($val['usedatefrom']);
			$val['UseTimeFromView'] = $this->oSC->getTimeView($val['usetimefrom']);
			$val['UseTimeToView'] = $this->oSC->getTimeView($val['usetimeto']);
			$res[$val['yoyakunum']] = $val;
		}
		unset($aCombi);
		return $res;
	}
}
?>
