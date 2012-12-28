<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  キャンセル
 *
 *  rsv_02_04_cancelAction.class.php
 *  rsv_02_04.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';
require OPENREAF_ROOT_PATH.'/app/class/touroku_base.class.php';

class rsv_02_04_cancelAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		global $aKinshu;

		$message = '';
		$req = array();
		$code = '';
		$rate = 0;

		$this->set_header_info();

		$oTB = new touroku_base($this->oDB, $_SESSION['userid']);

		$YoyakuNum = $_REQUEST['YoyakuNum'];

		if (isset($_POST)) $req = $_POST;

		if (isset($req['cancelBtn'])) {
			list($rate, $code) = explode(',', $req['CancelReason']);
			$rec = $this->get_reserve_data($YoyakuNum);
			$enable_cancel_fee = ($_SESSION['BACK_OP'] == 'rsv_01_04' && $rec['CancelFeeFlg'] == '1') ? true : false;
			$rc = 0;
			if ($rec['class'] == 'y') {
				$rc = $oTB->cancel_yoyaku($YoyakuNum, $code, $req['Bikou']);
			} else {
				$dataset = array();
				$dataset['bikou'] = $req['Bikou'];
				$dataset['canceljiyucode'] = $code;
				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];
				$rc = $oTB->update_by_yoyakunum('h_yoyaku', $dataset, $YoyakuNum);
			}
			if ($rc < 0) {
				$message = '取消できませんでした。';
			} else {
				$message = '取消処理が完了しました。';

				if ($enable_cancel_fee && ($rec['honyoyakukbn'] == '02' || intval($req['CancelFee'])) > 0) {
					$this->con->autoCommit(false);
					$tmp = $this->make_yoyaku_data();
					$rc = $oTB->update_by_yoyakunum('h_yoyaku', $tmp, $YoyakuNum);
					if ($rc < 0) {
						$this->con->rollback();
						$message = 'キャンセル料の処理ができませんでした。';
					} else {
						$tmp = $this->make_uketsuke_data($req, $rec);
						$oTB->delete_by_yoyakunum('t_yoyakufeeuketsuke', $YoyakuNum);
						$rc = $this->oDB->insert('t_yoyakufeeuketsuke', $tmp);
						if ($rc < 0) {
							$this->con->rollback();
							$message = 'キャンセル料の処理ができませんでした。';
						} else {
							$this->con->commit();
						}
					}
				}
			}
		}

		$rec = $this->get_reserve_data($YoyakuNum);

		$req['CancelReason'] = isset($req['CancelReason']) ? $req['CancelReason'] : $rec['Rate'].','.$rec['canceljiyucode'];

		$aCancel = $this->get_cancel_options(true);
		$aShisetsu = $this->oPrivilege->get_shisetsu_list();
		if (isset($aShisetsu[$rec['shisetsucode']])) {
			$aShisetsu[$rec['shisetsucode']] = '- 利用施設に同じ -';
		}
		$datetime = time();
		if ($rec['ReceptDate'] != '') {
			$datetime = strtotime($rec['ReceptDate']);
		}

		$template = ($_SESSION['BACK_OP'] == 'rsv_01_04' && $rec['CancelFeeFlg'] == '1') ? 'rsv_02_04_02.tpl' : 'rsv_02_04_01.tpl';

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('req', $req);
		$this->oSmarty->assign('aKinshu', $aKinshu);
		$this->oSmarty->assign('aCancel', $aCancel);
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('datetime', $datetime);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('fmode', $_SESSION['BACK_OP']);
		$this->oSmarty->assign('returnUrl', 'index.php?op='.$_SESSION['BACK_OP'].'_search&back=1');
		$this->oSmarty->display($template);
	}

	function get_reserve_data($YoyakuNum)
	{
		global $aNinzu, $aPayKbn;

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
		$rec['usedate'] = $rec['usedatefrom'];
		$sTemp = $this->oSC->get_shisetsu_data($rec['shisetsucode']);
		$rec['ShisetsuName'] = $sTemp['shisetsuname'];
		$rec['CancelFeeFlg'] = $sTemp['cancelfeeflg'];
		$rec['fractionflg'] = $sTemp['fractionflg'];
		unset($sTemp);
		if ($rec['combino'] != 0) {
			$rec['shitsujyoname'] .= ' '.$this->oSC->get_combi_name($rec['shisetsucode'], $rec['shitsujyocode'], $rec['combino']);
		}
		foreach ($res as $val)
		{
			if ($val['shitsujyokbn'] == '3') {
				$rec['shitsujyoname'] .= ' '.$val['shitsujyoname'];
			}
		}
		unset($res);

		$aStaff = $this->oSC->get_staff_name_array();

		$rec['HonYoyakuKbnName'] = $this->oSC->get_HonYoyakuKbn_name($rec['honyoyakukbn']);
		$rec['MokutekiName'] = $this->oSC->get_purpose_name($rec['mokutekicode']);
		$rec['LstUpdDate'] = $this->oSC->getDateView($rec['lstupddate']);
		$rec['LstUpdTime'] = $this->oSC->getTimeView($rec['lstupdtime']);
		$rec['CancelStaffName'] = isset($aStaff[$rec['cancelstaffid']]) ? $aStaff[$rec['cancelstaffid']] : $rec['cancelstaffid'];
		$rec['DaikouStaffName'] = isset($aStaff[$rec['daikouid']]) ? $aStaff[$rec['daikouid']] : $rec['daikouid'];
		$rec['AppDateView'] = $this->oSC->getDateView($rec['appdate']);
		$rec['AppTimeView'] = $this->oSC->getTimeView($rec['apptime']);
		$rec['UseDateView'] = $this->oSC->getDateView($rec['usedatefrom']);
		$rec['UseTime'] = $this->oSC->getTimeView($rec['usetimefrom']).' ～ '.$this->oSC->getTimeView($rec['usetimeto']);

		$sql = "SELECT basefee, suuryo, suuryotani,
			optionfee4, chousei_reason,
			useninzu, ninzu1, ninzu2, ninzu3, ninzu4, ninzu5,
			ninzu6, ninzu7, ninzu8, ninzu9, ninzu10,ninzu11,
			ninzu12, ninzu13, ninzu14, ninzu15, ninzu16
			FROM t_yoyakufeeshinsei
			WHERE localgovcode=? AND yoyakunum=?
			UNION SELECT basefee, suuryo, suuryotani,
			optionfee4, chousei_reason,
			useninzu, ninzu1, ninzu2, ninzu3, ninzu4, ninzu5,
			ninzu6, ninzu7, ninzu8, ninzu9, ninzu10,ninzu11,
			ninzu12, ninzu13, ninzu14, ninzu15, ninzu16
			FROM h_fee
			WHERE localgovcode=? AND yoyakunum=?";
		$fee = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$fee['suuryo'] = intval($fee['suuryo']);
		$rec['useninzu'] = $fee['useninzu'];
		foreach ($aNinzu as $key => $val)
		{
			$rec[$key] = $fee[$key];
		}

		$rec['BaseShisetsuFee'] = intval($fee['basefee']);
		$rec['ShisetsuFee'] = $fee['suuryo'];
		$rec['ReceptDate'] = '';
		$rec['ReceptPlace'] = $rec['shisetsucode'];

		$oRS = new receipt_status($this->con, $rec['yoyakunum'], $rec['honyoyakukbn'], $fee['suuryo']);
		$paykbn = $oRS->getReceiptStatus($rec['class']=='h');
		$rec['PayKbnName'] = $aPayKbn[$paykbn];
		$rec['Receipt'] = $oRS->getReceiptFee();
		foreach ($rec['Receipt'] as $key => $val)
		{
			if ($key < 8) $rec['Receipt'][$key] = intval($val);
		}
		$rec['ChouseiGaku'] = intval($fee['optionfee4']);
		$rec['ChouseiRiyuu'] = $fee['chousei_reason'];
		$rec['KinouGaku'] = $oRS->getT_YoyakuFeeUketsukeSumFee();
		$rec['SumFee'] = $rec['Receipt'][7];
		$rec['CancelFee'] = $rec['Receipt'][7];

		if ($rec['Receipt'][8] != '') {
			$rec['ReceptPlace'] = $rec['Receipt'][11];
			if ($rec['Receipt'][0] > 0 || $fee['suuryo'] == 0) {
				$rec['ReceptDate'] = $rec['Receipt'][8];
			}
		}
		unset($fee);

		$sql = "SELECT namesei, nameseikana FROM m_user
			WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $rec['userid']);
		$user = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec = array_merge($rec, $user);
		unset($user);

		$rec['CancelDesc'] = '';
		$rec['Rate'] = 100;
		if ($rec['canceljiyucode']) {
			$sql = "SELECT rate FROM m_canceljiyucode
				WHERE localgovcode=? AND cancelcode=?";
			$aWhere = array(_CITY_CODE_, $rec['canceljiyucode']);
			$rec['Rate'] = $this->con->getOne($sql, $aWhere);
			if ($rec['honyoyakukbn'] == '02') {
				if ($rec['CancelFee'] == 0) {
					$rec['CancelDesc'] = 'キャンセル料はかかかりません。';
				} else {
					$rec['CancelDesc'] = 'キャンセル料は施設使用料の '.strval(100-$rec['Rate']).'%です。';
				}
			}
		}

		if ($rec['userid'] === _UNREGISTED_USER_ID_) {
			$unreg = $this->oSC->get_unregisted_user_info($rec['yoyakunum']);
			if ($unreg) {
				$rec = array_merge($rec, $unreg);
				unset($unreg);
			}
		}

		return $rec;
	}

	function make_yoyaku_data()
	{
		$dataset = array(
				'useukeflg' => '1',
				'honyoyakukbn' => '02',
				'upddate' => date('Ymd'),
				'updtime' => date('His'),
				'updid' => $_SESSION['userid']
				);
		return $dataset;
	}

	function make_uketsuke_data(&$req, &$rec)
	{
		$oFB = new fee_base($this->con, $rec);
		$taxRate = $oFB->get_tax_rate();

		$dataset = array();
		$dataset['localgovcode'] = $rec['localgovcode'];
		$dataset['shisetsucode'] = $rec['shisetsucode'];
		$dataset['receptdate'] = $req['RecYear'].$req['RecMonth'].$req['RecDay'];
		$dataset['uketime'] = date('His');
		$dataset['yoyakunum'] = $rec['yoyakunum'];
		$dataset['receptnum'] = '01';
		$dataset['userid'] = $rec['userid'];
		$dataset['shisetsufee'] = $req['ShisetsuFee'];
		$dataset['cancelfee'] = $req['CancelFee'];
		$dataset['tax'] = $oFB->calc_tax_from_fee($req['ShiharaiFee'], $taxRate);
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

	function get_cancel_options($rate=false)
	{
		$sql = "SELECT cancelcode, canceljiyuname, rate FROM m_canceljiyucode ORDER BY cancelcode";
		$res = $this->con->getAll($sql);
		$recs = array();
		foreach($res as $val)
		{
			$key = $val[2].','.$val[0];
			$recs[$key] = $val[1];
			if ($rate) {
				$recs[$key].= '-還付率【'.$val[2].'%】';
			}
		}
		return $recs;
	}
}
?>
