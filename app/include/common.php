<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  common.php
 */

$aNinzu = array('ninzu1'=>array('男性', true), 'ninzu2'=>array('女性', true),
		'ninzu3'=>array('子ども(男)', true), 'ninzu4'=>array('子ども(女)', true),
		'ninzu5'=>array('利用人数5', true), 'ninzu6'=>array('利用人数6', true),
		'ninzu7'=>array('利用人数7', true), 'ninzu8'=>array('利用人数8', true),
		'ninzu9'=>array('利用人数9', true), 'ninzu10'=>array('利用人数10', true),
		'ninzu11'=>array('利用人数11', true), 'ninzu12'=>array('利用人数12', true),
		'ninzu13'=>array('利用人数13', true), 'ninzu14'=>array('利用人数14', true),
		'ninzu15'=>array('利用人数15', true), 'ninzu16'=>array('利用人数16', true));

$dispflg_arr = array('表示しない', '表示する');

$useflg_arr = array('使用しない', '使用する');

$setflg_arr = array('設定しない', '設定する');

$aInputType = array('数字', '数字＋英小文字(l,qを除く)', '英数字(l,q,I,Oを除く)');

$aWeekJ = array('日', '月', '火', '水', '木', '金', '土');

$aKinshu = array('01'=>'現金', '02'=>'口座振替', '03'=>'利用券',
		'04'=>'口座振込', '05'=>'その他', '06'=>'充当');

$aOptionFee = array(1 => 'オプション1', 2 => 'オプション2', 3 => 'オプション3', 4 => '調整額', 5 => '加算料金');

$aGenmenType = array('減免なし', '利用者減免', '申請減免', '室場減免');

$aHonYoyakuKbn = array('01'=>'仮予約', '02'=>'本予約', '03'=>'要審査', '04'=>'仮押え');

$aShinsaFlg = array('なし', 'あり', '仮押え');

$aShinsaKbn = array('審査不要', '承認', '不承', '保留', '審査待ち');

$aEscapeFlg = array('来場', '不来場');

$aPayKbn = array('-', '未収納', '無料', '一部入金', '完納',
			'超過(還付)', '還付済', '充当', '還付なし');
$aPayKbnUser = array('-', '未入金', '無料', '一部入金', '入金済',
			'超過', '還付済', '充当', '還付なし');

$aStatusName = array('-', '当選', '当選', '予約',
			'抽選待ち', '発表待ち', '当選取消', '取消',
			'抽選取消', '不来場', '落選', '利用済み',
			'審査待ち', '承認', '不承', '審査保留');

$aHitFixAppStatus = array('-', '未確定', '確定済', '取消済');

$mobile_user_agent = array('DoCoMo',
				'blackberry',
				'J-PHONE', 'Vodafone', 'MOT', 'SoftBank',
				'KDDI', 'UP.Browser',
				'WILLCOM'
				);

$smart_user_agent = array('iPhone', 'iPod', 'incognito', 'webmate',
				'Android', 'dream', 'CUPCAKE',
				'IEMobile',
				'NetFront',
				'webOS'
				);

$tablet_user_agent = array('iPad');

//
// return value  0:PC 1:Mobile phone 2:Smart Phone 3:Tablet
//
function check_user_agent()
{
	global $mobile_user_agent;
	global $smart_user_agent;
	global $tablet_user_agent;

	if (!isset($_SERVER['HTTP_USER_AGENT'])) return 0;

	$ua = $_SERVER['HTTP_USER_AGENT'];

	$pattern1 = '/'.implode('|', $mobile_user_agent).'/i';
	$pattern2 = '/'.implode('|', $smart_user_agent).'/i';
	$pattern3 = '/'.implode('|', $tablet_user_agent).'/i';

	if (preg_match($pattern1, $ua)) {
		return 1;
	} elseif (preg_match('/Android/i', $ua)) {
		if (preg_match('/Mob|Fennec/i', $ua)) return 2;
		return 3;
	} elseif (preg_match($pattern2, $ua)) {
		return 2;
	} elseif (preg_match($pattern3, $ua)) {
		return 3;
	}
	return 0;
}

function getTopUrl($protocol='http')
{
	$dir_path = preg_replace('/\/[^\/]*$/', '', $_SERVER['SCRIPT_NAME']);

	return $protocol.'://'.$_SERVER['SERVER_NAME'].$dir_path.'/index.php';
}

function get_request_var($key)
{
	if (isset($_REQUEST[$key])) {
		return $_REQUEST[$key];
	} else {
		return '';
	}
}

function sanitize($str)
{
	if (preg_match("/(\|%|_)/", $str)) {
		$str = addslashes($str);
	}
	$str = htmlspecialchars($str, ENT_QUOTES);
	return $str;
}

function sanitize_r($str)
{
	if (is_array($str)) {
		foreach ($str as $key => $value) {
			$str[$key] = sanitize_r($value);
		}
	} else {
		$str = sanitize($str);
	}
	return $str;
}

if (isset($_GET)) $_GET = sanitize_r($_GET);
if (isset($_POST)) $_POST = sanitize_r($_POST);
if (isset($_REQUEST)) $_REQUEST = sanitize_r($_REQUEST);
?>
