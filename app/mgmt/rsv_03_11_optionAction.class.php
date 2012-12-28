<?php
/*
 *  Copyright 2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  オプション料金設定
 *
 *  rsv_03_11_optionAction.class.php
 *  rsv_03_11.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';
require OPENREAF_ROOT_PATH.'/app/class/time_schedule.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_calc.class.php';
require OPENREAF_ROOT_PATH.'/app/class/touroku_base.class.php';

class rsv_03_11_optionAction extends adminAction
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
		global $aOptionFee;

		$message = '';
		$YoyakuNum = '';
		$back_op = '';

		$this->set_header_info();

		$req = isset($_POST) ? $_POST : array();

		if (isset($req['yoyakunum'])) {
			$YoyakuNum = $req['yoyakunum'];
			$back_op = $req['back_op'];
		} elseif (isset($req['addfeeBtn'])) {
			$YoyakuNum = $req['YoyakuNum'][key($req['addfeeBtn'])];
			$back_op = 'rsv_03_07_receipt';
		} else {
			$YoyakuNum = $req['YoyakuNum'];
			$back_op = 'rsv_02_05_receipt';
		}

		$rec = $this->get_reserve_data($YoyakuNum);

		$res = $this->get_option_data($rec['shisetsucode'], $rec['usedate']);
		foreach ($res as $n => $val)
		{
			$res[$n]['aFeeKbn'] = $this->oTB->get_stj_feekbn_options($rec['shisetsucode'], $val['shitsujyocode'], 0, $rec['usedate']);
			$res[$n]['aGenmen'] = $this->oTB->get_all_genmen($rec['shisetsucode'], $val['shitsujyocode'], 0, $rec['usedate'], $rec['userid']);
			$res[$n]['aExtra'] = $this->oTB->get_extracharge_options($rec['shisetsucode'], $val['shitsujyocode']);
			$res[$n]['combino'] = 0;
			$res[$n]['Fuzoku'] = array();
			$res[$n]['usedate'] = $rec['usedate'];
			$res[$n]['userid'] = $rec['userid'];
			$res[$n]['fractionflg'] = $rec['fractionflg'];
		}

		if (isset($req['calcBtn'])) {
			$total = 0;
			foreach ($res as $n => $val)
			{
				$rcd = $val['shitsujyocode'];
				$req['basefee'][$rcd] = 0;
				$req['billingfee'][$rcd] = 0;
				$req['tax'][$rcd] = 0;
				$amount = intval($req['amount'][$rcd]);
				if ($amount == 0) continue;

				$usetimefrom = $req['FromHour'][$rcd].$req['FromMinute'][$rcd].'00';
				$usetimeto = $req['ToHour'][$rcd].$req['ToMinute'][$rcd].'00';
				$oTS = new time_schedule($this->con, _CITY_CODE_, $val['shisetsucode'], $rcd, false);
				$aTimeKoma = $oTS->get_time_schedule_ptn($val['usedate'], _PRIVILEGE_TIME_);

				$res[$n]['UTFrom'] = $res[$n]['UTTo'] = array();
				foreach ($aTimeKoma as $key => $koma)
				{
					if ($usetimefrom <= $koma['From'] && $koma['To'] <= $usetimeto) {
						$res[$n]['UTFrom'][$key] = substr($koma['From'], 0, 4);
						$res[$n]['UTTo'][$key] = substr($koma['To'], 0, 4);
					}
				}
				$res[$n]['FeeKbn'] = $req['feekbn'][$rcd];
				$frec = $this->get_option_fee($res[$n], $amount, $req['genmen'][$rcd], $req['extra'][$rcd]);
				$req['basefee'][$rcd] = $frec['basefee'];
				$req['billingfee'][$rcd] = $frec['billingfee'];
				$req['tax'][$rcd] = $frec['tax'];
				$total += $frec['billingfee'];
			}
			$req['optionfee5'] = $total;
		} elseif (isset($req['applyBtn'])) {
			$this->con->autoCommit(false);
			if ($this->update_db_info($rec, $res, $req)) {
				$this->con->commit();
				$message = '登録しました。';
			} else {
				$this->con->rollback();
				$message = '登録できませんでした。';
			}
		} else {
			$req['FromHour'] = $req['FromMinute'] = $req['ToHour'] = $req['ToMinute'] = array();
			$req['amount'] = $req['basefee'] = $req['billingfee'] = $req['tax'] = array();
			$req['feekbn'] = $req['extra'] = $req['genmen'] = array();
			foreach ($aOptionFee as $idx => $val) {
				$key = 'optionfee'.$idx;
				$req[$key] = $rec[$key];
			}
			$req['chousei_reason'] = $rec['chousei_reason'];
			$rows = $this->get_fee_data($rec['yoyakunum']);
			foreach ($res as $val)
			{
				$rcd = $val['shitsujyocode'];

				$timefrom = $rec['usetimefrom'];
				$timeto = $rec['usetimeto'];
				$genmen = $rec['suuryotani'];
				$extra = $rec['surcharge'];
				$req['amount'][$rcd] = 0;
				$req['feekbn'][$rcd] = $rec['usekbn'];
				$req['basefee'][$rcd] = 0;
				$req['billingfee'][$rcd] = 0;
				$req['tax'][$rcd] = 0;

				if (isset($rows[$rcd])) {
					$timefrom = $rows[$rcd]['usetimefrom'];
					$timeto = $rows[$rcd]['usetimeto'];
					$genmen = $rows[$rcd]['genmen'];
					$extra =$rows[$rcd]['surcharge'];
					$req['amount'][$rcd] = $rows[$rcd]['amount'];
					$req['feekbn'][$rcd] = $rows[$rcd]['feekbn'];
					$req['basefee'][$rcd] = intval($rows[$rcd]['basefee']);
					$req['billingfee'][$rcd] = intval($rows[$rcd]['billingfee']);
					$req['tax'][$rcd] = intval($rows[$rcd]['tax']);
				}

				$req['FromHour'][$rcd] = substr($timefrom, 0, 2);
				$req['FromMinute'][$rcd] = substr($timefrom, 2, 2);
				$req['ToHour'][$rcd] = substr($timeto, 0, 2);
				$req['ToMinute'][$rcd] = substr($timeto, 2, 2);
				$req['extra'][$rcd] = '';
				if ($extra != '') {
					$tmpval = $this->oSC->get_extracharge_info($extra);
					$req['extra'][$rcd] = $tmpval['rate'].','.$extra;
				}
				$req['genmen'][$rcd] = '';
				if ($genmen != '') {
					list($genID, $genCode) = explode(',', $genmen);
					$tmpval = $this->oSC->get_genmen_info($genID, $genCode);
					$req['genmen'][$rcd] = $tmpval['rate'].','.$genmen;
				}
			}
		}

		$aHours = array();
		for ($i = 0; $i < 24; ++$i) {
			$val = sprintf('%02d', $i);
			$aHours[$val] = $val;
		}
		$aMinutes = array();
		for ($i = 0; $i < 60; $i+=5) {
			$val = sprintf('%02d', $i);
			$aMinutes[$val] = $val;
		}

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('aOptionFee', $aOptionFee);
		$this->oSmarty->assign('aHours', $aHours);
		$this->oSmarty->assign('aMinutes', $aMinutes);
		$this->oSmarty->assign('req', $req);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('back_op', $back_op);
		$this->oSmarty->display('rsv_03_11.tpl');
	}

	function get_reserve_data($YoyakuNum)
	{
		$aWhere = array(_CITY_CODE_, $YoyakuNum);

		$sql = "SELECT y.localgovcode, y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom AS usedate, y.usetimefrom, y.usetimeto,
			y.honyoyakukbn, y.yoyakunum, y.userid, y.usekbn,
			s.shitsujyokbn, s.shitsujyoname,
			f.shisetsufee, f.suuryo, f.suuryotani, f.surcharge,
			f.bihinfee, f.optionfee1, f.optionfee2, f.optionfee3,
			f.optionfee4, f.optionfee5, f.chousei_reason 
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			WHERE y.localgovcode=? AND y.yoyakunum=?
			ORDER BY shitsujyokbn, shitsujyocode";

		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$rec = $res[0];
		$atmp = $this->oSC->get_shitsujyo_info($rec['shisetsucode'], $rec['shitsujyocode']);
		$rec['ShisetsuName'] = $atmp['shisetsuname'];
		$rec['fractionflg'] = $atmp['fractionflg'];
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

		$rec['UseDateView'] = $this->oSC->getDateView($rec['usedate']);
		$rec['UseTime'] = $this->oSC->getTimeView($rec['usetimefrom']).' ～ '.$this->oSC->getTimeView($rec['usetimeto']);

		$oRS = new receipt_status($this->con, $rec['yoyakunum'], $rec['honyoyakukbn'], $rec['suuryo']);
		$receipt = $oRS->getReceiptFee();
		$rec['receipt'] = false;
		if ($receipt[8] != '') $rec['receipt'] = true;

		return $rec;
	}

	function get_option_data($scd, $usedate)
	{
		$sql = 'SELECT localgovcode, shisetsucode, shitsujyocode,
			shitsujyoname, shitsujyoskbcode,
			genapplyflg, genmen, extracharge
			FROM m_shitsujyou
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyokbn=?
			AND (haishidate>? OR haishidate=? OR haishidate IS NULL)
			ORDER BY shitsujyoskbcode, shitsujyocode';
		$aWhere = array(_CITY_CODE_, $scd, '4', $usedate, '');
		return $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function get_fee_data($yoyakunum)
	{
		$sql = 'SELECT shitsujyocode,
			usetimefrom, usetimeto, amount,
			basefee, tax, billingfee, feekbn, genmen, surcharge
			FROM t_yoyaku_fee_option
			WHERE localgovcode=? AND yoyakunum=?
			ORDER BY shitsujyocode';
		$aWhere = array(_CITY_CODE_, $yoyakunum);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$recs = array();
		foreach ($res as $val) $recs[$val['shitsujyocode']] = $val;
		unset($res);
		return $recs;
	}

	function get_option_fee(&$src, $amount, $genInfo, $extra)
	{
		$rec = array('basefee' => 0, 'billingfee' => 0, 'tax' => 0);

		if (empty($src['UTFrom'])) return $rec;

		$oFC = new fee_calc($this->con, $src);

		$fee = $oFC->get_price($src['shitsujyocode'], $src['combino']) * $amount;

		if ($extra != '') {
			$tmpExt = explode(',', $extra);
			$extRate = -1*(intval($tmpExt[0]) - 100);
			$fee = $oFC->calc_fee($fee, 0, $extRate, $src['fractionflg']);
		}
		$rec['basefee'] = $fee;

		if ($genInfo != '') {
			$tmpGen = explode(',', $genInfo);
			if (preg_match('/'.$tmpGen[1].'/', $src['genapplyflg'])) {
				$genRate = intval($tmpGen[0]);
				$fee = $oFC->calc_fee($fee, 0, $genRate, $src['fractionflg']);
			}
		}
		$rec['billingfee'] = $fee;

		$taxRate = $oFC->get_tax_rate();
		$rec['tax'] = $oFC->calc_tax($fee, $taxRate, 0);

		return $rec;
	}

	function update_db_info(&$rec, &$res, &$req)
	{
		$sql ='DELETE FROM t_yoyaku_fee_option WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array($rec['localgovcode'], $rec['yoyakunum']);
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$dataset = array();
		$dataset['localgovcode'] = $rec['localgovcode'];
		$dataset['shisetsucode'] = $rec['shisetsucode'];
		$dataset['yoyakunum'] = $rec['yoyakunum'];
		$dataset['usedate'] = $rec['usedate'];
		$dataset['updid'] = $_SESSION['userid'];

		$total = 0;
		foreach ($res as $val)
		{
			$rcd = $val['shitsujyocode'];
			$amount = intval($req['amount'][$rcd]);
			if ($amount == 0) continue;

			$dataset['shitsujyocode'] = $rcd;
			$dataset['usetimefrom'] = $req['FromHour'][$rcd].$req['FromMinute'][$rcd].'00';
			$dataset['usetimeto'] = $req['ToHour'][$rcd].$req['ToMinute'][$rcd].'00';
			$dataset['amount'] = $amount;
			$dataset['basefee'] = intval($req['basefee'][$rcd]);
			$dataset['tax'] = intval($req['tax'][$rcd]);
			$dataset['billingfee'] = intval($req['billingfee'][$rcd]);
			$dataset['feekbn'] = $req['feekbn'][$rcd];
			$total += $dataset['billingfee'];
			$dataset['genmen'] = '';
			if ($req['genmen'][$rcd] != '') {
				$tmp = explode(',', $req['genmen'][$rcd]);
				$dataset['genmen'] = $tmp[1].','.$tmp[2];
			}
			$dataset['surcharge'] = '';
			if ($req['extra'][$rcd] != '') {
				$tmp = explode(',', $req['extra'][$rcd]);
				$dataset['surcharge'] = $tmp[1];
			}
			$dataset['upddate'] = date('Ymd');
			$dataset['updtime'] = date('His');
			$rc = $this->oDB->insert('t_yoyaku_fee_option', $dataset);
			if ($rc < 0) return false;
		}

		$where = "localgovcode='".$rec['localgovcode']."' AND yoyakunum='".$rec['yoyakunum']."'";

		$oFB = new fee_base($this->con, $rec);
		$taxRate = $oFB->get_tax_rate();
		$sumfee = $rec['shisetsufee'] + $rec['bihinfee'] + $req['optionfee1'] + $req['optionfee2'] + $req['optionfee3'] + $req['optionfee4'] + $total;
		$fee = $oFB->calc_fee($sumfee, $taxRate, 0, 0);

		$values = array('suuryo' => $fee,
				'optionfee1' => $req['optionfee1'],
				'optionfee2' => $req['optionfee2'],
				'optionfee3' => $req['optionfee3'],
				'optionfee4' => $req['optionfee4'],
				'optionfee5' => $total,
				'chousei_reason' => $req['chousei_reason'],
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']);
		$values['tax'] = $oFB->calc_tax($sumfee, $taxRate, 0);
		$rc = $this->oDB->update('t_yoyakufeeshinsei', $values, $where);
	       	if ($rc < 0) return false;

		if ($rec['receipt']) {
			$values = array('shisetsufee' => $fee,
					'upddate' => date('Ymd'),
					'updtime' => date('His'),
					'updid' => $_SESSION['userid']);
			$rc = $this->oDB->update('t_yoyakufeeuketsuke', $values, $where);
	       		if ($rc < 0) return false;
		}
		$req['optionfee5'] = $total;
		return true;
	}
}
?>
