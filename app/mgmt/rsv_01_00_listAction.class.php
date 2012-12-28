<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  本日分予約一覧
 *
 *  rsv_01_00_listAction.class.php
 *  rsv_01_00.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';

class rsv_01_00_listAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $aPayKbn;

		$this->set_header_info();

		$oSC = new system_common($this->con);

		$aShisetsu = $oSC->get_shisetsu_name_array();
		$aCombi = $oSC->get_combi_name_array();
		$aMokuteki = $oSC->get_purpose_name_array();

		$pShisetsuCode = isset($_POST['pShisetsuCode']) ? $_POST['pShisetsuCode'] : $this->oPrivilege->getDefaultShisetsuCode();

		$res = $this->get_db_info($pShisetsuCode);

		$recs = array();
		foreach($res as $val)
		{
			if (array_key_exists($val['yoyakunum'], $recs)) {
				if ($val['shitsujyokbn'] == '3') {
					$recs[$val['yoyakunum']]['shitsujyoname'] .= '<br>'.$val['shitsujyoname'];
				}
				continue;
			}
			$oRS = new receipt_status($this->con, $val['yoyakunum'], $val['honyoyakukbn'], $val['suuryo']);
			$receiptStatus = $oRS->getReceiptStatus();
			$val['PayKbnName'] = $aPayKbn[$receiptStatus];
			$val['ShisetsuName'] = $aShisetsu[$val['shisetsucode']];
			if ($val['combino'] != 0) {
				$val['shitsujyoname'] .= '&nbsp;'.$aCombi[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$val['MokutekiName'] = $aMokuteki[$val['mokutekicode']];
			if ($val['userid'] === _UNREGISTED_USER_ID_) {
				$UnregUserName = $oSC->get_unregisted_user_name($val['yoyakunum']);
				if ($UnregUserName != '') {
					$val['namesei'] .= '&nbsp;('.$UnregUserName.')';
				}
			}
			$val['UseTimeFromView'] = $oSC->getTimeView($val['usetimefrom']);
			$val['UseTimeToView'] = $oSC->getTimeView($val['usetimeto']);
			$recs[$val['yoyakunum']] = $val;
		}
		unset($rest, $aShisetsu, $aCombi, $aMokuteki);

		$this->oSmarty->assign('ShisetsuOptions', $this->oPrivilege->get_shisetsu_list());
		$this->oSmarty->assign('ShisetsuSelected', $pShisetsuCode);
		$this->oSmarty->assign('TempReserv', $recs);
		$this->oSmarty->display('rsv_01_00.tpl');
	}

	function get_db_info($ShisetsuCode)
	{
		$res = $this->oPrivilege->getStaffShitsujyoSql('y', $ShisetsuCode);
		if (empty($res)) return array();

		$stWhere = ' AND '.$res[0];
		$aWhere = array_merge(array(_CITY_CODE_, date('Ymd')), $res[1]);

		$sql = 'SELECT DISTINCT y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.yoyakunum, y.userid,
			y.honyoyakukbn, y.yoyakukbn, y.mokutekicode, f.useninzu,
			u.namesei, s.shitsujyoname, s.shitsujyokbn, f.suuryo
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			JOIN m_user u
			USING (localgovcode, userid)
			WHERE y.localgovcode=? AND y.usedatefrom=?'
			. $stWhere
			. " AND y.yoyakukbn='02'
			AND y.honyoyakukbn<>'03' AND y.honyoyakukbn<>'04'
			ORDER BY usetimefrom, shisetsucode, shitsujyokbn,
				shitsujyocode, combino";
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}
}
?>
