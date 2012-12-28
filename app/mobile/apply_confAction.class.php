<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  apply_confAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/apply.php';
require_once OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_calc.class.php';

class apply_confAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		global $aNinzu;

		$this->check_login('apply_conf');

		$msg = $this->check_session_data();
		if ($msg != '') $this->display_error_msg($msg);

		$oSC = new system_common($this->con);

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$MenCode = $_SESSION['Y_I']['mencode'];
		$CombiNo = $_SESSION['Y_I']['combino'];

		$row = $oSC->get_shitsujyo_info($ShisetsuCode, $ShitsujyoCode);

		$_SESSION['M_I']['ShisetsuName'] = $row['shisetsuname'];
		$_SESSION['M_I']['ShitsujyoName'] = $row['shitsujyoname'];
		$_SESSION['M_I']['CombiName'] = $oSC->get_combi_name($ShisetsuCode, $ShitsujyoCode, $CombiNo);

		$MokutekiCode = '';
		if (isset($_REQUEST['MokutekiCode'])) {
			$MokutekiCode = $_REQUEST['MokutekiCode'];
			$_SESSION['Y_I']['mokutekicode'] = $MokutekiCode;
		} else {
			$MokutekiCode = $_SESSION['Y_I']['mokutekicode'];
		}
		$_SESSION['M_I']['MokutekiName'] = $oSC->get_purpose_name($MokutekiCode);

		$oFC = new fee_calc($this->con, $_SESSION['Y_I']);
		$user_genmen = '';
		$gen = $oFC->get_user_gen();
		if ($gen) {
			$user_genmen = $gen['Rate'].',1,'.$gen['KoteiGenCode'];
		}
		$taxRate = $oFC->get_tax_rate();
		$frec = $oFC->get_shisetsu_fee($user_genmen);
		$_SESSION['Y_I']['TotalBaseFee'] = $frec['BaseFee'];
		$_SESSION['Y_I']['TotalShisetsuFee'] = $frec['ShisetsuFee'];
		$_SESSION['Y_I']['TotalFee'] = $oFC->calc_fee($frec['ShisetsuFee'], $taxRate, 0);
		$_SESSION['Y_I']['TotalTax'] = $oFC->calc_tax($frec['ShisetsuFee'], $taxRate, 0);

		$message = OR_CONFIRM_AND_CLICK;
		$condition = OR_REQUEST_CONFIRMATION.' :: 【'.$_SESSION['UNAME'].'】';
		$BackLink = '?op=daily';
		if ($_SESSION['SentakuMode'] != 1 && !isset($_SESSION['skip_pps'])) {
			$BackLink = '?op=mokuteki';
		} elseif (count($_SESSION['Y_I']['Fuzoku']) > 0) {
			$BackLink = '?op=fuzoku';
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('notice', $_SESSION['msg2']);
		$this->oSmarty->assign('ShisetsuName', $_SESSION['M_I']['ShisetsuName']);
		$this->oSmarty->assign('ShitsujyoName', $_SESSION['M_I']['ShitsujyoName']);
		$this->oSmarty->assign('CombiName', $CombiNo == 0 ? '-' : $_SESSION['M_I']['CombiName']);
		$this->oSmarty->assign('CombiNo', $CombiNo);
		$this->oSmarty->assign('UseDateDisp', $_SESSION['M_I']['UseDateDisp']);
		$this->oSmarty->assign('UseTime', $_SESSION['M_I']['UseTime']);
		$this->oSmarty->assign('FuzokuName', $_SESSION['M_I']['Fuzoku']);
		$this->oSmarty->assign('aNinzu', $aNinzu);
		$this->oSmarty->assign('showDanjyoNinzuFlg', $_SESSION['ShowDanjyoNinzuFlg']);
		$this->oSmarty->assign('Fee', number_format($_SESSION['Y_I']['TotalFee']));
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('apply_conf.tpl');
	}

	function check_session_data()
	{
		$hushData = array(
					'shisetsucode' => '施設コード',
					'shitsujyocode' => '室場コード',
					'mencode' => '利用単位コード',
					'combino' => '組合せ番号',
					'usedate' => '利用日',
					'UTFrom' => 'コマ開始時間情報',
					'UTTo' => 'コマ終了時間情報',
					'userid' => '利用者ID',
					'YoyakuKbn' => '予約区分'
				);
		$msg = '';

		foreach ($hushData as $key => $value)
		{
			if (!isset($_SESSION['Y_I'][$key])) $msg .= $value.'がありません。<br>';
		}
		if ($msg != '') $msg .= OR_RETURN_TO_USERMENU;
		return $msg;
	}
}
?>
