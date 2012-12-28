<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  dailyAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/monthly.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';

class dailyAction extends Action
{
	private $oSC = null;

	function __construct($type)
	{
		parent::__construct($type);

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		if (!isset($_SESSION['SentakuMode'])) $this->display_error_msg(OR_RETURN_TO_TOP);

		$LocalGovCode = _CITY_CODE_;
		$_SESSION['Y_I']['localgovcode'] = $LocalGovCode;

		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$MenCode = $_SESSION['Y_I']['mencode'];
		$CombiNo = $_SESSION['Y_I']['combino'];

		$UseDate = '';
		$UseDateDisp = '';
		if (isset($_REQUEST['UseDate'])) {
			$UseDate = $_REQUEST['UseDate'];
			$_SESSION['Y_I']['usedate'] = $UseDate;
			$UseDateDisp = $this->oSC->date4lang($_REQUEST['UseDate'], _LANGUAGE_);
			$_SESSION['M_I']['UseDateDisp'] = $UseDateDisp;
		} else {
			$UseDate = $_SESSION['Y_I']['usedate'];
			$UseDateDisp = $_SESSION['M_I']['UseDateDisp'];
		}

		// 利用者登録期限と利用日のチェック
		if (isset($_SESSION['UID'])) {
			$msg = $this->check_user_expiration($UseDate);
			if ($msg != '') {
				$this->display_error_msg($msg, '?op=monthly');
			}
		}

		$oRES = new reserve_status($this->con, $LocalGovCode, $ShisetsuCode, $ShitsujyoCode, $MenCode);
		if ($CombiNo > 0) $oRES->set_combi_openkbn($CombiNo);

		$prec = $oRES->get_schedule_ptn();
		$notice = $prec['msg1'];
		$_SESSION['msg2'] = $prec['msg2'];
		$_SESSION['ShowDanjyoNinzuFlg'] = $prec['showdanjyoninzuflg'];
		$_SESSION['Y_I']['shinsaflg'] = $prec['shinsaflg'];
		$_SESSION['Y_I']['genapplyflg'] = $prec['genapplyflg'];
		$_SESSION['Y_I']['fractionflg'] = $prec['fractionflg'];

		//--------------------------------------------
		// 一般利用者への公開レベル判定
		//--------------------------------------------
		$Month = substr($UseDate, 4, 2);
		$monthkey = intval($Month) - 1;
		$openkbn_arr = explode(',', $prec['openkbn']);
		$openkbn = intval($openkbn_arr[$monthkey]);
		$openflg = $openkbn == 1 ? 1 : 0;

		$ptn = $oRES->get_time_schedule_ptn($UseDate);
		$oRES->put_koma_status($ptn, $UseDate);

		$recs = array();
		foreach ($ptn as $key => $val)
		{
			$KomaTimeFrom = $val['From'];
			$KomaTimeTo = $val['To'];

			$entry = array();
			$entry['line1'] = '';
			if ($val['KomaClass'] == '3' && $val['KomaName'] != '') {
				$entry['line1'] = $val['KomaName'].'<br>';
			}
			$entry['line1'] .= $this->oSC->timeFormat($KomaTimeFrom).'-'.$this->oSC->timeFormat($KomaTimeTo);
			$entry['KomaKbn'] = $key;
			$entry['UseTimeFrom'] = substr($KomaTimeFrom, 0, 4);
			$entry['UseTimeTo'] = substr($KomaTimeTo, 0, 4);
			$entry['mark'] = $val['mark'];
			$recs[] = $entry;
		}
		unset($ptn);

		$HoliFlg = '';
		if ($openkbn == 1) {
			if ($openkbn_arr[12] == '1') {
				$HoliFlg = $oRES->getHoliFlg(date('Ymd'));
				if ($HoliFlg == '0') {
					$openflg = 0;
					$openkbn = 5;
				}
			}
			if ($openkbn_arr[13] == '1') {
				$Hrec = $oRES->KyukanHantei(date('Ymd'));
				if ($Hrec == 2 || $Hrec == 17 || $Hrec == 103) {
					$HoliFlg = '0';
					$openflg = 0;
					$openkbn = 6;
				}
			}
		}

		$webopen = is_web_enable($prec, 2);
		if ($openkbn == 1 && $webopen == 0) {
			$openflg = 0;
		}

		$tmpMonth = date(OR_MONTH_FORMAT, strtotime($UseDate));
		$YoyakuKbn = 2;
		$message = OR_CHOOSE_TIME_AND_CLICK;
		$showflag = false;
		$htmlDisplay = 0;
		$pulloutTerm = false;
		if ($openflg == 1) {

			$optmsg = 0;
			$status = $oRES->check_term($UseDate);
			$tmp_time = date('Hi', $status[2]);
			$date_format = $tmp_time == '0000' || $tmp_time == '2359' ? OR_MONTHDAY_FORMAT : OR_MONTHDAYTIME_FORMAT;
			$tmpDate = date($date_format, $status[2]);

			switch($status[1]) {
			case 3:
				$message = sprintf(OR_LOTTARY_START, $tmpDate);
				$optmsg = 1;
				break;
			case 11:
				$YoyakuKbn = 1;
				$htmlDisplay = 1;
				$showflag = true;
				$pulloutTerm = true;
				$optmsg = 1;
				break;
			case 4:
				$pulloutTerm = true;
				$message = OR_LOTTARY_CLOSE;
				break;
			case 9:
				$pulloutTerm = true;
				$message = OR_CONFIRM_WIN;
				break;
			case 2:
				$message = sprintf(OR_RESERVATION_START, $tmpDate);
				$optmsg = 2;
				break;
			case 0:
				$htmlDisplay = 1;
				$showflag = true;
				$optmsg = 2;
				break;
			case 1:
				$message = sprintf(OR_RESERVATION_END, $tmpDate);
			}
			if ($optmsg > 0) {
				$restriction = $oRES->checkRestriction($YoyakuKbn);
				if ($restriction != 1) {
					$tmpmsg = $oRES->putRestrictionMsg($optmsg);
					if ($tmpmsg != 0) $message.= '('.$GLOBALS['aMemberShip'][$tmpmsg].')';
				}
			}
		} else {
			$message = OR_OUT_OF_SERVICE_TIME;
			if ($openkbn == 2) {
				$message = OR_ONLY_DISPLAY;
			} elseif ($openkbn == 0) {
				$message = sprintf(OR_NOT_ACCEPTED, $tmpMonth);
			} elseif ($openkbn == 5 && $HoliFlg == '0') {
				$message = OR_NOT_ACCEPTED_FOR_CLOSED_OFFICE;
			} elseif ($openkbn == 6 && $HoliFlg == '0') {
				$message = OR_NOT_ACCEPTED_FOR_CLOSED_DAY;
			}
		}

		$_SESSION['Y_I']['YoyakuKbn'] = $YoyakuKbn;

		$komasu = count($recs);

		$multi = $prec['yoyakudispkoma'];
		if ($pulloutTerm) {
			$multi = $prec['pulloutdispkoma'];
		}
		if ($multi == 0) $multi = 1;

		$modkoma = $komasu%$multi;
		if ($modkoma != 0) $komasu -= $modkoma;

		$entries2 = array();
		if ($multi > 1) {
			for ($k=0; $k < $komasu; ++$k)
			{
				$val = $recs[$k];
				if ($k%$multi == 0) {
					$val['line1'] = $this->oSC->timeFormat($val['UseTimeFrom']).'-'.$this->oSC->timeFormat($recs[$k+$multi-1]['UseTimeTo']);
					for ($i=0; $i < $multi; ++$i)
					{
						if ($recs[$k+$i]['mark'][0] == 6) {
							$val['mark'] = $recs[$k+$i]['mark'];
							break;
						}
					}
					$entries2[] = $val;
				}
			}
		} else {
			$entries2 = $recs;
		}

		$komaCount = count($entries2);
		$htmlTdCount = 4;
		if ($komaCount > 12) {
			$htmlTdCount = 6;
		}
		$tData = array();
		if ($komaCount%$htmlTdCount != 0) {
			$addNum = $htmlTdCount - ($komaCount%$htmlTdCount);
			for ($i=0; $i < $addNum; ++$i)
			{
				$entries2[] = array();
				++$komaCount;
			}
		}

		for ($i=0; $i < $komaCount; ++$i)
		{
			if ($i%$htmlTdCount == 0 && $i != ($komaCount-1)) {
				if ($i > 0) $tData[] = $subArr;
				$subArr = array();
			}
			$subArr[] = $entries2[$i];
			if ($i == ($komaCount-1)) {
				$tData[] = $subArr;
			}
		}

		$offset = isset($recs[0]) ? intval($recs[0]['KomaKbn']) : 1;

		unset($prec);

		$protocol = _USE_SSL_ ? 'https' : 'http';
		$NextUrl = getTopUrl($protocol);

		$usedate = strtotime($UseDate);
		$NaviDate = array();
		$NaviDate[] = date('Ymd', $usedate - 86400*7);
		$NaviDate[] = date('Ymd', $usedate - 86400);
		$NaviDate[] = date('Ymd', $usedate + 86400);
		$NaviDate[] = date('Ymd', $usedate + 86400*7);

		$mode = 0;
		$condition = OR_TIME_CHOICE;
		$BackLink = '?op=monthly&UseYM='.substr($UseDate, 0, 6);
		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('notice', $notice);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $_SESSION['M_I']['ShisetsuClassName']);
		$this->oSmarty->assign('ShisetsuName', $_SESSION['M_I']['ShisetsuName']);
		$this->oSmarty->assign('ShitsujyoName', $_SESSION['M_I']['ShitsujyoName']);
		$this->oSmarty->assign('CombiName', $CombiNo == 0 ? '-' : $_SESSION['M_I']['CombiName']);
		$this->oSmarty->assign('CombiNo', $_SESSION['Y_I']['combino']);
		$this->oSmarty->assign('UseDateDisp', $UseDateDisp);
		$this->oSmarty->assign('UseDate', $UseDate);
		$this->oSmarty->assign('Komasu', $komasu);
		$this->oSmarty->assign('multi', $multi);
		$this->oSmarty->assign('offset', $offset);
		$this->oSmarty->assign('tData', $tData);
		$this->oSmarty->assign('htmlDisplay', $htmlDisplay);
		$this->oSmarty->assign('htmlTdCount', $htmlTdCount);
		$this->oSmarty->assign('showflag', $showflag);
		$this->oSmarty->assign('NaviDate', $NaviDate);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('NextUrl', $NextUrl);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('daily.tpl');
	}

	function check_user_expiration($UseDate)
	{
		if (intval($_SESSION['USERLIMIT']) < intval($UseDate)) {
			$msg = '利用者登録期限が';
			$msg.= $this->oSC->put_wareki_date($_SESSION['USERLIMIT'], true).'までのため、この日は申し込みできません。';
			$msg.= '<br>期限内の日をお申し込みください。';
			return $msg;
		}
		return '';
	}
}
?>
