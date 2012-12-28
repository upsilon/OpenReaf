<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  fuzokuAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/monthly.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';
require OPENREAF_ROOT_PATH.'/app/include/restriction.php';

class fuzokuAction extends Action
{
	private $oSC = null;

	function __construct($type)
	{
		parent::__construct($type);

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		if (!isset($_SESSION['Y_I'])) $this->display_error_msg(OR_RETURN_TO_TOP);

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$MenCode = $_SESSION['Y_I']['mencode'];
		$CombiNo = $_SESSION['Y_I']['combino'];
		$UseDate = $_SESSION['Y_I']['usedate'];
		$YoyakuKbn = $_SESSION['Y_I']['YoyakuKbn'];

		if (isset($_SESSION['Y_I']['Fuzoku'])) unset($_SESSION['Y_I']['Fuzoku']);
		if (isset($_SESSION['M_I']['Fuzoku'])) unset($_SESSION['M_I']['Fuzoku']);

		if (isset($_POST['chkClicktime'])) {

			$_SESSION['Y_I']['UTFrom'] = array();
			$_SESSION['Y_I']['UTTo'] = array();

			$multi = isset($_POST['multi']) ? intval($_POST['multi']) : 1;
			$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 1;

			foreach ($_POST['chkClicktime'] as $key => $val)
			{
				if ($val == '0') continue;
				for ($i=0; $i < $multi; ++$i)
				{
					$n = intval($_POST['KomaKbn'][$key]) + $i;
					$kk = sprintf('%02d', $n);
					list($utfrom, $utto) = explode('_', $_POST['UseTime'][$n-$offset]);
					$_SESSION['Y_I']['UTFrom'][$kk] = $utfrom;
					$_SESSION['Y_I']['UTTo'][$kk] = $utto;
				}
			}
		}
		if (empty($_SESSION['Y_I']['UTFrom'])) {
			$stMsg = '時間を選択してください';
			$this->display_error_msg($stMsg, '?op=daily');
		}

		$_SESSION['Y_I']['komasu'] = count($_SESSION['Y_I']['UTFrom']);
		$timecode = array_keys($_SESSION['Y_I']['UTFrom']);
		$first_index = $timecode[0];
		$last_index =  $timecode[$_SESSION['Y_I']['komasu'] - 1];

		$_SESSION['Y_I']['usetimefrom'] = $_SESSION['Y_I']['UTFrom'][$first_index].'00';
		$_SESSION['Y_I']['usetimeto'] = $_SESSION['Y_I']['UTTo'][$last_index].'00';
		$UseTime = $this->oSC->timeFormat($_SESSION['Y_I']['usetimefrom']).'-'.$this->oSC->timeFormat($_SESSION['Y_I']['usetimeto']);
		$_SESSION['M_I']['UseTime'] = $UseTime;

		//--------------------------------------------
		// コマ連続のチェック
		//--------------------------------------------
		if (!$this->check_usetime($_SESSION['Y_I']['UTFrom']))
		{
			$stMsg = '連続するコマを選んでください';
			$this->display_error_msg($stMsg, '?op=daily');
		}

		$this->check_login('fuzoku');

		$_SESSION['Y_I']['userid'] = $_SESSION['UID'];
		$_SESSION['Y_I']['FeeKbn'] = $_SESSION['USEKBN'];
 
		$oRES = new reserve_status($this->con, $LocalGovCode, $ShisetsuCode, $ShitsujyoCode, array('ZZ'));

		$stMsg = $this->check_user_rights($UseDate);
		if ($stMsg != '') {
			$this->display_error_msg($stMsg, '?op=daily');
		}
		if (empty($_SESSION['ShisetsuRestrictionFlg'])) {
			$stMsg = $this->check_facility_rights($ShisetsuCode);
			if ($stMsg != '') {
				$this->display_error_msg($stMsg, '?op=daily');
			}
		}
		//
		// 市内/市外、団体/個人の制限チェック
		//
		if (!$oRES->checkRestriction($YoyakuKbn)) {
			$poyfmt = $YoyakuKbn == 1 ? OR_ENABLE_LOTTARY_APPLICATION : OR_ENABLE_FACILITY_RESERVATION;
			$tmp = $oRES->putRestrictionMsg($YoyakuKbn);
			$stMsg = sprintf($poyfmt, $GLOBALS['aMemberShip'][$tmp]);
			$this->display_error_msg($stMsg, '?op=daily');
		}
		//
		// 申し込み期間チェック
		//
		if ($YoyakuKbn == 2) {
			$start_day = $oRES->get_start_date($UseDate);
			$end_day = $oRES->get_end_date($UseDate);
			$current = time();
			if ($current < $start_day || $end_day <= $current) {
				$StartDate = date('n月j日', $start_day);
				$EndDate = date('n月j日', $end_day);
				$stMsg = '申し込み期間は、'.$StartDate.'から'.$EndDate.'です。';
				$stMsg.= '<br>期間内の日をお申し込みください。';
				$this->display_error_msg($stMsg, '?op=daily');
			}
		}
		$stMsg = $this->check_limitation($_SESSION['Y_I']);
		if ($stMsg != '') {
			$this->display_error_msg($stMsg, '?op=daily');
		}
		$stMsg = $this->check_minimum_time($_SESSION['Y_I']);
		if ($stMsg != '') {
			$this->display_error_msg($stMsg, '?op=daily');
		}
		if ($YoyakuKbn == 1) {
			$stMsg = $this->check_duplicate_lot($_SESSION['Y_I']);
			if ($stMsg != '') {
				$this->display_error_msg($stMsg, '?op=daily');
			}
			$prec = $oRES->get_schedule_ptn();
			$stMsg = $this->check_komasu($prec, $_SESSION['Y_I']['komasu']);
			if ($stMsg != '') {
				$this->display_error_msg($stMsg, '?op=daily');
			}
		}

		$sql = "SELECT shitsujyokbn FROM m_shitsujyou";
		$sql.= " WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?";
		$ShitsujyoKbn = $this->con->getOne($sql, array($LocalGovCode, $ShisetsuCode, $ShitsujyoCode));

		$recs = array();
		if ($ShitsujyoKbn == '2') {
			$sql = "SELECT DISTINCT s.shitsujyocode, s.shitsujyoname, s.shitsujyoskbcode";
			$sql.= " FROM m_fuzokushitsujyou f";
			$sql.= " JOIN m_shitsujyou s";
			$sql.= " ON f.localgovcode=s.localgovcode AND f.shisetsucode=s.shisetsucode AND f.fuzokucode=s.shitsujyocode";
			$sql.= " WHERE f.localgovcode=? AND f.shisetsucode=? AND f.shitsujyocode=?";
			$sql.= " AND (f.combino=0 OR f.combino=?)";
			$sql.= " AND s.shitsujyokbn='3' AND s.openflg='1' AND s.appdatefrom<=?";
			$sql.= " AND (s.haishidate>? OR s.haishidate='' OR s.haishidate IS NULL)";
			$sql.= " ORDER BY shitsujyoskbcode, shitsujyocode";
			$aWhere = array($LocalGovCode, $ShisetsuCode, $ShitsujyoCode, $CombiNo, $UseDate, $UseDate);
			$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
			foreach ($res as $val)
			{
				$mark = array(1, 0, '-', '-', '#ffffff');
				$oTS = new time_schedule($this->con, $LocalGovCode, $ShisetsuCode, $val['shitsujyocode'], false);
				$aTimeKoma = $oTS->get_time_schedule_ptn($UseDate);
				if (!empty($aTimeKoma))  {
					$komakbn = array_keys($aTimeKoma);
					$start_time = $aTimeKoma[$komakbn[0]]['From'];
					$end_time = $aTimeKoma[$komakbn[count($komakbn)-1]]['To'];

					if ($start_time < $_SESSION['Y_I']['usetimeto'] && $_SESSION['Y_I']['usetimefrom'] < $end_time) {

						$ptn = array();
						foreach ($_SESSION['Y_I']['UTFrom'] as $key => $value)
						{
							$ptn[$key] = array(
									'From' => $KomaTimeFrom = $value.'00',
									'To' => $KomaTimeTo = $_SESSION['Y_I']['UTTo'][$key].'00');
						}
						$oRES->put_koma_status($ptn, $UseDate, $val['shitsujyocode']);
						foreach ($ptn as $koma)
						{
							$mark = $koma['mark'];
							if ($mark[0] != 10 && $mark[0] != 11) break;
						}
					}
				}
				$recs[] = array(
						'check' => $mark[0],
						'line1' => $val['shitsujyoname'],
						'FuzokuCode' => $val['shitsujyocode'],
						'mark' => $mark[3]
						);
			}
			unset($res);
		}

		if (count($recs) == 0) {
			$class_name = 'mokutekiAction';
			require OPENREAF_ROOT_PATH.'/app/'.$this->type.'/'.$class_name.'.class.php';
			$oAction = new $class_name($this->type);
			$oAction->execute();
			exit();
		}

		$message = OR_CHOOSE_OPTIONS_AND_CLICK;
		$condition = OR_OPTIONS_CHOICE.' :: 【'.$_SESSION['UNAME'].'】';
		$BackLink = '?op=daily';

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('UseDateDisp', $this->oSC->date4lang($UseDate, _LANGUAGE_));
		$this->oSmarty->assign('tData', $recs);
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('fuzoku.tpl');
	}

	//
	// 連続時間のチェック
	//
	function check_usetime($UseTimeArr)
	{
		$timecode = array_keys($UseTimeArr);
		sort($timecode);

		$cnt = count($UseTimeArr);
		if ($cnt == 0) return false;

		$pre = $timecode[0];
		for ($i = 1; $i < $cnt; ++$i) {
			if ($pre == $timecode[$i] - 1) {
				++$pre;
			} else {
				return false;
			}
		}
		return true;
	}

	//
	// 予約権限、利用者登録期限と利用日のチェック
	//
	function check_user_rights($UseDate)
	{
		$sql = "SELECT yoyakukyokaflg, userjyoutaikbn, stoperasedate";
		$sql.= " FROM m_user WHERE localgovcode=? AND userid=?";
		$aWhere = array(_CITY_CODE_, $_SESSION['UID']);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if (intval($_SESSION['USERLIMIT']) < intval($UseDate)) {
			$msg = '利用者登録期限が';
			$msg.= $this->oSC->put_wareki_date($_SESSION['USERLIMIT'], true).'までのため、この日は申し込みできません。';
			$msg.= '<br>期限内の日をお申し込みください。';
			return $msg;
		}
		if (empty($row['yoyakukyokaflg'])) {
			return '予約権限が与えられていません。';
		}
		if ($row['userjyoutaikbn'] != '1') {
			if (intval($row['stoperasedate']) <= intval(date('Ymd'))) {
				return '予約権限が与えられていません。';
			} elseif (intval($row['stoperasedate']) <= intval($UseDate)) {
				return '予約権限が与えられていません。';
			}
		}
		return '';
	}

	//
	// 施設利用権限のチェック
	//
	function check_facility_rights($ShisetsuCode)
	{
		$RestrictionFlg = checkShisetsuRestriction($this->con, _CITY_CODE_);
		if ($RestrictionFlg) {
			$sql = 'SELECT shisetsu FROM m_user WHERE localgovcode=? AND userid=?';
			$value = $this->con->getOne($sql, array(_CITY_CODE_, $_SESSION['UID']));
			if ($value != '') {
				if (!preg_match('/'.$ShisetsuCode.'/', $value)) {
					return 'この施設への予約権限がありません。';
				}
			}
		}
		return '';
	}

	//
	// 申し込み回数制限チェック
	//
	function check_limitation(&$ses)
	{
		$checkres = restrict_request($this->con, $ses, $ses['YoyakuKbn']);
		if ($checkres != 0) {
   			return put_restriction_error_msg($checkres);
		}
		return '';
	}

	//
	// 抽選申し込みコマ数チェック
	//
	function check_komasu(&$res, $Komasu)
	{
		$komalimit = 0;

		if ($res['pulloutkomalimitflg'] != '0'
			&& $res['pulloutfixlimitkbn'] == '2') {

			if ($res['pulloutkomalimitflg'] == '1') {
				$komalimit = 1;
			} else {
				if ($_SESSION['KOJINDANKBN'] == '1') {
					$komalimit = $res['pulloutfixlimitkojin'];
				} else {
					$komalimit = $res['pulloutfixlimitdantai'];
				}
			}
			if ($komalimit < $Komasu) {
				$msg = 'この施設の1回の抽選申し込みで指定可能なコマ数は'.$komalimit.'までです。<br>';
				$msg.= $komalimit.'コマ以内で再度お申し込みください。';
				if ($komalimit == 0) {
					$msg = 'この施設は現在申し込みできません。';
				} elseif ($komalimit == 1) {
					$msg = 'この施設の1回の抽選申し込みで指定可能なコマ数は1つのみです。<br>';
					$msg.= '1コマで再度お申し込みください。';
				}
				return $msg;
			}
		}
		return '';
	}

	//
	// 最低利用時間のチェック
	//
	function check_minimum_time(&$ses)
	{
		$UseMD = substr($ses['usedate'], 4, 4);
		$wday = strtolower(date('D', strtotime($ses['usedate']))).'flg';
		$sql = "SELECT holiflg FROM m_holiday WHERE localgovcode=? AND heichouholiday=? ";
		$HoliFlg = $this->con->getOne($sql, array($ses['localgovcode'], $ses['usedate']));
		if ($HoliFlg == '1') $wday = "holiflg";

		$sql = "SELECT * FROM m_stjfee
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?
			AND timefrom='min' AND appdatefrom<=?
			AND (haishidate>? OR haishidate='' OR haishidate IS NULL)
			AND monthdayfrom<=? AND monthdayto>=? AND $wday='1'";
		$aWhere = array($ses['localgovcode'], $ses['shisetsucode'],
				$ses['shitsujyocode'], $ses['combino'],
				$ses['usedate'], $ses['usedate'],
				$UseMD, $UseMD);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		foreach ($res as $rec)
		{
			if ($rec['minimumusetimeflg'] == '1') {
				$checkTime = intval($rec['minimumusetime']);
				$timeSum = 0;
				foreach ($ses['UTFrom'] as $key=>$value)
				{
					$time = intval(substr($ses['UTTo'][$key],0,2))*60
						+ intval(substr($ses['UTTo'][$key],2,2))
						- intval(substr($value,0,2))*60
						- intval(substr($value,2,2));
					$timeSum += $time;
				}
				if ($timeSum < $checkTime) {
					return '最低利用時間は'.$checkTime.'分です。';
				}
			}
			if ($rec['komaunitflg'] == '1' && $rec['komaunit'] != 0) {
				if (($ses['komasu'] % $rec['komaunit']) != 0) {
					return $rec['komaunit'].'コマずつ申し込んでください。';
				}
			}
		}
		return '';
	}

	//
	// 繰り返し抽選申込のチェック
	//
	function check_duplicate_lot(&$ses)
	{
		$sql = "SELECT COUNT(pulloutyoyakunum) FROM t_pulloutyoyaku
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?
			AND usedate=? AND userid=? AND usetimefrom=? AND usetimeto=?
			AND pulloutjoukyoukbn<>'4'";
		$aWhere = array($ses['localgovcode'], $ses['shisetsucode'],
				$ses['shitsujyocode'], $ses['combino'],
				$ses['usedate'], $_SESSION['UID'],
				$ses['usetimefrom'], $ses['usetimeto']
				);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res > 0) {
			return '同一利用単位・日時の繰り返し抽選申込はできません。';
		}
		return '';
	}
}
?>
