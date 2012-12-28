<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  monthlyAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/monthly.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';

class monthlyAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		if (!isset($_SESSION['SentakuMode'])) $this->display_error_msg(OR_RETURN_TO_TOP);

		$oSC = new system_common($this->con);

		$LocalGovCode = _CITY_CODE_;

		if ($_SESSION['SentakuMode'] == 2 && isset($_GET['ShisetsuCode'])) {
			$_SESSION['Y_I']['shisetsucode'] = $_GET['ShisetsuCode'];
			$_SESSION['Y_I']['shisetsuclasscode'] = $_GET['ShisetsuClassCode'];
			$_SESSION['Y_I']['shitsujyocode'] = $_GET['ShitsujyoCode'];
			$row = $oSC->get_shitsujyo_info($_SESSION['Y_I']['shisetsucode'], $_SESSION['Y_I']['shitsujyocode']);

			$_SESSION['M_I']['ShisetsuName'] = $row['shisetsuname'];
			$_REQUEST['ShitsujyoName'] = $row['shitsujyoname'];
			$_REQUEST['CombiName'] = $oSC->get_combi_name($_SESSION['Y_I']['shisetsucode'], $_SESSION['Y_I']['shitsujyocode'], $_GET['CombiNo']);
			$_SESSION['M_I']['ShisetsuClassName'] = '';
			if ($_SESSION['screenflg'] == 1) {
				$_SESSION['M_I']['ShisetsuClassName'] = $row['shisetsuclassname'];
			}
		}

		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];

		$ShitsujyoCode = '';
		$CombiNo = 0;
		$CombiName = '';
		$MenCode = array();
		if (isset($_REQUEST['CombiNo'])) {
			$ShitsujyoCode = $_REQUEST['ShitsujyoCode'];
			$_SESSION['Y_I']['shitsujyocode'] = $ShitsujyoCode;
			$_SESSION['M_I']['ShitsujyoName'] = $_REQUEST['ShitsujyoName'];
			$CombiNo = intval($_REQUEST['CombiNo']);
			$_SESSION['Y_I']['combino'] = $CombiNo;
			$CombiName = $_REQUEST['CombiName'];
			$_SESSION['M_I']['CombiName'] = $CombiName;

			$MenCode = $oSC->combino2mencode($ShisetsuCode, $ShitsujyoCode, $CombiNo);
			if (empty($MenCode)) $this->display_error_msg(OR_SYSTEM_ERROR);
			$_SESSION['Y_I']['mencode'] = $MenCode;
		} else {
			$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
			$CombiNo = $_SESSION['Y_I']['combino'];
			$CombiName = $_SESSION['M_I']['CombiName'];
			$MenCode = $_SESSION['Y_I']['mencode'];
		}

		$oRES = new reserve_status($this->con, $LocalGovCode, $ShisetsuCode, $ShitsujyoCode, $MenCode);
		if ($CombiNo > 0) $oRES->set_combi_openkbn($CombiNo);

		//表示する年月を取得
		$UseYM = empty($_REQUEST['UseYM']) ? date('Ym') : $_REQUEST['UseYM'];
		$Year = intval(substr($UseYM, 0, 4));
		$Month = intval(substr($UseYM, 4, 2));
		$curMonth = mktime(0, 0, 0, $Month, 1, $Year);

		$thisMonth = strtotime(date('Ym01'));
		$YearMonths = array();
		$step = array(-2, -1, 1, 2);
		foreach ($step as $val) {
			$month = mktime(0, 0, 0, $Month+$val, 1, $Year);
			$YearMonths[] = array('Date' => date('Ym', $month), 'Label' => date(OR_MONTH_FORMAT, $month), 'Disable' => $month < $thisMonth);
		}

		$recs = array();
		$j = date('w', $curMonth);
		for ($i = 0; $i < $j; ++$i) {
			$recs[$i] = array('day' => '', 'wday' => $i);
		}

		$last_day = date('t', $curMonth);
		$startDate = $UseYM.'01';
		$endDate = sprintf('%s%02d', $UseYM, $last_day);
		$aTimeKoma = array();
		$aTimeMask = array();
		$oRES->get_timetable_data($startDate, $endDate, $aTimeKoma, $aTimeMask);
		for ($i = 1; $i <= $last_day; ++$i) {
			$t_day = date('Ymd', mktime(0, 0, 0, $Month, $i, $Year));
			$ptn = $oRES->make_timetable($aTimeKoma, $aTimeMask, $t_day);
			$tmpArr = array('day' => strval($i), 'wday' => $j%7);
			$tmpArr['open'] = $oRES->put_day_status($ptn, $t_day);
			$recs[$j] = $tmpArr;
			++$j;
		}
		unset($aTimeKoma);
		unset($aTimeMask);
		$tmpCount = $j%7;
		if ($tmpCount > 0) {
			for ($i = $tmpCount; $i < 7; ++$i) {
				$recs[$j] = array('day' => '', 'wday' => $j%7);
				++$j;
			}
		}
		$recs = array_chunk($recs, 7);

		$strYM = sprintf('%s年%2d月', $oSC->getNengouView($Year), $Month);
		if (_LANGUAGE_ != 'ja')  {
			$strYM = date(OR_YEARMONTH_FORMAT, $curMonth);
		}
		$message = OR_CHOOSE_A_DAY;
		$poyFlg = 2;
		$status = $oRES->check_term($startDate);
		$tmpMonth = date(OR_MONTH_FORMAT, $curMonth);
		$tmp_time = date('Hi', $status[2]);
		$date_format = $tmp_time == '0000' || $tmp_time == '2359' ? OR_MONTHDAY_FORMAT : OR_MONTHDAYTIME_FORMAT;
		$tmpDate = date($date_format, $status[2]);

		switch($status[1]) {
		case 3:
			$message = sprintf(OR_LOTTARY_START_MONTH, $tmpMonth, $tmpDate);
			$poyFlg = 1;
			break;
		case 11:
			$message.= '&nbsp;'.sprintf(OR_LOTTARY_END, $tmpDate);
			$poyFlg = 1;
			break;
		case 4:
			$message = sprintf(OR_LOTTARY_CLOSE_MONTH, $tmpMonth);
			$poyFlg = 0;
			break;
		case 9:
			$message = sprintf(OR_CONFIRM_WIN_MONTH, $tmpMonth);
			$poyFlg = 0;
			break;
		case 2:
			$message = sprintf(OR_RESERVATION_START_MONTH, $tmpMonth, $tmpDate);
		}

		$restriction = $oRES->checkRestriction($poyFlg);
		if ($restriction != 1 && $poyFlg > 0) {
			$tmpmsg = $oRES->putRestrictionMsg($poyFlg);
			if ($tmpmsg != 0) {
				if ($restriction == 0) {
					$poyfmt = $poyFlg == 1 ? OR_ENABLE_LOTTARY_APPLICATION : OR_ENABLE_FACILITY_RESERVATION;
					$message = sprintf($poyfmt, $GLOBALS['aMemberShip'][$tmpmsg]);
				} else {
					$message.= '('.$GLOBALS['aMemberShip'][$tmpmsg].')';
				}
			}
		}

		$prec = $oRES->get_schedule_ptn();
		$openkbn_arr = explode(',', $prec['openkbn']);
		$openkbn = $openkbn_arr[$Month-1];
		if ($openkbn == '0') {
			$message = sprintf(OR_NOT_ACCEPTED, $tmpMonth);
		}

		$mode = 0;
		$condition = OR_DAY_CHOICE;
		$BackLink = '?op=men';
		if ($_SESSION['SentakuMode'] == 2) {
			$BackLink = '?op=preset';
		} else {
			if ($CombiNo == 0) {
				$BackLink = '?op=shitsujyo';
				if (isset($_SESSION['skip_stj'])) {
					$BackLink = '?op=shisetsu';
				}
			}
		}
		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
		}

		if ($_SESSION['SentakuMode'] == 1) $_SESSION['SentakuMode'] = 0;

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $_SESSION['M_I']['ShisetsuClassName']);
		$this->oSmarty->assign('ShisetsuName', $_SESSION['M_I']['ShisetsuName']);
		$this->oSmarty->assign('ShitsujyoName', $_SESSION['M_I']['ShitsujyoName']);
		$this->oSmarty->assign('CombiName', $CombiNo == 0 ? '-' : $CombiName);
		$this->oSmarty->assign('CombiNo', $CombiNo);
		$this->oSmarty->assign('aWeek', $GLOBALS['aWeek']);
		$this->oSmarty->assign('YearMonths', $YearMonths);
		$this->oSmarty->assign('strYM', $strYM);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('monthly.tpl');
	}
}
?>
