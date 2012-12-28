<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  予約申込
 *
 *  rsv_04_02_inputAction.class.php
 *  rsv_04_02_01.tpl
 *  rsv_04_02_02.tpl
 *  rsv_04_02_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/yoyaku_touroku.class.php';

define('P_I', 'rsv_01_02');
define('P2_I', 'rsv_03_01');

class rsv_04_02_inputAction extends adminAction
{
	private $oSC = null;
	private $oYT = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
		$this->oYT = new yoyaku_touroku($this->oDB, $_SESSION['userid']);
	}

	function execute()
	{
		global $aNinzu, $aShinsaFlg;

		$message = '';
		$counter = array(0, 0, 0, 0);
		$info = array();
		$template = 'rsv_04_02_01.tpl';

		$this->set_header_info();

		$scd = $_SESSION[P2_I]['ShisetsuCode'];
		$rcd = $_SESSION[P2_I]['ShitsujyoCode'];
		$mcd = $_SESSION[P2_I]['MenCode'];
		$cno = $_SESSION[P2_I]['CombiNo'];
		$UseDate = $_SESSION[P2_I]['UseDate'];

		$callUser = isset($_POST['callUser']) ? intval($_POST['callUser']) : 0;

		if (isset($_POST['nextBtn'])) {
			$info = $_POST; 
			$info['FeeKbn'] = $info['UseKbn'];
			$_SESSION['Y_I']['userid'] = $info['UserID'];
			$_SESSION['Y_I']['FeeKbn'] = $info['UseKbn'];
			$FuzokuCodeArr = array();
			if (isset($_POST['FuzokuCode'])) {
				foreach ($_POST['FuzokuCode'] as $val)
				{
					$FuzokuCodeArr[] = $val;
				}
			}
			$_SESSION['Y_I']['Fuzoku'] = $FuzokuCodeArr;

			$info['useninzu'] = intval($info['useninzu']);
			$tmpNinzu = 0;
			foreach ($aNinzu as $key => $val)
			{
				$info[$key] = intval($info[$key]);
				if ($val[1]) $tmpNinzu += $info[$key];
			}
			if ($_SESSION['Y_I']['ShowDanjyoNinzuFlg'] == 1) {
				$info['useninzu'] = $tmpNinzu;
			}
			$message = $this->oYT->check_ninzu($info, $_SESSION['Y_I'], $_SESSION['Y_I']['ShowDanjyoNinzuFlg']);
			if ($message == '') {
				$template = 'rsv_04_02_02.tpl';
				$_SESSION['Y_I']['suuryotani'] = '';
				$_SESSION['Y_I']['surcharge'] = '';
				$oFC = new fee_calc($this->con, $_SESSION['Y_I']);
				$user_genmen = '';
				$gen = $oFC->get_user_gen();
				if ($gen) {
					$user_genmen = $gen['Rate'].',1,'.$gen['KoteiGenCode'];
				}
				$frec = $oFC->get_shisetsu_fee($user_genmen);
				if ($info['YoyakuKbn'] != '01' && $info['YoyakuKbn'] != '02') {
					$frec['BaseFee'] = 0;
					$frec['ShisetsuFee'] = 0;
				}
				$taxRate = $oFC->get_tax_rate();
				$info['BaseFee'] = $frec['BaseFee'];
				$info['ShisetsuFee'] = $frec['ShisetsuFee'];
				$info['TotalFee'] = $oFC->calc_fee($frec['ShisetsuFee'], $taxRate, 0);
				$info['Tax'] = $oFC->calc_tax($frec['ShisetsuFee'], $taxRate, 0);
				$info['OriginalFee'] = $frec['BaseFee'];
				$info['Genmen'] = $user_genmen;
				$message = $this->oYT->check_user_validate($scd, $rcd, $info['UserID'], $info['YoyakuKbn']);
			}
		} elseif (isset($_POST['calcBtn'])) {
			$template = 'rsv_04_02_02.tpl';
			$info = $_POST; 
			$_SESSION['Y_I']['suuryotani'] = '';
			$_SESSION['Y_I']['surcharge'] = '';
			$_SESSION['Y_I']['FeeKbn'] = $info['FeeKbn'];
			$oFC = new fee_calc($this->con, $_SESSION['Y_I']);
			$frec = $oFC->get_shisetsu_fee($info['Genmen'], $info['Extracharge']);
			if ($info['YoyakuKbn'] != '01' && $info['YoyakuKbn'] != '02') {
				$frec['BaseFee'] = 0;
				$frec['ShisetsuFee'] = 0;
			}
			$taxRate = $oFC->get_tax_rate();
			$info['BaseFee'] = $frec['BaseFee'];
			$info['ShisetsuFee'] = $frec['ShisetsuFee'];
			$info['TotalFee'] = $oFC->calc_fee($frec['ShisetsuFee'], $taxRate, 0);
			$info['Tax'] = $oFC->calc_tax($frec['ShisetsuFee'], $taxRate, 0);
			$info['OriginalFee'] = $frec['BaseFee'];
		} elseif (isset($_POST['applyBtn'])) {
			$info = $_POST; 
			$_SESSION['Y_I']['mokutekicode'] = $info['MokutekiCode'];
			$_SESSION['Y_I']['useninzu'] = intval($info['useninzu']);
			foreach ($aNinzu as $key => $val)
			{
				$_SESSION['Y_I'][$key] = intval($info[$key]);
			}
			$_SESSION['Y_I']['YoyakuKbn'] = $info['YoyakuKbn'];
			$_SESSION['Y_I']['shinsaflg'] = $info['ShinsaFlg'];
			$_SESSION['Y_I']['yoyakuname'] = $info['YoyakuName'];
			$_SESSION['Y_I']['bikou'] = $info['Bikou'];
			$_SESSION['Y_I']['OriginalFee'] = $info['OriginalFee'];
			$_SESSION['Y_I']['TotalBaseFee'] = $info['BaseFee'];
			if ($info['OriginalFee'] != $info['BaseFee']) {
				$oFC = new fee_calc($this->con, $_SESSION['Y_I']);
				$taxRate = $oFC->get_tax_rate();
				$info['ShisetsuFee'] = $info['BaseFee'];
				$info['TotalFee'] = $oFC->calc_fee($info['BaseFee'], $taxRate, 0);
				$info['Tax'] = $oFC->calc_tax($info['BaseFee'], $taxRate, 0);
				$info['Extracharge'] = '';
				$info['Genmen'] = '';
				$_SESSION['Y_I']['suuryotani'] = '';
				$_SESSION['Y_I']['surcharge'] = '';
			}
			$_SESSION['Y_I']['TotalShisetsuFee'] = $info['ShisetsuFee'];
			$_SESSION['Y_I']['TotalFee'] = $info['TotalFee'];
			$_SESSION['Y_I']['TotalTax'] = $info['Tax'];
			$_SESSION['Y_I']['ExtraRate'] = 100;
			if ($info['Extracharge'] != '') {
				$tmpExt = explode(',', $info['Extracharge']);
				$_SESSION['Y_I']['ExtraRate'] = $tmpExt[0];
			}
			$_SESSION['Y_I']['GenmenRate'] = 0;
			if ($info['Genmen'] != '') {
				$tmpGen = explode(',', $info['Genmen']);
				$_SESSION['Y_I']['GenmenRate'] = $tmpGen[0];
			}
			if ($_SESSION['Y_I']['userid'] === _UNREGISTED_USER_ID_) {
				$_SESSION['Y_I']['UnregUserName'] = $info['UnregUserName'];
				$_SESSION['Y_I']['UnregAddress'] = $info['UnregAddress'];
				$_SESSION['Y_I']['UnregTel'] = $info['UnregTel'];
				$_SESSION['Y_I']['UnregContact'] = $info['UnregContact'];
			}
			$rc = $this->commit_yoyaku($_SESSION['Y_I']);
			if ($rc) {
				$template = 'rsv_04_02_03.tpl';
			} else {
				$template = 'rsv_04_02_02.tpl';
				$message = '申込できませんでした。';
			}
		} elseif (isset($_POST['repeatBtn'])) {
			unset($_SESSION['Y_I']);
			header('Location:index.php?op=rsv_03_01_status&repeat=1');
			return;
		} elseif (isset($_POST['againBtn'])) {
			unset($_SESSION[P2_I]);
			unset($_SESSION['Y_I']);
			header('Location:index.php?op=rsv_02_02_status');
			return;
		} elseif (isset($_POST['receiptBtn'])) {
			$yoyakuNum = $_SESSION['Y_I']['yoyakunum'];
			$_SESSION['rsv_01_04'] = array(
					'YoyakuNum' => $yoyakuNum,
					'FromYear' => substr($UseDate, 0, 4),
					'FromMonth' => substr($UseDate, 4, 2),
					'FromDay' => substr($UseDate, 6, 2),
					'ShisetsuCode' => $scd,
					'ShitsujyoCode' => $rcd,
					'searchBtn' => '検索'
				);
			unset($_SESSION[P_I]);
			unset($_SESSION[P2_I]);
			unset($_SESSION['Y_I']);
			header('Location:index.php?op=rsv_02_05_receipt&YoyakuNum='.$yoyakuNum);
			return;
		} elseif (isset($_POST['backBtn'])) {
			$user = $this->oSC->set_user_status($_POST['UserID'], $UseDate);
			$info = array_merge($_POST, $user); 
		} elseif ($callUser == 1) {
			$user = $this->oSC->set_user_status($_POST['UserID'], $UseDate);
			$info = array_merge($_POST, $user); 
		} else {
			$_SESSION['Y_I'] = array();
			$this->set_yoyaku_info($_SESSION[P2_I], $_SESSION['Y_I']);
		}

		$aYoyakuKbn = $this->oSC->get_codename_options('YoyakuKbn');
		$aPurpose = $this->oYT->get_stj_purpose_options($scd, $rcd, $cno);
		$aFuzoku = $this->oYT->get_fuzoku_options($scd, $rcd, $cno, $UseDate, $_SESSION['Y_I']['usetimefrom'], $_SESSION['Y_I']['usetimeto']);

		if ($template == 'rsv_04_02_01.tpl') {
			$Ifselectflg = $this->oYT->check_lots_period($scd, $rcd, $mcd, $UseDate);
			if ($Ifselectflg == 0) unset($aYoyakuKbn['01']);
			$this->oSmarty->assign('Ifselectflg', $Ifselectflg);
		} else {
			$counter = $this->oYT->get_user_count($info['UserID'], $scd, $rcd, $UseDate);
			$info['ShisetsuUserCount'] = $counter[0];
			$info['ShitsujyoUserCount'] = $counter[1];
			if ($info['YoyakuKbn'] == '01') {
				$info['ShisetsuUserCount'] = $counter[2];
				$info['ShitsujyoUserCount'] = $counter[3];
			}
			$aFeeKbn = $this->oYT->get_stj_feekbn_options($scd, $rcd, $cno, $UseDate);
			$aGenmen = $this->oYT->get_all_genmen($scd, $rcd, $UseDate, $info['UserID']);
			$aExtra = $this->oYT->get_extracharge_options($scd, $rcd);
			$this->oSmarty->assign('aFeeKbn', $aFeeKbn);
			$this->oSmarty->assign('aExtra', $aExtra);
			$this->oSmarty->assign('aGenmen', $aGenmen);
		}

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('aFuzoku', $aFuzoku);
		$this->oSmarty->assign('aPurpose', $aPurpose);
		$this->oSmarty->assign('aYoyakuKbn', $aYoyakuKbn);
		$this->oSmarty->assign('aNinzu', $aNinzu);
		$this->oSmarty->assign('aShinsaFlg', $aShinsaFlg);
		$this->oSmarty->assign('info', $info);
		$this->oSmarty->assign('aMain', $_SESSION['Y_I']);
		$this->oSmarty->display($template);
	}

	function set_yoyaku_info(&$src, &$dest)
	{
		$dest['usedate'] = $src['UseDate'];
		$dest['localgovcode'] = $src['LocalGovCode'];
		$dest['shisetsucode'] = $src['ShisetsuCode'];
		$dest['shitsujyocode'] = $src['ShitsujyoCode'];
		$dest['mencode'] = $src['MenCode'];
		$dest['combino'] = $src['CombiNo'];
		$dest['ShisetsuName'] = $src['ShisetsuName'];
		$dest['ShitsujyoName'] = $src['ShitsujyoName'];
		$dest['MenName'] = $src['MenName'];
		$dest['ShowDanjyoNinzuFlg'] = $src['ShowDanjyoNinzuFlg'];
		$dest['ShinsaFlg'] = $src['ShinsaFlg'];
		$dest['shinsaflg'] = $src['ShinsaFlg'];
		$dest['genapplyflg'] = $src['GenApplyFlg'];
		$dest['fractionflg'] = $src['FractionFlg'];
		$dest['DateView'] = $src['DateView'];

		$dest['UTFrom'] = $dest['UTTo'] = array();
		$timeFrom = '000000';
		$timeTo = '000000';
		$i = 0;
		foreach ($src['aTimeKoma'] as $key => $val)
		{
			if ($val['set']) {
				if ($i == 0) $timeFrom = $val['From'];
				$timeTo = $val['To'];
				$dest['UTFrom'][$key] = substr($val['From'], 0, 4);
				$dest['UTTo'][$key] = substr($val['To'], 0, 4);
				++$i;
			}
		}
		$dest['usetimefrom'] = $timeFrom;
		$dest['UseTimeFromView'] = $this->oSC->getTimeView($timeFrom);
		$dest['usetimeto'] = $timeTo;
		$dest['UseTimeToView'] = $this->oSC->getTimeView($timeTo);
		$dest['komasu'] = $i;
	}

	function commit_yoyaku(&$ses)
	{
		$YoyakuNum = '';

		$this->con->query('START TRANSACTION');

		if ($ses['YoyakuKbn'] == '01') {
			$check = $this->oYT->check_duplicate_lot($ses);
			if ($check == 11) {
				$YoyakuNum = $this->oYT->emit_yoyaku_number($ses);
				if (!$YoyakuNum) {
					$this->con->query('ROLLBACK');
					return false;
				}
				if (!$this->oYT->insert_lot($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					return false;
				}
				if (!$this->oYT->insert_yoyaku_shinsei($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					return false;
				}
				$ses['yoyakunum'] = $YoyakuNum;
			} else {
				$this->con->query('ROLLBACK');
				return false;
			}
		} else {
			$check = $this->oYT->check_duplicate_yoyaku($ses);
			if ($check == 10) {
				$YoyakuNum = $this->oYT->emit_yoyaku_number($ses);
				if (!$YoyakuNum) {
					$this->con->query('ROLLBACK');
					return false;
				}
				if (!$this->oYT->set_yoyaku($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					return false;
				}
				if (!$this->oYT->insert_yoyaku_shinsei($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					return false;
				}
				$ses['yoyakunum'] = $YoyakuNum;
			} else {
				$this->con->query('ROLLBACK');
				return false;
			}
		}
		$this->con->query('COMMIT');

		if ($ses['userid'] === _UNREGISTED_USER_ID_) {
			$this->oYT->insert_unregisted_user($YoyakuNum, $ses);
		}
		return true;
	}
}
?>
