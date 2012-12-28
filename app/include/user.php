<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  user.php
 */

// 選択項目の配列定義
//
$userareakbn_arr = array(
				'01' => _INSIDE_,
				'02' => _OUTSIDE_ 
//				'03' => 'その他'
);

$mailsendflg_arr = array('送信しない', '送信する', '抽選結果のみ送信する');

$nengoukbn_arr = array('不明', '明治', '大正', '昭和', '平成');

$seibetsukbn_arr = array('不明', '男性', '女性');

$yoyakukyokaflg_arr = array(1 => '可', 0 => '不可');

$userjyoutaikbn_arr = array('未承認', '通常', '利用停止', '登録抹消', '不承');

$optionItems = array(
			'KojinDanKbn',
//			'KoukaiKbn',
//			'CardUse',
			'UserKubun',
			'HonninKakuninKubun'
);

// フォーム情報
//
// カラム名, 日本語カラム名, 入力タイプ, 必須フラグ, basic/detail/stop
//
$columns = array(
	'userid' => array('利用者ID', 'text', false, 'basic'),
	'firstapplydate' => array('申請日', 'date', true, 'basic'),
	'kojindankbn' => array('団体／個人', 'radio', true, 'user'),
	'userareakbn' => array('地域区分', 'radio', true, 'user', $userareakbn_arr),
	'usekbn' => array('料金区分', 'select', false, 'basic'),
	'namesei' => array('利用者氏名', 'text', true, 'user'),
	'nameseikana' => array('利用者氏名('._KANA_.')', 'text', true, 'user'),
	'hyoujimei' => array('略称', 'text', false, 'basic'),
	'headnamesei' => array('代表者氏名', 'text', false, 'user'),
	'headnameseikana' => array('代表者氏名('._KANA_.')', 'text', false, 'user'),
	'contactname' => array('担当者氏名', 'text', false, 'basic'),
	'contactnamekana' => array('担当者氏名('._KANA_.')', 'text', false, 'basic'),
	'postno1' => array('郵便番号1', 'text', true, 'user'),
	'postno2' => array('郵便番号2', 'text', true, 'user'),
	'adr1' => array('住所', 'text', true, 'user'),
	'adr2' => array('アパート名等', 'text', false, 'user'),
	'telno11' => array('市外局番', 'text', true, 'user'),
	'telno12' => array('局番', 'text', true, 'user'),
	'telno13' => array('電話番号', 'text', true, 'user'),
	'telno21' => array('市外局番（緊急）', 'text', false, 'user'),
	'telno22' => array('局番（緊急）', 'text', false, 'user'),
	'telno23' => array('電話番号（緊急）', 'text', false, 'user'),
	'telno31' => array('市外局番（担当者）', 'text', false, 'basic'),
	'telno32' => array('局番（担当者）', 'text', false, 'basic'),
	'telno33' => array('電話番号（担当者）', 'text', false, 'basic'),
	'faxno1' => array('市外局番（FAX）', 'text', false, 'basic'),
	'faxno2' => array('局番（FAX）', 'text', false, 'basic'),
	'faxno3' => array('電話番号（FAX）', 'text', false, 'basic'),
	'mailadr' => array('メールアドレス', 'text', false, 'user'),
	'mailsendflg' => array('メール送信指定', 'radio', false, 'user', $mailsendflg_arr),
	'nengoukbn' => array('年号', 'radio', false, 'user', $nengoukbn_arr),
	'bdayyear' => array('誕生年', 'text', false, 'user'),
	'bdaymonth' => array('誕生月', 'text', false, 'user'),
	'bdayday' => array('誕生日', 'text', false, 'user'),
	'seibetsukbn' => array('性別', 'radio', false, 'user', $seibetsukbn_arr),
	'pwd' => array('パスワード', 'text', true, 'basic'),
	'kouseijinnin' => array('構成人員', 'number', false, 'user'),
	'kouseijinmeisai1' => array(_INSIDE_.'在住', 'number', false, 'basic'),
	'kouseijinmeisai2' => array(_INSIDE_.'在勤', 'number', false, 'basic'),
	'kouseijinmeisai3' => array(_INSIDE_.'在学', 'number', false, 'basic'),
	'kouseijinmeisai4' => array('その他', 'number', false, 'basic'),
//	'koukaikbn' => array('広告等へ紹介の可否', 'radio', false, 'basic'),
	'userkubun' => array('区分', 'radio', false, 'basic'),
	'honninkakuninkubun' => array('本人確認書類', 'radio', false, 'basic'),
//	'carduse' => array('住基カードの有無', 'radio', false, 'basic'),
	'userlimit' => array('利用者登録期限', 'date', true, 'basic'),
	'yoyakukyokaflg' => array('予約', 'radio', true, 'basic', $yoyakukyokaflg_arr),
	'yoyakukyokaflgweb' => array('インターネット利用', 'radio', true, 'basic', $yoyakukyokaflg_arr),
	'nojiyu' => array('不可事由', 'text', false, 'basic'),
	'katudogaiyou' => array('活動概要', 'text', false, 'detail'),
	'kaihijyouhou' => array('会費', 'text', false, 'detail'),
	'katudodate' => array('活動日・時間', 'text', false, 'detail'),
	'lecturerjyouhou' => array('講師・指導者', 'text', false, 'detail'),
	'thanksjyouhou' => array('謝礼', 'text', false, 'detail'),
	'bikou' => array('備考', 'text', false, 'detail'),
	'userjyoutaikbn' => array('利用者登録状態', 'radio', false, 'stop', $userjyoutaikbn_arr),
	'stoperasedate' => array('停止/抹消日', 'text', false, 'stop'),
	'stopenddate' => array('利用停止解除日', 'text', false, 'stop'),
	'stoperasejiyu' => array('停止/抹消事由', 'text', false, 'stop')
);
?>
