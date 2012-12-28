<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  詳細表示
 *
 *  rsv_02_01_detailAction.class.php
 *  rsv_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';

class rsv_02_01_detailAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();
		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		global $aNinzu, $aKinshu, $aOptionFee;

		$this->set_header_info();

		$YoyakuNum = $_GET['YoyakuNum'];

		$rec = $this->get_reserve_data($YoyakuNum);

		$this->oSmarty->assign('d', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('aNinzu', $aNinzu);
		$this->oSmarty->assign('aOptionFee', $aOptionFee);
		$this->oSmarty->assign('aKinshu', $aKinshu);
		$this->oSmarty->assign('fmode', $_SESSION['BACK_OP']);
		$this->oSmarty->assign('returnUrl', 'index.php?op='.$_SESSION['BACK_OP'].'_search&back=1');
		$this->oSmarty->display('rsv_02_01.tpl');
	}

	function get_reserve_data($YoyakuNum)
	{
		global $aNinzu, $aPayKbn, $aOptionFee, $aGenmenType;

		$aWhere = array(_CITY_CODE_, $YoyakuNum, _CITY_CODE_, $YoyakuNum);

		$sql = "SELECT y.*,
			'00000000' AS lstupddate, '000000' AS lstupdtime,
			'000000' AS lstupdid, 'y' AS class,
			s.shitsujyokbn, s.shitsujyoname
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE y.localgovcode=? AND y.yoyakunum=?
			UNION SELECT y.*, 'h' AS class,
			s.shitsujyokbn, s.shitsujyoname
			FROM h_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE y.localgovcode=? AND y.yoyakunum=?
			ORDER BY shitsujyokbn, shitsujyocode";

		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rec = $res[0];
		$atmp = $this->oSC->get_shitsujyo_info($rec['shisetsucode'], $rec['shitsujyocode']);
		$rec['ShisetsuName'] = $atmp['shisetsuname'];
		$rec['ShowDanjyoNinzuFlg'] = $atmp['showdanjyoninzuflg'];
		$rec['ShinsaFlg'] = $atmp['shinsaflg'];
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

		$aStaff = $this->oSC->get_staff_name_array();

		$rec['YoyakuKbnName'] = $this->oSC->get_YoyakuKbn_name($rec['yoyakukbn']);
		$rec['HonYoyakuKbnName'] = $this->oSC->get_HonYoyakuKbn_name($rec['honyoyakukbn']);
		$rec['MokutekiName'] = $this->oSC->get_purpose_name($rec['mokutekicode']);
		$rec['UseKbnName'] = $this->oSC->get_UseKbn_name($rec['usekbn']);
		$rec['ShinsaDate'] = '-';
		$rec['KariShinsaID'] = '-';
		if ($rec['shinsadate']) {
			$rec['ShinsaDate'] = $this->oSC->getDateView($rec['shinsadate']);
	
			$rec['KariShinsaID'] = isset($aStaff[$rec['karishinsaid']]) ? $aStaff[$rec['karishinsaid']] : $rec['karishinsaid'];
		}
		$rec['ShinsaKbnName'] = $this->oSC->get_ShinsaKbn_name($rec['shinsakbn']);

		$rec['LstUpdDate'] = $this->oSC->getDateView($rec['lstupddate']);
		$rec['LstUpdTime'] = $this->oSC->getTimeView($rec['lstupdtime']);
		$rec['CancelStaffName'] = isset($aStaff[$rec['cancelstaffid']]) ? $aStaff[$rec['cancelstaffid']] : $rec['cancelstaffid'];
		$rec['DaikouStaffName'] = isset($aStaff[$rec['daikouid']]) ? $aStaff[$rec['daikouid']] : $rec['daikouid'];
		$rec['UpdStaffName'] = isset($aStaff[$rec['updid']]) ? $aStaff[$rec['updid']] : $rec['updid'];
		$rec['UpdDateView'] = $this->oSC->getDateView($rec['upddate']);
		$rec['UpdTimeView'] = $this->oSC->getTimeView($rec['updtime']);
		$rec['AppDateView'] = $this->oSC->getDateView($rec['appdate']);
		$rec['AppTimeView'] = $this->oSC->getTimeView($rec['apptime']);
		$rec['UseDateView'] = $this->oSC->getDateView($rec['usedatefrom']);
		$rec['UseTime'] = $this->oSC->getTimeView($rec['usetimefrom']).' ～ '.$this->oSC->getTimeView($rec['usetimeto']);
		$rec['PayLimitDate'] = $this->oSC->getDateView($rec['shisetsupaylimitdate']);

		$sql = "SELECT basefee, shisetsufee, suuryo, suuryotani, surcharge,
			optionfee1, optionfee2, optionfee3, optionfee5,
			optionfee4, chousei_reason, bihinyoyakunum, bihinfee,
			useninzu, ninzu1, ninzu2, ninzu3, ninzu4, ninzu5,
			ninzu6, ninzu7, ninzu8, ninzu9, ninzu10,ninzu11,
			ninzu12, ninzu13, ninzu14, ninzu15, ninzu16
			FROM t_yoyakufeeshinsei
			WHERE localgovcode=? AND yoyakunum=?
			UNION SELECT basefee, shisetsufee, suuryo, suuryotani, surcharge,
			optionfee1, optionfee2, optionfee3, optionfee5,
			optionfee4, chousei_reason, bihinyoyakunum, bihinfee,
			useninzu, ninzu1, ninzu2, ninzu3, ninzu4, ninzu5,
			ninzu6, ninzu7, ninzu8, ninzu9, ninzu10,ninzu11,
			ninzu12, ninzu13, ninzu14, ninzu15, ninzu16
			FROM h_fee
			WHERE localgovcode=? AND yoyakunum=?";
		$fee = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rec['useninzu'] = $fee['useninzu'];
		foreach ($aNinzu as $key => $val) {
			$rec[$key] = $fee[$key];
		}
		foreach ($aOptionFee as $key => $val) {
			$rec['OptionFee'.$key] = number_format($fee['optionfee'.$key]);
		}
		$rec['ExtName'] = '';
		$rec['ExtRate'] = 100;
		if ($fee['surcharge'] != '') {
			$extra = $this->oSC->get_extracharge_info($fee['surcharge']);
			$rec['ExtName'] = $extra['extraname'];
			$rec['ExtRate'] = $extra['rate'];
			unset($extra);
		}
		$rec['genTypeName'] = $aGenmenType[0];
		$rec['GenName'] = '';
		$rec['GenRate'] = 0;
		if ($fee['suuryotani'] != '') {
			list($genID, $genCode) = explode(',', $fee['suuryotani']);
			$genmen = $this->oSC->get_genmen_info($genID, $genCode);
			$rec['genTypeName'] = $aGenmenType[$genID];
			$rec['GenName'] = $genmen['genname'];
			$rec['GenRate'] = $genmen['rate'];
			unset($genmen);
		}

		$rec['BaseShisetsuFee'] = number_format($fee['basefee']);
		$rec['ShisetsuFee'] = number_format($fee['shisetsufee']);
		$rec['BihinFee'] = number_format($fee['bihinfee']);
		$rec['BihinNum'] = $fee['bihinyoyakunum'] == '' ? '-' : $fee['bihinyoyakunum'];
		$rec['ReceptStaffName'] = '';
		$rec['ReceptDateView'] = '-';
		$rec['ReceptTimeView'] = '';
		$rec['ReceptPlaceName'] = '';

		$oRS = new receipt_status($this->con, $rec['yoyakunum'], $rec['honyoyakukbn'], $fee['suuryo']);
		$paykbn = $oRS->getReceiptStatus($rec['class']=='h');
		$rec['PayKbnName'] = $aPayKbn[$paykbn];
		$rec['Receipt'] = $oRS->getReceiptFee();
		$rec['ChouseiRiyuu'] = $fee['chousei_reason'];
		$rec['SumFee'] = number_format($fee['suuryo']);
		foreach ($rec['Receipt'] as $key => $val) {
			if ($key < 8) $rec['Receipt'][$key] = number_format($val);
		}
		$rec['paynum'] = number_format($oRS->getT_YoyakuFeeUketsukeSumFee());

		if ($rec['Receipt'][8] != '') {
			$rec['ReceptStaffName'] = isset($aStaff[$rec['Receipt'][10]]) ? $aStaff[$rec['Receipt'][10]] : $rec['Receipt'][10];
			$rec['ReceptDateView'] = $this->oSC->getDateView($rec['Receipt'][8]);
			$rec['ReceptTimeView'] = $this->oSC->getTimeView($rec['Receipt'][9]);
			$rec['ReceptPlaceName'] = '('.$this->oSC->get_shisetsu_name($rec['Receipt'][11]).')';
		}
		unset($fee);

		$sql = "SELECT namesei, nameseikana FROM m_user
			WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $rec['userid']);
		$user = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec = array_merge($rec, $user);
		unset($user);

		if ($rec['canceljiyucode']) {
			$sql = "SELECT canceljiyuname FROM m_canceljiyucode
				WHERE localgovcode=? AND cancelcode=?";
			$aWhere = array(_CITY_CODE_, $rec['canceljiyucode']);
			$rec['CancelJiyu'] = $this->con->getOne($sql, $aWhere);
		}

		if ($rec['userid'] === _UNREGISTED_USER_ID_) {
			$unreg = $this->oSC->get_unregisted_user_info($rec['yoyakunum']);
			if ($unreg) {
				$rec = array_merge($rec, $unreg);
				unset($unreg);
			}
		}

		$sql = 'SELECT s.shitsujyoname, f.usetimefrom, f.usetimeto,
			f.amount, f.basefee, f.tax, f.billingfee,
			f.feekbn, f.genmen, f.surcharge
			FROM t_yoyaku_fee_option f
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE f.localgovcode=? AND f.yoyakunum=?';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$opt = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rec['addition'] = array();
		foreach ($opt as $val)
		{
			$tmpval = array();
			$tmpval['item_name'] = $val['shitsujyoname'];
			$tmpval['usetime'] = $this->oSC->getTimeView($val['usetimefrom']).' ～ '.$this->oSC->getTimeView($val['usetimeto']);
			$tmpval['amount'] = $val['amount'];
			$tmpval['basefee'] = number_format($val['basefee']);
			$tmpval['tax'] = number_format($val['tax']);
			$tmpval['billingfee'] = number_format($val['billingfee']);
			$tmpval['FeeInfo'] = $this->oSC->get_UseKbn_name($val['feekbn']);
			if ($val['surcharge'] != '') {
				$extra = $this->oSC->get_extracharge_info($val['surcharge']);
				$tmpval['FeeInfo'].= '&nbsp;'.$extra['extraname'].'&nbsp;'.$extra['rate'].'%';
				unset($extra);
			}
			if ($val['genmen'] != '') {
				list($genID, $genCode) = explode(',', $val['genmen']);
				$genmen = $this->oSC->get_genmen_info($genID, $genCode);
				$tmpval['FeeInfo'].= '&nbsp;'.$aGenmenType[$genID].'&nbsp;'.$genmen['genname'].'&nbsp;'.$genmen['rate'].'%';
				unset($genmen);
			}
			$rec['addition'][] = $tmpval;
		}

		return $rec;
	}
}
?>
