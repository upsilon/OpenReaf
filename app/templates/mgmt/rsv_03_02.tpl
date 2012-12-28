{include file='header.tpl'}
<!-- templates rsv_03_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
var openerWindow;

function openPdf(Place, UseDate)
{
	var now = new Date();
	var time = now.getTime();
	window.open('index.php?op=rsv_03_02_status&pdf=1&scd='+Place+'&date='+UseDate+'&random='+time, 'rsv_03_02');
}

function pushYoyaku(yoyakuKey)
{
	var now = new Date();
	var time = now.getTime();

	var openerWindowMode = document.form_sumrsv.openerWindowMode.value;
	var windowName = 'YoyakuMulti';
	var sizeOption = 'width=900,height=800,dependent=yes,scrollbars=yes,resizable=yes';

	// ポップアップウィンドウが開かれているかどうかを判定する
	isOpenerWindowFlg = isOpenerWindow();
	// ポップアップウィンドウが開かれていない場合
	if (isOpenerWindowFlg == 0) {
		document.form_sumrsv.openerWindowMode.value = 1;
		openerWindow = window.open("index.php?op=rsv_03_06_01_user&yoyakuKey="+yoyakuKey+'&random='+time,windowName,sizeOption);
	} else {
		// 予約情報一括入力画面が開かれている場合
		if (openerWindowMode == 2) {
			openerWindow= window.open('index.php?op=rsv_03_06_02_list&yoyakuKey='+yoyakuKey+'&mode=setList' +'&random='+ time, windowName);
		}
		// それ以外のウィンドウの場合、何もしない
	}
}

// ウィンドウが開かれているかどうかを判定する
function isOpenerWindow()
{
	var flg;
	if (openerWindow) {
		if (openerWindow.closed) {
			flg = 0;
		} else {
			flg = 1;
		}

	} else {
		flg = 0;
	}
	return flg;
}

function gotoNext(flg)
{
	// 正常申込があれば、予約登録画面へ遷移します
	if (flg>0) {
		url = 'index.php?op=rsv_04_02_input&firstAccess=1';
		parent.top.location.href=url;
	} else if (flg == -1) {
		// なければ、エラーを表示します。
		alert('利用する時間帯を選択してください。');
	} else if (flg == -2) {
		// またいだ申込の場合もエラー表示します
		alert('時間をまたいだ申込はできません。');
	}
}
//-->
</script>
{/literal}

<body {if $okFlg}onFocus="gotoNext('{$okFlg}');"{/if}>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt; 
<a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt; 
<a href="index.php?op=rsv_02_02_status">空き状況表示</a> &gt; 
<strong><u>指定日空き状況詳細表示</u></strong>
</div>

<h2 class="subtitle01">指定日空き状況詳細表示</h2>
<div class="itemtop-area">
<h3 class="subtitle02">{$showShisetsuName}&nbsp;&nbsp;&nbsp;&nbsp;<input name="btm_pdfprint" type="button" onClick="openPdf('{$req.scd}','{$req.date}');" value="印刷"></h3>
&nbsp;&nbsp;<strong>{$showSelectDate}</strong>
</div>

{foreach $recs as $val1}
	{foreach $val1 as $val2}
	{if $val2.lineCount==1 or $val2.lineCount=='simple'}
	<table class="itemtable02">
	{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
		<tr>
		{****  午前・午後・夜間  ****}
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		{if $val2.aAPN.am}
			{if $reserve_band_active[0]}
			<th height="25" colspan="{$val2.aAPN.am}">午前</th>
			{/if}
		{/if}
		{if $val2.aAPN.pm}
			{if $reserve_band_active[1]}
			<th colspan="{$val2.aAPN.pm}">午後</td>
			{/if}
		{/if}
		{if $val2.aAPN.nt}
			{if $reserve_band_active[2]}
			<th colspan="{$val2.aAPN.nt}">夜間</td>
			{/if}
		{/if}
	</tr>
	{/if}
	<tr height="25" align="center">
	<td width="131" class="gray" nowrap>
	<strong>{$val2.shitsujyoname}</strong>
	</td>
	<td width="40" class="gray" nowrap>定員</td>
		{foreach $val2.aTimeKoma as $value}
			{if $value.apnFlg}
			{if $val2.Komasu > 12}
			<td class="gray">{$value.FromView}-</td>
			{elseif $val2.Komasu > 8}
			<td class="gray" nowrap>{$value.FromView}〜</td>
			{else}
			<td width="59" class="gray" nowrap>{$value.FromView}〜</td>
			{/if}
			{/if}
		{/foreach}
	</tr>
	{/if}
	<tr align="center">
		<td class="gray" nowrap>
			{if $val2.combiname==''}
				全面
			{else}
				{$val2.combiname}
			{/if}
		</td>
		<td>
			{if $val2.teiin==0}-
			{else}{$val2.teiin|default:'&nbsp;'}
			{/if}
		</td>
		{foreach $val2.aTimeKoma as $key => $value}
			{if $value.apnFlg}
		<td>
				{if $value.set}<span class="y-blu">申し込む</span>
				{elseif $value.reserved}<span class="y-blu">{if $value.YoyakuNum == ''}{$value.Mark|default:'&nbsp;'}{else}<a href="#" onclick="openReserveInfo('{$value.YoyakuNum}');">{$value.Mark|default:'&nbsp;'}</a>{/if}</span>
				{else}<span class="y-blu">
					{if $searchMode == 1}
						<a href="index.php?op=rsv_03_01_status&scd={$val2.shisetsucode}&rcd={$val2.shitsujyocode}&mcd={$val2.mencode}&cno={$val2.combino}&date={$val2.UseDate}">
					{else}
						<a href="#" onclick="pushYoyaku('{$val2.shisetsucode},{$val2.UseDate},{$val2.shitsujyocode},{$val2.mencode},{$val2.combino}');">
					{/if}○</a></span>
				{/if}
		</td>
			{/if}
		{/foreach}
	</tr>
	{if $val2.lineCount=='last' or $val2.lineCount=='simple'}
	{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
	<tr>
		<td height="15" colspan="{$val2.komaCount}">
			<span class="fsize_10">※&nbsp;午前&nbsp;{$val2.AMFromView}-{$val2.AMToView}&nbsp;&nbsp;午後&nbsp;{$val2.PMFromView}-{$val2.PMToView}&nbsp;&nbsp;夜間&nbsp;{$val2.NTFromView}-{$val2.NTToView}</span>
		</td>
	</tr>
	{/if}
</table>
<br />
	{/if}
	{/foreach}
{/foreach}

<div class="bt-area">
<input type="button" name="backBtn" onclick="location.href='index.php?op=rsv_02_02_status';" value="戻る" />
</div>

<form name="form_sumrsv">
  <input type="hidden" name="openerWindowMode" value="{$openerWindowMode|default:0}">
  <input type="hidden" name="isOpenerWindowFlg" value="{$isOpenerWindowFlg|default:0}">
</form>

{include file='footer.tpl'}
