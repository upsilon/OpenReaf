<?php
/*
 *  Copyright 2011-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  mail_template.php
 */

/*
 *  申し込み確認用メール
 */
function confirm_mail_body(&$rec)
{
	global $aShinsaKbn;

	$content = <<< __END_OF_BODY1__
◇ {$rec['LocalGovName']}{$rec['ShisetsuName']} ◇

{$rec['namesei']}　様

施設の{$rec['type']}申し込み{$rec['cancel']}を受け付けました。

------------------------
{$rec['type']}申し込み{$rec['cancel']}内容
------------------------
■予約番号
 {$rec['yoyakunum']}
■利用日時
 {$rec['UseDateDisp']}
 {$rec['UseTime']}
■利用施設
 {$rec['ShisetsuName']}
 {$rec['shitsujyoname']}

__END_OF_BODY1__;
if ($rec['Fuzoku']) {
	foreach ($rec['Fuzoku'] as $val)
	{
		$content.= ' '.$val."\r\n";
	}
}
if ($rec['cancel'] == '') {
	$content.= <<< __END_OF_BODY2__
■施設使用料
 {$rec['Fee']} 円

__END_OF_BODY2__;

if ($rec['type'] == '抽選') {
	$content.= "\r\n抽選日は".$rec['LotDay']."です。\r\n";
} else {
if ($rec['shinsakbn'] == '0') {
if ($rec['Notice'] != '') {
	$content.= <<< __END_OF_BODY3__

《注意事項》
{$rec['Notice']}に
ご利用料金を所定の窓口にてお支払いください。

__END_OF_BODY3__;
}
} elseif ($rec['shinsakbn'] == '4') {
	$content.= "\r\n".$aShinsaKbn[$rec['shinsakbn']]."\r\n";
}
}
}
	$content.= <<< __END_OF_BODY4__

※※※
このメールは送信専用です。ご返信いただいても回答できません。
------------------------
{$rec['LocalGovName']}{$rec['BushoName']}

__END_OF_BODY4__;
	if ($rec['ShisetsuTel'] != '') {
		$content.= $rec['ShisetsuTel']."\r\n";
	}
	$content.= <<< __END_OF_BODY5__
{$rec['TopMenuURL']}
------------------------

__END_OF_BODY5__;

	return $content;
}

/*
 *  審査結果メール
 */
function shinsa_mail_body(&$rec)
{
	global $aShinsaKbn;

	$sending_time = date('Y-m-d H:i');

	$content = <<< __END_OF_BODY1__
◇ {$rec['LocalGovName']}{$rec['ShisetsuName']} ◇

{$rec['namesei']}　様

施設予約の審査結果をお知らせします。

審査日時：{$rec['ShinsaDateView']}

------------------------
審査結果 ＜{$aShinsaKbn[$rec['shinsakbn']]}＞
------------------------
■予約番号
 {$rec['yoyakunum']}
■利用日時
 {$rec['UseDateDisp']}
 {$rec['UseTime']}
■利用施設
 {$rec['ShisetsuName']}
 {$rec['shitsujyoname']}

__END_OF_BODY1__;
if ($rec['Fuzoku']) {
	foreach ($rec['Fuzoku'] as $val)
	{
		$content.= ' '.$val."\r\n";
	}
}
if ($rec['shinsakbn'] == 1) {
	$content.= <<< __END_OF_BODY2__
■施設使用料
 {$rec['Fee']} 円

__END_OF_BODY2__;
if ($rec['Notice'] != '') {
	$content.= <<< __END_OF_BODY3__

《注意事項》
{$rec['Notice']}に
ご利用料金を所定の窓口にてお支払いください。

__END_OF_BODY3__;
}
}
	$content.= <<< __END_OF_BODY4__

※※※
このメールは送信専用です。ご返信いただいても回答できません。
------------------------
{$rec['LocalGovName']}{$rec['BushoName']}

__END_OF_BODY4__;
	if ($rec['ShisetsuTel'] != '') {
		$content.= $rec['ShisetsuTel']."\r\n";
	}
	$content.= <<< __END_OF_BODY5__
{$rec['TopMenuURL']}
------------------------
送信日時: {$sending_time}
__END_OF_BODY5__;

	return $content;
}

/*
 *  抽選結果メール
 */
function result_mail_body(&$rec)
{
	$sending_time = date('Y-m-d H:i');

	$content = <<< __END_OF_BODY__
◇ {$rec['LocalGovName']}{$rec['ShisetsuName']} ◇

{$rec['NameSei']}　様

施設予約{$rec['usemonth']}月分の抽選結果をお知らせします。

{$rec['pulldate']}に抽選を行いました。

------------------------
抽選結果
------------------------
■予約番号
 {$rec['YoyakuNum']}
■利用日時
 {$rec['UseDateView']}
 {$rec['UseTimeFromView']}-{$rec['UseTimeToView']}
{$rec['yoyakuinfostr']}
__END_OF_BODY__;
if ($rec['PullOutJoukyouKbn'] == '3') {
	$content.= <<< __END_OF_BODY2__
■施設使用料
 {$rec['Fee']} 円

__END_OF_BODY2__;
if ($rec['Notice'] != '') {
	$content.= <<< __END_OF_BODY3__

《注意事項》
{$rec['Notice']}に
ご利用料金を所定の窓口にてお支払いください。

__END_OF_BODY3__;
}
}
	$content.= <<< __END_OF_BODY4__

※※※
このメールは送信専用です。ご返信いただいても回答できません。
------------------------
{$rec['LocalGovName']}{$rec['BushoName']}

__END_OF_BODY4__;
	if ($rec['ShisetsuTel'] != '') {
		$content.= $rec['ShisetsuTel']."\r\n";
	}
	$content.= <<< __END_OF_BODY5__
{$rec['TopMenuURL']}
------------------------
送信日時: {$sending_time}
__END_OF_BODY5__;

	return $content;
}

/*
 *  利用者登録受付確認メール
 */
function register_confirm_mail(&$rec)
{
	$system_name = _SYSTEM_NAME_;
	$contact_tel = _CONTACT_TEL_;
	$sending_time = date('Y-m-d H:i');

	$content = <<< __END_OF_BODY__
◇◇ {$system_name} ◇◇

{$rec['namesei']}　様

ご利用登録の申し込みを受け付けました。

------------------------
ご利用登録受付内容
------------------------
■受付番号
 {$rec['userid']}
■受付日時
 {$rec['accept_date']}

↓下記のURLをクリックして申込を完了してください↓
{$rec['topmenuurl']}/accept.php?{$rec['para']}

受付内容を審査後、登録したメールアドレスに結果を通知します。
それまでお待ちください。

※※※
　このメールは、{$system_name}の利用申込の際に
　入力していただいたメールアドレスに自動的に送信しています。
　当メールは送信専用です。ご返信いただいても回答できません。
　なお、このメールに心当たりのない場合は、{$system_name}まで
　お知らせください。
------------------------
　{$system_name}

__END_OF_BODY__;
	if ($contact_tel != '') {
		$content.= '　'.$contact_tel."\r\n";
	}
	$content.= <<< __END_OF_BODY2__
　{$rec['topmenuurl']}
------------------------
送信日時: {$sending_time}
__END_OF_BODY2__;

	return $content;
}

/*
 *  利用登録お知らせメール
 */
function register_complete_mail(&$rec)
{
	$system_name = _SYSTEM_NAME_;

	$content = <<< __END_OF_BODY__
◇◇ {$system_name} ◇◇

{$rec['namesei']}　様

__END_OF_BODY__;
	if ($rec['temporaryid'] != '') {
		$content.= "(受付番号：{$rec['temporaryid']})\r\n";
	}
if ($rec['userjyoutaikbn'] == '4') {
	$content.= <<< __END_OF_BODY2__

利用登録の審査結果をお知らせします。

------------------------
審査結果 ＜不承＞
------------------------

なお、ご登録いただいた情報は破棄いたします。

__END_OF_BODY2__;
} else {
	$content.= <<< __END_OF_BODY2__

システムの利用登録が完了しました。

------------------------
登録内容
------------------------
■利用者ID
 {$rec['userid']}
■パスワード
 {$rec['pwd']}
■登録日
 {$rec['NewApplyDate']}

本メールのパスワードは仮パスワードです。
必ずご本人よるパスワードの変更を行ってください。
パソコンから下記のURLにアクセスしてパスワード変更を行うことができます。
{$rec['topmenuurl']}

上記のURLにアクセス後、以下のように進みます。
1. 「マイページ」ボタンをクリック
2. 「ログイン」画面でこのメールの利用者IDとパスワードを使ってログイン
3. 「利用者メニュー」画面の「利用者情報の変更」ボタンをクリック
4. 「利用者情報照会」画面の「パスワード変更」ボタンをクリック
5. 「パスワード変更」画面でパスワードを変更

__END_OF_BODY2__;
}

	$content.= <<< __END_OF_BODY3__

※※※
当メールは送信専用です。ご返信いただいても回答できません。
------------------------
　{$system_name}
　{$rec['topmenuurl']}
------------------------

__END_OF_BODY3__;

	return $content;
}

/*
 *  申し込み確認用メール
 */
function bulk_confirm_mail(&$recs, &$sys)
{
	$system_name = _SYSTEM_NAME_;
	$sending_time = date('Y-m-d H:i');

	$content = <<< __END_OF_BODY1__
◇◇ {$system_name} ◇◇

{$_SESSION['UNAME']}　様

施設利用の申し込みを受け付けました。

------------------------
申し込み内容
------------------------

__END_OF_BODY1__;
	foreach ($recs as $val)
	{
		$content.= "{$val['StatusName']}\r\n";
		$content.= "■予約番号\r\n";
		$content.= " {$val['YoyakuNum']}\r\n";
		$content.= "■利用日時\r\n";
		$content.= " {$val['UseDateView']}\r\n {$val['UseTimeView']}\r\n";
		$content.= "■利用施設\r\n";
		$content.= " {$val['shisetsuname']}\r\n {$val['shitsujyoname']}";
		if ($val['combiname'] != '') $content.= " {$val['combiname']}";
		$content.= "\r\n";
		$content.= "■施設使用料\r\n";
		$content.= " {$val['ShisetsuFeeView']} 円\r\n\r\n";
	}
	$content.= <<< __END_OF_BODY2__
※※※
このメールは送信専用です。ご返信いただいても回答できません。
------------------------
{$system_name}
{$sys['topmenuurl']}
------------------------
送信日時: {$sending_time}
__END_OF_BODY2__;

	return $content;
}
?>
