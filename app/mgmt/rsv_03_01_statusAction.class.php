<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  空き状況表示
 *
 *  rsv_03_01_statusAction.class.php
 *  rsv_03_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';

define('P_I', 'rsv_01_02');
define('P2_I', 'rsv_03_01');

class rsv_03_01_statusAction extends adminAction
{
	private $oSC = null;
	private $time_area_flag = array(1, 1, 1);

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$message = '';
		$aData = array();

		$this->set_header_info();

		$this->time_area_flag = array(1, 1, 1);
		if (isset($_SESSION[P_I]['TimeArea'])) {
			$this->time_area_flag = array(0, 0, 0);
			foreach ($_SESSION[P_I]['TimeArea'] as $val)
			{
				$this->time_area_flag[$val] = 1;
			}
		}

		if (isset($_POST['commitBtn'])) {
			$aData = $_SESSION[P2_I];
			$message = $this->confirmYoyaku($_POST, $aData);
			$_SESSION[P2_I] = $aData;
			if ($message == '') {
				header('Location:index.php?op=rsv_04_02_input&firstAccess=1');
			}
		} elseif (isset($_REQUEST['replayFlg'])) {
			$aData = $_SESSION[P2_I];
			$aData['UseDate'] = $_REQUEST['date'];
			$this->set_date($aData);
			$this->set_reserve_status($aData);
			$_SESSION[P2_I] = $aData;
		} elseif (isset($_REQUEST['back'])) {
			$aData = $_SESSION[P2_I];
		} elseif (isset($_REQUEST['repeat'])) {
			$aData = $_SESSION[P2_I];
			$this->set_reserve_status($aData);
			$_SESSION[P2_I] = $aData;
		} else {
			$this->setInfo($aData);
			$this->set_date($aData);
			$this->set_reserve_status($aData);
			$_SESSION[P2_I] = $aData;
		}

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('reserve_band_active', $this->time_area_flag);
		$this->oSmarty->assign('aMain', $aData);
		$this->oSmarty->display('rsv_03_01.tpl');
	}

	function confirmYoyaku(&$req, &$aTmp)
	{
		$start = 0;
		$tempNo = 0;
		$count = 0;
		$msg = '';
		foreach ($req['clickKoma'] as $key => $val) {
			$aTmp['aTimeKoma'][$key]['set'] = $val;
			if ($val) {
				if ($start && $tempNo != $count) {
					$msg = '連続しない時間の申し込みはできません。';
					break;
				}
				++$start;
				$tempNo = $count+1;
			}
			++$count;
		}
		if ($start == 0) $msg = '利用する時間帯を選択してください。';
		return $msg;
	}

	function setInfo(&$aTmp)
	{
		$aTmp['UseDate'] = $_REQUEST['date'];
		$aTmp['LocalGovCode'] = _CITY_CODE_;
		$aTmp['ShisetsuCode'] = $_REQUEST['scd'];
		$aTmp['ShitsujyoCode'] = $_REQUEST['rcd'];
		$aTmp['MenCode'] = explode('-', $_REQUEST['mcd']);
		$aTmp['CombiNo'] = $_REQUEST['cno'];
		$aTemp = $this->oSC->get_shitsujyo_info($aTmp['ShisetsuCode'],$aTmp['ShitsujyoCode']);
		$aTmp['ShisetsuName'] = $aTemp['shisetsuname'];
		$aTmp['ShitsujyoName'] = $aTemp['shitsujyoname'];
		$aTmp['MenName'] = $this->oSC->get_combi_name($aTmp['ShisetsuCode'],$aTmp['ShitsujyoCode'], $aTmp['CombiNo']);
		$aTmp['ShowDanjyoNinzuFlg'] = $aTemp['showdanjyoninzuflg'];
		$aTmp['ShinsaFlg'] = $aTemp['shinsaflg'];
		$aTmp['GenApplyFlg'] = $aTemp['genapplyflg'];
		$aTmp['FractionFlg'] = $aTemp['fractionflg'];
		$aTmp['Teiin'] = $aTemp['teiin'];
		if ($aTmp['CombiNo'] != 0) {
			$menSql = " AND (mencode='".implode("' OR mencode='", $aTmp['MenCode'])."')";
			$sql = "SELECT SUM(teiin) FROM m_men
				WHERE localgovcode=? AND shisetsucode=?
				AND shitsujyocode=? ".$menSql;
			$aWhere = array(_CITY_CODE_, $aTmp['ShisetsuCode'], $aTmp['ShitsujyoCode']);
			$aTmp['Teiin'] = $this->con->getOne($sql, $aWhere);
		}
	}

	function set_date(&$aTmp)
	{
		$aTmp['DateView'] = $this->oSC->put_wareki_date($aTmp['UseDate'],true);
		$aTmp['Yesterday'] = date('Ymd', strtotime($aTmp['UseDate'].' -1 day'));
		$aTmp['Tomorrow'] = date('Ymd', strtotime($aTmp['UseDate'].' +1 day'));
		$aTmp['PreWeek'] = date('Ymd', strtotime($aTmp['UseDate'].' -7 day'));
		$aTmp['NextWeek'] = date('Ymd', strtotime($aTmp['UseDate'].' +7 day'));
	}

	function set_reserve_status(&$aTmp)
	{
		$aSys = $this->oSC->get_system_parameters();

		$oRES = new reserve_status($this->con, _CITY_CODE_, $aTmp['ShisetsuCode'], $aTmp['ShitsujyoCode'], $aTmp['MenCode'], false);

		$UseDate = $aTmp['UseDate'];
		$aTimeKoma = $oRES->get_time_schedule_ptn($UseDate, _PRIVILEGE_TIME_);
		$oRES->get_reserved_user($aTimeKoma, $UseDate);
		$aAPN = array('am' => 0, 'pm' => 0, 'nt' => 0, 'all' => 0);
		foreach ($aTimeKoma as $key => $koma)
		{
			$komaFrom = substr($koma['From'], 0, 4);
			$aTimeKoma[$key]['apnFlg'] = 0;
			if ($aSys['amfrom'] <= $komaFrom && $komaFrom <= $aSys['amto']) {
				if ($this->time_area_flag[0]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['am'];
			}
			if ($aSys['pmfrom'] <= $komaFrom && $komaFrom <= $aSys['pmto']) {
				if ($this->time_area_flag[1]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['pm'];
			}
			if ($aSys['ntfrom'] <= $komaFrom && $komaFrom <= $aSys['ntto']) {
				if ($this->time_area_flag[2]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['nt'];
			}
			++$aAPN['all'];
			$aTimeKoma[$key]['FromView'] = substr($koma['From'], 0, 2).':'.substr($koma['From'], 2, 2);
			$aTimeKoma[$key]['ToView'] = substr($koma['To'], 0, 2).':'.substr($koma['To'], 2, 2);
			$aTimeKoma[$key]['set'] = 0;
		}
		$aTmp['AMFromView'] = $aSys['AMFromView'];
		$aTmp['AMToView'] = $aSys['AMToView'];
		$aTmp['PMFromView'] = $aSys['PMFromView'];
		$aTmp['PMToView'] = $aSys['PMToView'];
		$aTmp['NTFromView'] = $aSys['NTFromView'];
		$aTmp['NTToView'] = $aSys['NTToView'];
		$aTmp['aAPN'] = $aAPN;
		$aTmp['komaCount'] = $aAPN['all'];
		$aTmp['aTimeKoma'] = $aTimeKoma;
	}
}
?>
