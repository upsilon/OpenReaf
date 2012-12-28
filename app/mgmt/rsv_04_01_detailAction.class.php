<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  詳細表示
 *
 *  rsv_04_01_detailAction.class.php
 *  rsv_04_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';

class rsv_04_01_detailAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();
		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$this->set_header_info();

		$YoyakuNum = $_GET['YoyakuNum'];

		$rec = $this->get_reserve_data($YoyakuNum);

		$this->oSmarty->assign('d', $rec);
		$this->oSmarty->display('rsv_04_01.tpl');
	}

	function get_reserve_data($YoyakuNum)
	{
		global $aPayKbn;

		$aWhere = array(_CITY_CODE_, $YoyakuNum);

		$sql = "SELECT y.*,
			s.shitsujyokbn, s.shitsujyoname
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE y.localgovcode=? AND y.yoyakunum=?
			ORDER BY shitsujyokbn, shitsujyocode";

		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rec = $res[0];
		$atmp = $this->oSC->get_shitsujyo_info($rec['shisetsucode'], $rec['shitsujyocode']);
		$rec['ShisetsuName'] = $atmp['shisetsuname'];
		if ($rec['combino'] != 0) {
			$rec['shitsujyoname'] .= '&nbsp;'.$this->oSC->get_combi_name($rec['shisetsucode'], $rec['shitsujyocode'], $rec['combino']);
		}
		foreach ($res as $val)
		{
			if ($val['shitsujyokbn'] == '3') {
				$rec['shitsujyoname'] .= '&nbsp;'.$val['shitsujyoname'];
			}
		}
		unset($res);

		$rec['YoyakuKbnName'] = $this->oSC->get_YoyakuKbn_name($rec['yoyakukbn']);
		$rec['HonYoyakuKbnName'] = $this->oSC->get_HonYoyakuKbn_name($rec['honyoyakukbn']);
		$rec['MokutekiName'] = $this->oSC->get_purpose_name($rec['mokutekicode']);
		$rec['UseDateView'] = $this->oSC->getDateView($rec['usedatefrom']);
		$rec['UseTime'] = $this->oSC->getTimeView($rec['usetimefrom']).' ～ '.$this->oSC->getTimeView($rec['usetimeto']);

		$sql = "SELECT basefee, suuryo
			FROM t_yoyakufeeshinsei
			WHERE localgovcode=? AND yoyakunum=?";
		$fee = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$oRS = new receipt_status($this->con, $rec['yoyakunum'], $rec['honyoyakukbn'], $fee['suuryo']);
		$paykbn = $oRS->getReceiptStatus();
		$rec['PayKbnName'] = $aPayKbn[$paykbn];
		unset($fee);

		$sql = "SELECT namesei, nameseikana FROM m_user
			WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $rec['userid']);
		$user = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec = array_merge($rec, $user);
		unset($user);

		if ($rec['userid'] == _UNREGISTED_USER_ID_) {
			$unreg = $this->oSC->get_unregisted_user_info($rec['yoyakunum']);
			if ($unreg) {
				$rec = array_merge($rec, $unreg);
				unset($unreg);
			}
		}

		return $rec;
	}
}
?>
