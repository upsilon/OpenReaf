<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  client.php
 */

$aWeek = array();
$aWeekE = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

$aMemberShip = array('', '個人の方のみ', '団体の方のみ', _INSIDE_.'の方のみ', _INSIDE_.'個人の方のみ', _INSIDE_.'団体の方のみ');

function is_web_enable($recs, $mode=1)
{
	$Now = date("Hi");
	$result = 0;

	if ($recs['webuketimekbn'] == $mode) {
		if ($recs['webuketimefrom'] <= $Now && $Now <= $recs['webuketimeto']) {
			$result = 1;
		}
	} else {
		$result = 1;
	}
	return $result;
}

//
// 施設利用制限の実施判定
//
function checkShisetsuRestriction(&$con, $lcd)
{
	$sql = "SELECT shisetsurestrictionflg";
	$sql.= " FROM m_systemparameter WHERE localgovcode=?";
	$flg = $con->getOne($sql, array($lcd));

	if ($flg == 0) return false;
	return true;
}
?>
