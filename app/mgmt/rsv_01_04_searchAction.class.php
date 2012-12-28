<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  受付検索
 *
 *  rsv_01_04_searchAction.class.php
 *  rsv_01_04.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';

define('P_I', 'rsv_01_04');

class rsv_01_04_searchAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);

		$_SESSION['BACK_OP'] = P_I;
	}

	function execute()
	{
		$message = '';
		$p = array();
		$results = array();

		$this->set_header_info();

		if (isset($_GET['back']) || isset($_POST['escapeFlg'])) {
			$p = $_SESSION[P_I];
		} elseif (empty($_POST)) {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
		} else {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
			$p = $_POST;
		}

		if (isset($_POST['escapeFlg'])) {
			$this->set_escape_flg($_POST['YoyakuNum'], $_POST['escapeFlg']);
		}

		$aShisetsu = $this->oPrivilege->get_shisetsu_list();
		if (!isset($p['ShisetsuCode'])) {
			$scd = $this->oPrivilege->getDefaultShisetsuCode();
			$p['ShisetsuCode'] = $scd == '' ? key($aShisetsu) : $scd;
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

		$p['dateFrom'] = empty($p['FromYear']) ? time() : strtotime($p['FromYear'].$p['FromMonth'].$p['FromDay']);

		if (isset($p['searchBtn']) && !empty($aShisetsu)) {
			$res = $this->get_db_info($p);
			$results = $this->remake_data($res, $aShisetsu);
			unset($res);
			if (strlen($p['YoyakuNum']) > 0 && !empty($results)) {
				$p['dateFrom'] = strtotime($results[$p['YoyakuNum']]['usedatefrom']);
				$p['ShisetsuCode'] = $results[$p['YoyakuNum']]['shisetsucode'];
				$p['ShitsujyoCode'] = $results[$p['YoyakuNum']]['shitsujyocode'];
			}
			$_SESSION[P_I] = $p;
		}

		$this->oSmarty->assign('p', $p);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('array_court', $array_court);
		$this->oSmarty->assign('results', $results);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->display('rsv_01_04.tpl');
	}

	function get_db_info(&$p)
	{
		$sql = "SELECT y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.yoyakunum, y.honyoyakukbn, y.yoyakukbn,
			y.useukeflg, y.escapeflg,
			y.userid, u.namesei, s.shitsujyoname, s.shitsujyokbn,
			f.suuryo, 'Y' AS class
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			JOIN m_user u
			USING (localgovcode, userid)";

		$sql2 = "UNION select y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.yoyakunum, y.honyoyakukbn, y.yoyakukbn,
			y.useukeflg, y.escapeflg,
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

		$where = " WHERE y.honyoyakukbn<>'03' AND y.honyoyakukbn<>'04'
				AND y.yoyakukbn='02'
				AND f.localgovcode='"._CITY_CODE_."' ";
		$aWhere = array();

		if (strlen($p['YoyakuNum']) > 0) {
			$where.= "AND f.yoyakunum = '{$p['YoyakuNum']}' ";

			$priSql =  $this->oPrivilege->getStaffShitsujyoSql('f');
			if ($priSql) {
				$where .= 'AND '.$priSql[0].' ';
				$aWhere = array_merge($priSql[1], $priSql[1]);
				$sql.= $where.$sql2.$where.$orderBy;
			} else {
				return array();
			}
		} else {
			//利用日
			$useDateFrom = date('Ymd', $p['dateFrom']);
			$where .= "AND f.usedate = '{$useDateFrom}' ";
			//施設
			if ($p['ShitsujyoCode']) {
				$where .= "AND f.shisetsucode = '{$p['ShisetsuCode']}' ";
				$where .= "AND f.shitsujyocode = '{$p['ShitsujyoCode']}' ";
			} else {
				$priSql =  $this->oPrivilege->getStaffShitsujyoSql('f', $p['ShisetsuCode']);
				$where .= 'AND '.$priSql[0].' ';
				$aWhere = $priSql[1];
			}
			if (isset($p['WithoutCancelFlg'])) {
				$sql.= $where.$orderBy;
			} else {
				$sql.= $where.$sql2.$where.$orderBy;
				$aWhere = array_merge($aWhere, $aWhere);
			}
		}
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function remake_data(&$aData, &$shisetsuNameArr)
	{
		global $aPayKbn;

		$aCombi = $this->oSC->get_combi_name_array();

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

			$val['PayStatus'] = 1;
			if ($receiptStatus == 1 || $receiptStatus == 3) {
				$val['PayStatus'] = 0;
			}
			$val['PayKbn'] = $receiptStatus;
			$val['PayKbnName'] = $aPayKbn[$receiptStatus];

			$val['YoyakuKbnName'] = $this->oSC->get_HonYoyakuKbn_name($val['honyoyakukbn']);
			if ($val['class'] == 'H') $val['YoyakuKbnName'] = '取消';
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
			$val['UseDate'] = $this->oSC->getDateView($val['usedatefrom']);
			$val['UseTimeFromView'] = $this->oSC->getTimeView($val['usetimefrom']);
			$val['UseTimeToView'] = $this->oSC->getTimeView($val['usetimeto']);
			$res[$val['yoyakunum']] = $val;
		}
		unset($aCombi);
		return $res;
	}

	function set_escape_flg($YoyakuNum, $flg)
	{
		$dataset = array(
				'escapeflg' => $flg,
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);

		$where = "localgovcode='"._CITY_CODE_
			."' AND yoyakunum='".$YoyakuNum."'";
		$this->oDB->update('t_yoyaku', $dataset, $where);
	}
}
?>
