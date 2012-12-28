<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用受付
 *
 *  rsv_02_05_receiptAction.class.php
 *  rsv_02_05.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';
require OPENREAF_ROOT_PATH.'/app/class/touroku_base.class.php';
require OPENREAF_ROOT_PATH.'/app/class/log.class.php';

class rsv_02_05_receiptAction extends adminAction
{
	private $oSC = null;
	private $oTB = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
		$this->oTB = new touroku_base($this->oDB, $_SESSION['userid']);
	}

	function execute()
	{
		global $aNinzu, $aKinshu, $aOptionFee;

		$success = 0;
		$message = '';

		$this->set_header_info();

		$YoyakuNum = $_REQUEST['YoyakuNum'];

		if (isset($_POST['addBtn'])) {
			$res = $this->get_db_info($YoyakuNum);
			$this->con->autoCommit(false);
			if ($this->update_db_info($res, $_POST)) {
				$this->con->commit();
				$message = '受付処理が完了しました。';
				$success = 1;
			} else {
				$this->con->rollback();
				$message = '受付処理ができませんでした。';
				$success = -1;
			}
			unset($res);
		}

		$res = $this->get_db_info($YoyakuNum);
		$rec = $this->get_reserve_data($res);
		unset($res);

		if ($success < 0) {
			$rec['ChouseiRiyuu'] = $_POST['ChouseiRiyuu'];
			$rec['useninzu'] = $_POST['useninzu'];
			foreach ($aNinzu as $key => $val)
			{
				$rec[$key] = $_POST[$key];
			}
			$rec['mokutekicode'] = $_POST['MokutekiCode'];
		}

		$aShisetsu = $this->oPrivilege->get_shisetsu_list();
		if (isset($aShisetsu[$rec['shisetsucode']])) {
			$aShisetsu[$rec['shisetsucode']] = '- 利用施設に同じ -';
		}
		$aPurpose = $this->oTB->get_stj_purpose_options($rec['shisetsucode'], $rec['shitsujyocode'], $rec['combino']);
		$aGenmen = $this->oTB->get_all_genmen($rec['shisetsucode'], $rec['shitsujyocode'], $rec['usedatefrom'], $rec['userid']);

		$datetime = time();
		if ($rec['ReceptDate'] != '') {
			$datetime = strtotime($rec['ReceptDate']);
		}
		$oFB = new fee_base($this->con, $rec);
		$taxRate = $oFB->get_tax_rate();

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('taxRate', $taxRate);
		$this->oSmarty->assign('aNinzu', $aNinzu);
		$this->oSmarty->assign('aOptionFee', $aOptionFee);
		$this->oSmarty->assign('aKinshu', $aKinshu);
		$this->oSmarty->assign('aPurpose', $aPurpose);
		$this->oSmarty->assign('aGenmen', $aGenmen);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('datetime', $datetime);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->display('rsv_02_05.tpl');
	}

	function get_db_info($YoyakuNum)
	{
		$aWhere = array(_CITY_CODE_, $YoyakuNum);

		$sql = "SELECT y.*, y.usedatefrom as usedate,
			t.shitsujyokbn, t.shitsujyoname, t.genmen, t.genapplyflg, s.fractionflg
			FROM t_yoyaku y
			JOIN m_shitsujyou t
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN m_shisetsu s
			USING (localgovcode, shisetsucode)
			WHERE y.localgovcode=? AND y.yoyakunum=?
			ORDER BY shitsujyokbn, shitsujyocode, mencode";

		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_reserve_data(&$res)
	{
		global $aNinzu, $aOptionFee;

		$rec = $res[0];
		$atmp = $this->oSC->get_shitsujyo_info($rec['shisetsucode'], $rec['shitsujyocode']);
		$rec['ShisetsuName'] = $atmp['shisetsuname'];
		$rec['ShowDanjyoNinzuFlg'] = $atmp['showdanjyoninzuflg'];
		$rec['mencode'] = array();
		if ($rec['combino'] == 0) {
			$rec['mencode'][0] = 'ZZ';
		} else {
			$rec['shitsujyoname'] .= ' '.$this->oSC->get_combi_name($rec['shisetsucode'], $rec['shitsujyocode'], $rec['combino']);
		}
		$rec['baseFeeArr'] = $rec['baseshisetsufee'];
		$rec['genAppArr'] = $rec['genapplyflg'];
		$rec['genmenArr'] = $rec['genmen'];
		foreach ($res as $val)
		{
			if ($val['shitsujyokbn'] == '3') {
				$rec['shitsujyoname'] .= ' '.$val['shitsujyoname'];
				$rec['baseFeeArr'] .= '-'.$val['baseshisetsufee'];
				$rec['genAppArr'] .= '-'.$val['genapplyflg'];
				$rec['genmenArr'] .= '-'.$val['genmen'];
			} elseif ($val['combino'] != 0) {
				$rec['mencode'][] = $val['mencode'];
			}
		}

		$rec['MokutekiName'] = $this->oSC->get_purpose_name($rec['mokutekicode']);
		$rec['UseKbnName'] = $this->oSC->get_UseKbn_name($rec['usekbn']);
		$rec['DaikouStaffName'] = $this->oSC->get_staff_name($rec['daikouid']);
		$rec['AppDateView'] = $this->oSC->getDateView($rec['appdate']);
		$rec['AppTimeView'] = $this->oSC->getTimeView($rec['apptime']);
		$rec['UseDateView'] = $this->oSC->getDateView($rec['usedatefrom']);
		$rec['UseTime'] = $this->oSC->getTimeView($rec['usetimefrom']).' ～ '.$this->oSC->getTimeView($rec['usetimeto']);
		$rec['PayLimitDate'] = $this->oSC->getDateView($rec['shisetsupaylimitdate']);

		$sql = "SELECT basefee, shisetsufee, suuryo, suuryotani, surcharge,
			optionfee1, optionfee2, optionfee3, optionfee5,
			optionfee4, chousei_reason, bihinyoyakunum, bihinfee,
			useninzu, ninzu1, ninzu2, ninzu3, ninzu4, ninzu5,
			ninzu6, ninzu7, ninzu8, ninzu9, ninzu10, ninzu11,
			ninzu12, ninzu13, ninzu14, ninzu15, ninzu16
			FROM t_yoyakufeeshinsei
			WHERE localgovcode=? AND yoyakunum=?";
		$aWhere = array(_CITY_CODE_, $rec['yoyakunum']);
		$fee = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$fee['suuryo'] = intval($fee['suuryo']);
		$rec['useninzu'] = $fee['useninzu'];
		foreach ($aNinzu as $key => $val) {
			$rec[$key] = $fee[$key];
		}
		foreach ($aOptionFee as $key => $val) {
			$rec['OptionFee'.$key] = intval($fee['optionfee'.$key]);
		}
		$rec['ExtName'] = '';
		$rec['ExtRate'] = 100;
		if ($fee['surcharge'] != '') {
			$extra = $this->oSC->get_extracharge_info($fee['surcharge']);
			$rec['ExtName'] = $extra['extraname'];
			$rec['ExtRate'] = $extra['rate'];
			unset($extra);
		}
		$rec['Genmen'] = '';
		if ($fee['suuryotani'] != '') {
			list($genID, $genCode) = explode(',', $fee['suuryotani']
);
			$genmen = $this->oSC->get_genmen_info($genID, $genCode);
			$rec['Genmen'] = $genmen['rate'].','.$fee['suuryotani'];
			unset($genmen);
		}

		$rec['BaseShisetsuFee'] = intval($fee['basefee']);
		$rec['ShisetsuFee'] = intval($fee['shisetsufee']);
		$rec['BihinFee'] = intval($fee['bihinfee']);
		$rec['BihinNum'] = $fee['bihinyoyakunum'];
		$rec['SuuryoTani'] = $fee['suuryotani'];
		$rec['ReceptDate'] = '';
		$rec['ReceptPlace'] = $rec['shisetsucode'];
		$rec['ReceptStaffName'] = '';

		$oRS = new receipt_status($this->con, $rec['yoyakunum'], $rec['honyoyakukbn'], $fee['suuryo']);
		$rec['Receipt'] = $oRS->getReceiptFee();
		foreach ($rec['Receipt'] as $key => $val)
		{
			if ($key < 8) $rec['Receipt'][$key] = intval($val);
		}
		$rec['KinouGaku'] = $oRS->getT_YoyakuFeeUketsukeSumFee();
		$rec['SumFee'] = $fee['suuryo'];

		if ($rec['Receipt'][8] != '') {
			$rec['ReceptPlace'] = $rec['Receipt'][11];
			$rec['ReceptStaffName'] = $this->oSC->get_staff_name($rec['Receipt'][10]);
			if ($rec['Receipt'][0] > 0 || $fee['shisetsufee'] == 0) {
				$rec['ReceptDate'] = $rec['Receipt'][8];
			}
		}
		$rec['ChouseiRiyuu'] = $fee['chousei_reason'];
		unset($fee);

		$sql = "SELECT namesei, nameseikana FROM m_user
			WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $rec['userid']);
		$user = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec = array_merge($rec, $user);
		unset($user);

		if ($rec['userid'] === _UNREGISTED_USER_ID_) {
			$unreg = $this->oSC->get_unregisted_user_info($rec['yoyakunum']);
			if ($unreg) {
				$rec = array_merge($rec, $unreg);
				unset($unreg);
			}
		}

		return $rec;
	}

	function update_db_info(&$res, &$req)
	{
		$rec = $res[0];

		$oFB = new fee_base($this->con, $rec);
		$taxRate = $oFB->get_tax_rate();
		$genVal = array(0, '', '');
		if ($req['Genmen'] != '') {
			$genVal = explode(',', $req['Genmen']);
		}

		foreach ($res as $val)
		{
			$rate = 0;
			if ($genVal[0] != 0 && preg_match('/'.$genVal[1].'/', $val['genapplyflg'])) {
				$rate = $genVal[0];
				if ($genVal[1] == '3' && !preg_match('/'.$genVal[2].'/', $val['genmen'])) {
					$rate = 0;
				}
			}
			$fee = $oFB->calc_fee($val['baseshisetsufee'], $taxRate, $rate, $val['fractionflg']);
			$tax = $oFB->calc_tax($val['baseshisetsufee'], $taxRate, $rate, $val['fractionflg']);
			$tmp = $this->make_yoyaku_data($req, $fee, $tax);
			$rc = $this->oTB->update_yoyaku_by_code($tmp, $val['yoyakunum'], $val['shisetsucode'], $val['shitsujyocode'], $val['mencode']);
			if ($rc < 0) return false;
		}

		$total = $req['ShisetsuFee'] + $req['OptionFee1'] + $req['OptionFee2'] + $req['OptionFee3'] + $req['OptionFee4'] + $req['OptionFee5'] + $req['BihinFee'];
		$total = $oFB->calc_fee($total, $taxRate, 0, $req['fractionflg']);
		$tax = $oFB->calc_tax($total, $taxRate, 0);
		$tmp = $this->make_fee_data($req, $total, $tax);
		$rc = $this->oTB->update_by_yoyakunum('t_yoyakufeeshinsei', $tmp, $rec['yoyakunum']);
		if ($rc < 0) return false;

		$this->oTB->delete_by_yoyakunum('t_yoyakufeeuketsuke', $rec['yoyakunum']);

		$tax = $oFB->calc_tax_from_fee($req['ShiharaiFee'], $taxRate);
		$tmp = $this->make_uketsuke_data($req, $rec, $total, $tax);
		$rc = $this->oDB->insert('t_yoyakufeeuketsuke', $tmp);
		if ($rc < 0) return false;

		if ($req['BihinNum'] != '') {
			$total = $req['ShisetsuFee'] + $req['OptionFee1'] + $req['OptionFee2'] + $req['OptionFee3'] + $req['OptionFee4'] + $req['OptionFee5'];
			$total = $oFB->calc_fee($total, $taxRate, 0, $req['fractionflg']);
			$tmp = $this->make_bihin_data($req, $total);
			$rc = $this->oTB->update_by_yoyakunum('t_bihinyoyaku', $tmp, $req['BihinNum']);
			if ($rc < 0) return false;
		}

		$oLog = new log();
		$oLog->setLog($_SESSION['userid'].' Receipt');
		return true;
	}

	function make_yoyaku_data(&$req, $fee, $tax)
	{
		$dataset = array(
				'useukeflg' => '1',
				'honyoyakukbn' => '02',
				'mokutekicode' => $req['MokutekiCode'],
				'shisetsufee' => $fee,
				'shisetsutax' => $tax,
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);
		return $dataset;
	}

	function make_fee_data(&$req, $total, $tax)
	{
		global $aNinzu;

		$payfee = $req['Cash'] + $req['Chg'] + $req['Ticket'] + $req['KouzaFurikomi'] + $req['Others'] + $req['Jyutou'];
		$paykbn = $this->oTB->check_receipt_status($total, $payfee);

		$dataset = array(
				'shisetsufee' => $req['ShisetsuFee'],
				'suuryo' => $total,
				'tax' => $tax,
				'suuryotani' => '',
				'paykbn' => $paykbn,
				'optionfee1' => $req['OptionFee1'],
				'optionfee2' => $req['OptionFee2'],
				'optionfee3' => $req['OptionFee3'],
				'optionfee4' => $req['OptionFee4'],
				'optionfee5' => $req['OptionFee5'],
				'chousei_reason' => $req['ChouseiRiyuu'],
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);
		$dataset['useninzu'] = intval($req['useninzu']);
		$tmpNinzu = 0;
		foreach ($aNinzu as $key => $val)
		{
			$dataset[$key] = intval($req[$key]);
			if ($val[1]) $tmpNinzu += intval($req[$key]);
		}
		if ($req['ShowDanjyoNinzuFlg'] == 1) {
			$dataset['useninzu'] = $tmpNinzu;
		}
		if ($req['Genmen'] != '') {
			$info = explode(',', $req['Genmen']);
			$dataset['suuryotani'] = $info[1].','.$info[2];
		}
		return $dataset;
	}

	function make_uketsuke_data(&$req, &$rec, $total, $tax)
	{
		$dataset = array();
		$dataset['localgovcode'] = $rec['localgovcode'];
		$dataset['shisetsucode'] = $rec['shisetsucode'];
		$dataset['receptdate'] = $req['RecYear'].$req['RecMonth'].$req['RecDay'];
		$dataset['uketime'] = date('His');
		$dataset['yoyakunum'] = $rec['yoyakunum'];
		$dataset['receptnum'] = '01';
		$dataset['userid'] = $rec['userid'];
		$dataset['shisetsufee'] = $total;
		$dataset['tax'] = $tax;
		$dataset['cash'] = $req['Cash'];
		$dataset['chg'] = $req['Chg'];
		$dataset['ticket'] = $req['Ticket'];
		$dataset['kouzafurikomi'] = $req['KouzaFurikomi'];
		$dataset['others'] = $req['Others'];
		$dataset['jyutou'] = $req['Jyutou'];
		$dataset['receptid'] = $_SESSION['userid'];
		$dataset['receptplace'] = $req['ReceptPlace'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		return $dataset;
	}

	function make_bihin_data(&$req, $total)
	{
		$payflg = 0;
		$payfee = $req['Cash'] + $req['Chg'] + $req['Ticket'] + $req['KouzaFurikomi'] + $req['Others'] + $req['Jyutou'] - $total;
		$reqfee = $req['BihinFee'];

		if ($reqfee == 0) {
			$payflg = 2;
		} elseif ($payfee <= 0) {
			$payfee = 0;
		} elseif ($payfee >= $reqfee) {
			$payflg = 1;
			$payfee = $reqfee;
		} elseif ($payfee < $reqfee) {
			$payflg = 3;
		}

		$dataset = array();
		$dataset['receiptfee'] = $payfee;
		$dataset['receiptdate'] = time();
		$dataset['paykbn'] = $payflg;
		$dataset['upddate'] = time();
		$dataset['updid'] = $_SESSION['userid'];
		return $dataset;
	}
}
?>
