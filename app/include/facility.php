<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  facility.php
 */

$shisetsukbn_arr = array('01' => 'スポーツ系施設', '02' => '文化系施設');
$openflg_arr = array(1 => '公開する', 0 => '公開しない');
$delflg_arr = array('公開', '非公開');

//料金設定
$feetourokukbn_arr = array(
	'2' => '全時間帯一定(固定料金)',
	'1' => 'コマ時間毎',
	'3' => 'コマ時間毎(重複あり)',
	'4' => 'コマ時間毎(オプション時間あり)'
);

//抽選受付
$pulloutukemnflg_arr = array(
	'1'=>'受付不可',
	'2'=>'受付'
);

//予約時受付方法
$ippanyoyakukbn_arr = array(
	 '1'=>'即時確定',
	 '2'=>'仮予約受付'
);

//一般受付開始日フラグ(市内/市外)
$ippanresstartflg_arr = array(
	'1'=>'ヶ月前',
	'2'=>'日前',
	'3'=>'開始日指定'
);

//予約受付開始日が閉庁日の場合
$ipnchgflg1_arr = array(
	'1'=>'そのまま',
	'2'=>'翌開庁日から予約可'
);

//予約受付開始日が閉館日の場合
$ipnchgflg2_arr = array(
	'1'=>'そのまま',
	'2'=>'翌開館日から予約可'
);

//一般受付締切日フラグ(市内/市外)
$ippanreslimitflg_arr = array(
	'1'=>'ヶ月前',
	'2'=>'日前',
	'3'=>'締切日指定'
);

//取消受付締切日フラグ
$ippancanlimitflg_arr = array('取消不可', '取消可(日前)', '取消可(締切日指定)');

// 1: 予約 2: 空き状況のみ 0: 非表示
$openkbn_arr = array(1, 2, 0);

//インターネット受付時間帯
$webuketimekbn_arr = array('制限しない', '制限する', '制限する(空き状況表示あり)');

//申込制限方法
$limitflg_arr = array('なし', '回数', 'コマ数');

//抽選申込制限対象期間
$pulloutmonlimitkbn_arr = array('月間全体', '平日／土日祝日分離');

//団体／個人予約申込制限フラグ
$grouporpersonallimit_arr = array('制限なし', '個人のみ', '団体のみ');

//市内／市外予約申込制限フラグ
$areapriorityflg_arr = array('制限なし', _INSIDE_.'のみ');

//抽選受付
$pulloutflg_arr = array('抽選しない', '抽選する(自動抽選)');

//抽選受付とする期間
$pulloutukekbn_arr = array(
	'1'=>'通年',
	'2'=>'特定期間'
);

//当選者による当選確定処理
$fixflg_arr = array('確定処理不要', '確定処理必要');

//当選確定処理時受付方法
$kariyoyakuflg_arr = array(
	'1'=>'即時確定',
	'2'=>'仮予約受付'
);

//当選確定処理時制限範囲
$pulloutfixlimitflg_arr = array('制限しない', '室場', '施設', '施設分類');

//当選確定処理時制限方法
$pulloutfixlimitkbn_arr = array(
	'1'=>'申込回数',
	'2'=>'コマ数'
);

//抽選申込コマ数制限
$pulloutkomalimitflg_arr = array('制限しない', '1コマのみ', '確定制限数');

//室場区分
$shitsujyokbn_arr = array(
	'1'=>'一般（付属なし）',
	'2'=>'一般（付属あり）',
	'3'=>'付属',
	'4'=>'加算料金'
);

//端数処理フラグ
$fractionflg_arr = array('1円未満切捨て', '1円未満切上げ', '1円未満四捨五入', '10円未満切捨て', '10円未満切上げ', '10円未満四捨五入');

//使用料支払期限
$feepaylimtkbn_arr = array('指定しない', '利用日前払い', '利用日当日前払い',
	'利用日当日後払い', '利用日後払い', '申込日後払い', '申込日翌月払い');

//前払い時、支払期限切れの予約
$feeLimitautocanflg_arr = array('自動取消しない', '自動取消する');

$komatanitimekbn_arr = array('1'=>'時間', '2'=>'分');

define ('_MAX_KOMA_', 49);

$menu_arr = array(
	'mod' => array('basic' => array('label' => '室場基本情報', 'op' => 'fcl_03_01_02_mod'),
			'restriction' => array('label' => '制限設定', 'op' => 'fcl_04_04_mod'),
			'purpose' => array('label' => '利用目的設定', 'op' => 'fcl_04_11_mod'),
			'timetable' => array('label' => '予約時間割', 'op' => 'fcl_04_01_summary'),
			'schedule' => array('label' => '一般予約設定', 'op' => 'fcl_04_06_mod'),
			'men' => array('label' => '利用単位情報', 'op' => 'fcl_04_08_summary'),
			'close' => array('label' => '申込不可日設定', 'op' => 'fcl_04_03_menu'),
			'fee' => array('label' => '料金情報', 'op' => 'fcl_04_07_list'),
			'combination' => array('label' => '利用可能単位組合せ', 'op' => 'fcl_04_09_summary'),
			'blank1' => array('label' => '', 'op' => ''),
			'blank2' => array('label' => '', 'op' => ''),
			'fuzoku' => array('label' => '付属室場選択', 'op' => 'fcl_04_10_mod'),
		),
	'ref' => array('basic' => array('label' => '室場基本情報', 'op' => 'fcl_03_01_02_mod'),
			'restriction' => array('label' => '制限設定', 'op' => 'fcl_04_04_mod'),
			'purpose' => array('label' => '利用目的設定', 'op' => 'fcl_04_11_mod'),
			'timetable' => array('label' => '予約時間割', 'op' => 'fcl_04_01_summary'),
			'schedule' => array('label' => '一般予約設定', 'op' => 'fcl_04_06_mod'),
			'men' => array('label' => '利用単位情報', 'op' => 'fcl_04_08_summary'),
			'close' => array('label' => '申込不可日設定', 'op' => 'fcl_04_03_menu'),
			'fee' => array('label' => '料金情報', 'op' => 'fcl_04_07_list'),
			'combination' => array('label' => '利用可能単位組合せ', 'op' => 'fcl_04_09_summary'),
			'blank1' => array('label' => '', 'op' => ''),
			'blank2' => array('label' => '', 'op' => ''),
			'fuzoku' => array('label' => '付属室場選択', 'op' => 'fcl_04_10_mod'),
		)
);
?>
