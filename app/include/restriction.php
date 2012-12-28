<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  restriction.php
 */

define ('_RESTRICT_FROM_SUNDAY_', false);

/*
 * $poyFlg  1:抽選 2:予約
 */
function restrict_request(&$con, &$dataset, $poyFlg, &$appdata=null)
{
	$appflg = empty($appdata) ? false : true;

	$KojinDanKbn = $_SESSION['KOJINDANKBN'];//団体/個人 1:個人 2:団体
	if ($KojinDanKbn != '1') $KojinDanKbn = '2';

	$prefix = $poyFlg == 1 ? 'pullout' : '';

	$plKbn = 0; //抽選月間制限 0:全体 1:平日 2:土日祝日

	$sql = "SELECT * FROM m_shitsujyou 
		WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?"; 
	$aWhere = array(
			$dataset['localgovcode'],
			$dataset['shisetsucode'],
			$dataset['shitsujyocode']
		);
	$res = $con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

	//申込(回数/コマ)数 1:回数 2:コマ数
	//室場・日間
	$limitflg = $res[$prefix.'daylimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max_shitsujyo($res, $KojinDanKbn, $poyFlg, 3);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 1, 3, 0);
		if ($curCount > $maxLimit) return 1;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 1, 3, 0);
			if ($curCount > $maxLimit) return 1;
		}
	}
	//室場・週間
	$limitflg = $res[$prefix.'weklimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max_shitsujyo($res, $KojinDanKbn, $poyFlg, 2);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 1, 2, 0);
		if ($curCount > $maxLimit) return 2;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 1, 2, 0);
			if ($curCount > $maxLimit) return 2;
		}
	}
	//室場・月間
	$limitflg = $res[$prefix.'limitflg'];
	if ($limitflg != '0') {
		if ($res['pulloutmonlimitkbn'] != '0' && $poyFlg == 1) {
			$plKbn = is_holiday($con, $dataset['localgovcode'], $dataset['usedate']) ? 2 : 1;
		}
		$maxLimit = get_max_shitsujyo($res, $KojinDanKbn, $poyFlg, 1, $plKbn);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 1, 1, $plKbn);
		if ($curCount > $maxLimit) return 3 + $plKbn;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 1, 1, $plKbn);
			if ($curCount > $maxLimit) return 3 + $plKbn;
		}
	}

	$sql = "SELECT * FROM m_shisetsu 
		WHERE localgovcode=? AND shisetsucode=?";
	$aWhere = array(
			$dataset['localgovcode'],
			$dataset['shisetsucode']
		);
	$res = $con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

	//施設・日間
	$limitflg = $res[$prefix.'daylimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 3);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 2, 3, 0);
		if ($curCount > $maxLimit) return 6;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 2, 3, 0);
			if ($curCount > $maxLimit) return 6;
		}
	}
	//施設・週間
	$limitflg = $res[$prefix.'weklimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 2);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 2, 2, 0);
		if ($curCount > $maxLimit) return 7;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 2, 2, 0);
			if ($curCount > $maxLimit) return 7;
		}
	}
	//施設・月間
	$limitflg = $res[$prefix.'limitflg'];
	if ($limitflg != '0') {
		if ($res['pulloutmonlimitkbn'] != '0' && $poyFlg == 1) {
			$plKbn = is_holiday($con, $dataset['localgovcode'], $dataset['usedate']) ? 2 : 1;
		}
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 1, $plKbn);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 2, 1, $plKbn);
		if ($curCount > $maxLimit) return 8 + $plKbn;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 2, 1, $plKbn);
			if ($curCount > $maxLimit) return 8 + $plKbn;
		}
	}

	$sql = "SELECT * FROM m_shisetsuclass 
		WHERE localgovcode=? AND shisetsuclasscode=?";
	$aWhere = array(
			$dataset['localgovcode'],
			$dataset['shisetsuclasscode']
		);
	$res = $con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

	//施設分類・日間
	$limitflg = $res[$prefix.'daylimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 3);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 3, 3, 0);
		if ($curCount > $maxLimit) return 11;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 3, 3, 0);
			if ($curCount > $maxLimit) return 11;
		}
	}
	//施設分類・週間
	$limitflg = $res[$prefix.'weklimitflg'];
	if ($limitflg != '0') {
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 2);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 3, 2, 0);
		if ($curCount > $maxLimit) return 12;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 3, 2, 0);
			if ($curCount > $maxLimit) return 12;
		}
	}
	//施設分類・月間
	$limitflg = $res[$prefix.'limitflg'];
	if ($limitflg != '0') {
		if ($res['pulloutmonlimitkbn'] != '0' && $poyFlg == 1) {
			$plKbn = is_holiday($con, $dataset['localgovcode'], $dataset['usedate']) ? 2 : 1;
		}
		$maxLimit = get_max($res, $KojinDanKbn, $poyFlg, 1, $plKbn);
		$curCount = count_current_data($con, $dataset, $limitflg, $poyFlg, 3, 1, $plKbn);
		if ($curCount > $maxLimit) return 13 + $plKbn;

		if ($appflg) {
			$curCount += count_application_data($con, $dataset, $appdata, $limitflg, $poyFlg, 3, 1, $plKbn);
			if ($curCount > $maxLimit) return 13 + $plKbn;
		}
	}
	return 0;
}

function get_max(&$target,$KojinDanKbn,$poyFlg,$modFlg,$plKbn=0)
{
	$kd_arr = array('1'=>'personal', '2'=>'group');
	$poy_arr = array(1=>'pullout', 2=>'ippan');
	$mod_arr = array(1=>'mon', 2=>'wek', 3=>'day');
	$pl_arr = array(0=>'', 1=>'1', 2=>'2');

	$key = isset($kd_arr[$KojinDanKbn]) ? $kd_arr[$KojinDanKbn] : '';
	$key.= isset($poy_arr[$poyFlg]) ? $poy_arr[$poyFlg] : '';
	$key.= $mod_arr[$modFlg].$pl_arr[$plKbn].'limit';

	return isset($target[$key]) ? $target[$key] : 1000;
}

function get_max_shitsujyo(&$target,$KojinDanKbn,$poyFlg,$modFlg,$plKbn=0)
{
	$kd_arr = array('1'=>'kojin', '2'=>'dantai');
	$poy_arr = array(1=>'pullout', 2=>'yoyaku');
	$mod_arr = array(1=>'mon', 2=>'wek', 3=>'day');
	$pl_arr = array(0=>'', 1=>'1', 2=>'2');

	$key = isset($poy_arr[$poyFlg]) ? $poy_arr[$poyFlg] : '';
	$key.= $mod_arr[$modFlg].$pl_arr[$plKbn].'limit';
	$key.= isset($kd_arr[$KojinDanKbn]) ? $kd_arr[$KojinDanKbn] : '';

	return isset($target[$key]) ? $target[$key] : 1000;
}

function count_current_data(&$con, &$dataset, $LimitKbn, $poyFlg, $fKind, $modFlg, $plKbn)
{
	$sql = "SELECT ";
	$scSql = "SELECT ";
	$where = " WHERE a.userid='".$dataset['userid']."' AND a.localgovcode='".$dataset['localgovcode']."' ";
	if ($poyFlg == 1) { //抽選
		$sql .= " DISTINCT a.pulloutyoyakunum, a.komasu, a.usedate FROM t_pulloutyoyaku a";
		$scSql .= " DISTINCT a.pulloutyoyakunum, a.komasu, a.usedate FROM t_pulloutyoyaku a LEFT JOIN m_shisetsu b 
				ON a.localgovcode=b.localgovcode 
				AND a.shisetsucode=b.shisetsucode";
	} else { //予約
		$sql .= " DISTINCT a.yoyakunum, a.komasu, a.usedatefrom as usedate FROM t_yoyaku a LEFT JOIN t_pulloutyoyaku c
				ON a.localgovcode=c.localgovcode
				AND a.yoyakunum=c.pulloutyoyakunum";
		$scSql .= " DISTINCT a.yoyakunum, a.komasu, a.usedatefrom as usedate FROM t_yoyaku a LEFT JOIN m_shisetsu b 
				ON a.localgovcode=b.localgovcode 
				AND a.shisetsucode=b.shisetsucode
				LEFT JOIN t_pulloutyoyaku c
				ON a.localgovcode=c.localgovcode
				AND a.yoyakunum=c.pulloutyoyakunum";
		$where .= " AND c.pulloutyoyakunum IS NULL ";
	}
	if ($fKind == 1) { //室場
		$where .= " AND a.shisetsucode='".$dataset['shisetsucode']."' AND a.shitsujyocode='".$dataset['shitsujyocode']."'";
	} elseif ($fKind == 2) { //施設
		$where .= " AND a.shisetsucode='".$dataset['shisetsucode']."'";
	} else {
		$where .= " AND b.shisetsuclasscode='".$dataset['shisetsuclasscode']."' ";
	}
	if ($modFlg == 1) { //Mon
		if ($poyFlg == 1) { //抽選
			// 対象月の場合
			$where .= " AND SUBSTRING(a.usedate,1,6)='".substr($dataset['usedate'],0,6)."' ";
			// 申込月の場合
			//$where .= " AND SUBSTRING(a.pulloutukedate,1,6)='".date('Ym')."' ";
		} else { //予約
			// 対象月の場合
			$where .= " AND SUBSTRING(a.usedatefrom,1,6)='".substr($dataset['usedate'],0,6)."' ";
			// 申込月の場合
			//$where .= " AND SUBSTRING(a.appdate,1,6)='".date('Ym')."' ";
		}
	} elseif ($modFlg == 2) { //Week
		// 対象週の場合
		$usedate = strtotime($dataset['usedate']);
		$wnum = date('w', $usedate);
		// 申込週の場合
		//$usedate = time();
		//$wnum = date('w');
		$wnum_top = ($wnum+6)%7; // 月～日
		if (_RESTRICT_FROM_SUNDAY_) $wnum_top = $wnum; // 日～土
		$WeekTop = date('Ymd', $usedate - $wnum_top * 86400);
		$WeekEnd = date('Ymd', $usedate + (6 - $wnum_top) * 86400);

		if ($poyFlg == 1) { //抽選
			// 対象週の場合
			$where .= " AND SUBSTRING(a.usedate,1,6)='".substr($dataset['usedate'],0,6)."' ";
			$where .= " AND a.usedate BETWEEN '".$WeekTop."' AND '".$WeekEnd."' ";
			// 申込週の場合
			//$where .= " AND SUBSTRING(a.pulloutukedate,1,6)='".date('Ym')."' ";
			//$where .= " AND a.pulloutukedate BETWEEN '".$WeekTop."' AND '".$WeekEnd."' ";
		} else { //予約
			// 対象週の場合
			$where .= " AND a.usedatefrom BETWEEN '".$WeekTop."' AND '".$WeekEnd."' ";
			// 申込週の場合
			//$where .= " AND a.appdate BETWEEN '".$WeekTop."' AND '".$WeekEnd."' ";
		}
	} else { //Day
		if ($poyFlg == 1) { //抽選
			// 対象日の場合
			$where .= " AND a.usedate='".$dataset['usedate']."' ";
			// 申込日の場合
			//$where .= " AND a.pulloutukedate='".date('Ymd')."' ";
		} else { //予約
			// 対象日の場合
			$where .= " AND a.usedatefrom='".$dataset['usedate']."' ";
			// 申込日の場合
			//$where .= " AND a.appdate='".date('Ymd')."' ";
		}
	}
	if ($fKind != 3) {
		$sql.= $where;
	} else {
		$sql = $scSql.$where;
	}

	$res = $con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	$count = array(0, 0, 0);
	$komasu = array(0, 0, 0);
	foreach ($res as $row) {
		if (is_holiday($con, $dataset['localgovcode'], $row['usedate'])) {
			++$count[2];
			$komasu[2] += $row['komasu'];
		} else {
			++$count[1];
			$komasu[1] += $row['komasu'];
		}
		++$count[0];
		$komasu[0] += $row['komasu'];
	}
	++$count[$plKbn];
	$komasu[$plKbn] += $dataset['komasu'];

	if ($LimitKbn == '1') { //申込回数 
		return $count[$plKbn];
	} else { //コマ数
		return $komasu[$plKbn];
	}
}

function count_application_data(&$con, &$dataset, &$rsvdata, $LimitKbn, $poyFlg, $fKind, $modFlg, $plKbn)
{
	// 対象日の場合
	$dateStart = $dateEnd = $dataset['usedate'];
	// 申込日の場合
	//$dateStart = $dateEnd = date('Ymd');
	if ($modFlg == 1) { //Mon
		// 対象月の場合
		$dateStart = substr($dataset['usedate'], 0, 6).'01';
		$last_day = date('t', strtotime($dateStart));
		$dateEnd = substr($dataset['usedate'], 0, 6).$last_day;
		// 申込月の場合
		//$dateStart = date('Ym').'01';
		//$dateEnd = date('Ym').date('t');
	} elseif ($modFlg == 2) { //Week
		// 対象週の場合
		$usedate = strtotime($dataset['usedate']);
		$wnum = date('w', $usedate);
		// 申込週の場合
		//$usedate = time();
		//$wnum = date('w');
		$wnum_top = ($wnum+6)%7; // 月～日
		if (_RESTRICT_FROM_SUNDAY_) $wnum_top = $wnum; // 日～土
		$dateStart = date('Ymd', $usedate - $wnum_top * 86400);
		$dateEnd = date('Ymd', $usedate + (6 - $wnum_top) * 86400);
	}

	$res = array();
	if ($fKind == 1) { //室場
		foreach ($rsvdata as $val) {
			if ($val['shisetsucode'] == $dataset['shisetsucode']
				&& $val['shitsujyocode'] == $dataset['shitsujyocode']
				&& $poyFlg == $val['YoyakuKbn']
				&& $dateStart <= $val['usedate']
				&& $val['usedate'] <= $dateEnd) $res[] = $val;
		}
	} elseif ($fKind == 2) { //施設
		foreach ($rsvdata as $val) {
			if ($val['shisetsucode'] == $dataset['shisetsucode']
				&& $poyFlg == $val['YoyakuKbn']
				&& $dateStart <= $val['usedate']
				&& $val['usedate'] <= $dateEnd) $res[] = $val;
		}
	} else {
		foreach ($rsvdata as $val) {
			if ($val['shisetsuclasscode'] == $dataset['shisetsuclasscode']
				&& $poyFlg == $val['YoyakuKbn']
				&& $dateStart <= $val['usedate']
				&& $val['usedate'] <= $dateEnd) $res[] = $val;
		}
	}

	$count = array(0, 0, 0);
	$komasu = array(0, 0, 0);
	foreach ($res as $row) {
		if (is_holiday($con, $dataset['localgovcode'], $row['usedate'])) {
			++$count[2];
			$komasu[2] += $row['komasu'];
		} else {
			++$count[1];
			$komasu[1] += $row['komasu'];
		}
		++$count[0];
		$komasu[0] += $row['komasu'];
	}
	--$count[$plKbn];
	$komasu[$plKbn] -= $dataset['komasu'];

	if ($LimitKbn == '1') { //申込回数 
		return $count[$plKbn];
	} else { //コマ数
		return $komasu[$plKbn];
	}
}

function put_restriction_error_msg($err_no)
{
	$msg = array('',
		'1日に申し込める制限を超えました。(室場)',
		'1週間に申し込める制限を超えました。(室場)',
		'1ヶ月に申し込める制限を超えました。(室場)',
		'1ヶ月に申し込める平日の制限を超えました。(室場)',
		'1ヶ月に申し込める土日祝日の制限を超えました。(室場)',
		'1日に申し込める制限を超えました。(施設)',
		'1週間に申し込める制限を超えました。(施設)',
		'1ヶ月に申し込める制限を超えました。(施設)',
		'1ヶ月に申し込める平日の制限を超えました。(施設)',
		'1ヶ月に申し込める土日祝日の制限を超えました。(施設)',
		'1日に申し込める制限を超えました。(施設分類)',
		'1週間に申し込める制限を超えました。(施設分類)',
		'1ヶ月に申し込める制限を超えました。(施設分類)',
		'1ヶ月に申し込める平日の制限を超えました。(施設分類)',
		'1ヶ月に申し込める土日祝日の制限を超えました。(施設分類)'
		);
	return $msg[$err_no];
}

function is_holiday(&$con, $lcd, $UseDate)
{
	$sql = "SELECT holiflg FROM m_holiday
		WHERE localgovcode=? AND heichouholiday=?";
	$res = $con->getOne($sql, array($lcd, $UseDate));
	if ($res == '1') return true;

	$wnum = date('w', strtotime($UseDate));
	if ($wnum == 0 || $wnum == 6) return true;

	return false;
}
?>
