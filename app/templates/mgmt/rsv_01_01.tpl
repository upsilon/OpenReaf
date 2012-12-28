{include file='header.tpl'}
<!-- templates rsv_01_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--

var courts = {/literal}{$array_court}{literal};

function onChangePlace( id )
{
	document.forma.ShitsujyoCode.options.length = 1;
	var i, j = 1;
	document.forma.ShitsujyoCode.options[0] = new Option('指定しない', '');
	for (i = 0; i < courts.length - 1; ++i) {// 末尾のdummy arrayぶんを引く
		if (courts[i][0] == document.forma.ShisetsuCode.options[id].value ) {
			document.forma.ShitsujyoCode.options[j++] = new Option(courts[i][2], courts[i][1]);
		}
	}
}

function onSelectCode(scd, rcd)
{
	var i, len;
	len = document.forma.ShisetsuCode.length;
	for (i = 0; i < len; ++i) {
		if (document.forma.ShisetsuCode.options[i].value == scd) {
			document.forma.ShisetsuCode.selectedIndex = i;
			break;
		}
	}
	var id = i;
	document.forma.ShitsujyoCode.options.length = 1;
	len = courts.length - 1; // 末尾のdummy arrayぶんを引く
	var j = 1;
	document.forma.ShitsujyoCode.options[0] = new Option('指定しない', '');
	for (i = 0; i < len; ++i) {
		if (courts[i][0] == document.forma.ShisetsuCode.options[id].value ) {
			document.forma.ShitsujyoCode.options[j++] = new Option(courts[i][2], courts[i][1]);
		}
	}
	if (rcd == '') {
		document.forma.ShitsujyoCode.selectedIndex = 0;
	} else {
		len = document.forma.ShitsujyoCode.length;
		for (i = 0; i < len; ++i) {
			if (document.forma.ShitsujyoCode.options[i].value == rcd) {
				document.forma.ShitsujyoCode.selectedIndex = i;
				break;
			}
		}
	}
}

function clearElements()
{
	document.forma.YoyakuNum.value = '';
	document.forma.UserIDFrom.value = '';
	document.forma.UserIDTo.value = '';
	document.forma.PartialMatchFlg.checked = false;
	document.forma.Name.value = '';
	document.forma.TelNo1.value = '';
	document.forma.TelNo2.value = '';
	document.forma.TelNo3.value = '';
	var i;
	var d = new Date();
	var yearFrom = document.forma.FromYear;
	var yearTo = document.forma.ToYear;
	var len = yearFrom.length;
	for (i=0; i<len; ++i) {
		if (yearFrom.options[i].value==d.getFullYear()) {
			yearFrom.options[i].selected = true;
			yearTo.options[i].selected = true;
		}
	}
	i = d.getMonth();
	document.forma.FromMonth.options[i].selected = true;
	document.forma.ToMonth.options[i].selected = true;
	document.forma.FromDay.options[0].selected = true;
	d = new Date(d.getFullYear(), d.getMonth()+1, 0);
	i = d.getDate()-1;
	document.forma.ToDay.options[i].selected = true;
	document.forma.ShisetsuCode.options[0].selected = true;
	document.forma.ShitsujyoCode.length = 1;
	document.forma.MokutekiCode.options[0].selected = true;

	len = document.forma.elements['HonYoyakuKbn[]'].length;
	for (i=0; i<len; i++) {
		document.forma.elements['HonYoyakuKbn[]'][i].checked = false;
	}
	len = document.forma.elements['ListStatus[]'].length;
	for (i=0; i<len; i++) {
		document.forma.elements['ListStatus[]'][i].checked = false;
	}
	document.forma.EscapeFlg.checked = false;
	len = document.forma.elements['PayKbn[]'].length;
	for (i=0; i<len; i++) {
		document.forma.elements['PayKbn[]'][i].checked = false;
	}
}
//-->
</script>
{/literal}

{** 室場未選択対応 **}
{if $p.ShisetsuCode != ''}
<body onLoad="onSelectCode('{$p.ShisetsuCode}', '{$p.ShitsujyoCode}');">
{else if}
<body>
{/if}

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt; <strong><u>予約状況検索</u></strong>
</div>

<h2 class="subtitle01">予約状況検索</h2>

<h3>■検索条件設定</h3>
<div class="margin-box">
<div class="itemtop-area">
・条件を入力し、検索ボタンを押してください。
</div>
{if $message}<div id="errorbox">{$message}</div>{/if}
<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_01_01_search" />

<table class="itemtable03">
<tr>
	<th width="80" nowrap>予約番号</th>
	<td colspan="3">
	<input name="YoyakuNum" value="{$p.YoyakuNum}" type="text" size="16" maxlength="16" style="ime-mode:disabled;">&nbsp;(完全一致)
	</td>
</tr>
<tr>
	<th nowrap>利用者ID</th>
	<td colspan="3">
	<input name="UserIDFrom" value="{$p.UserIDFrom}" type="text" size="16" maxlength="16" style="ime-mode:disabled;">
	〜
	<input name="UserIDTo" value="{$p.UserIDTo}" type="text" size="16" maxlength="16" style="ime-mode:disabled;">
	&nbsp;<label>前方一致で検索<input type="checkbox" name="PartialMatchFlg" value="1" {if $p.PartialMatchFlg == '1'}checked{/if}></label>
	</td>
</tr>
<tr>
	<th nowrap>利用者名</th>
	<td>
	<input name="Name" value="{$p.Name}" type="text" size="60" maxlength="60" style="ime-mode:active;">&nbsp;（部分一致）
	</td>
	<th nowrap>電話番号</th>
	<td>
	<input type="text" name="TelNo1" value="{$p.TelNo1}" size="5" maxlength="4" style="ime-mode:disabled;">&nbsp;-
	<input type="text" name="TelNo2" value="{$p.TelNo2}" size="5" maxlength="4" style="ime-mode:disabled;">&nbsp;-
	<input type="text" name="TelNo3" value="{$p.TelNo3}" size="5" maxlength="4" style="ime-mode:disabled;">
	</td>
</tr>
<tr>
	<th nowrap>予約状態</th>
	<td>
	{html_checkboxes name="HonYoyakuKbn" options=$aHonYoyakuKbn checked=$p.HonYoyakuKbn}
	&nbsp;&nbsp;({html_checkboxes name="ListStatus" options=$aListStatus checked=$p.ListStatus})
	</td>
 	<th nowrap>来場状態</th>
	<td>
	<label><input type="checkbox" name="EscapeFlg" value="1" {if $p.EscapeFlg == '1'}checked{/if}>不来場のみ</label>
 	</td>
</tr>
<tr>
	<th nowrap>施設</th>
	<td>
	<select name="ShisetsuCode" onChange="onChangePlace(this.selectedIndex)">
	<option value="">指定しない</option>
	{html_options options=$aShisetsu selected=$p.ShisetsuCode}
	</select>
	</td>
	<th width="60" nowrap>室場</th>
	<td>
	<select name="ShitsujyoCode"><option value="">指定しない</option></select>
	</td>
</tr>
<tr>
	<th nowrap>利用日</th>
	<td colspan="3">
	{html_select_date prefix='From' start_year='-5' end_year='+5' display_days=false display_months=false time=$p.dateFrom}年
	{html_select_date prefix='From' display_days=false display_years=false month_format='%m' time=$p.dateFrom}月
	{html_select_date prefix='From' display_months=false display_years=false day_value_format='%02d' time=$p.dateFrom}日
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'forma');" class="f-s_down"/>
	〜
	{html_select_date prefix='To' start_year='-5' end_year='+5' display_days=false display_months=false time=$p.dateTo}年
	{html_select_date prefix='To' display_days=false display_years=false month_format='%m' time=$p.dateTo}月
	{html_select_date prefix='To' display_months=false display_years=false day_value_format='%02d' time=$p.dateTo}日
	<input name="calendarTo" type="button" value="カレンダー" onclick="openCalendar('To', 'forma');" class="f-s_down"/>
	</td>
</tr>
<tr>
	<th nowrap>収納状態</th>
	<td colspan="3">
	{html_checkboxes name="PayKbn" options=$aPayKbn checked=$p.PayKbn}
	</td>
</tr>
<tr>
	<th nowrap>利用目的</th>
	<td colspan="3">
	<select name="MokutekiCode">
	<option value="">指定しない</option>
	{html_options options=$aMokuteki selected=$p.MokutekiCode}
	</select>
	</td>
</tr>
<tr>
	<td class="no-border">&nbsp;</td>
	<td class="no-border" colspan="3">
	<input type="submit" name="searchBtn" value="検索">&nbsp;&nbsp;
	<input type="button" name="clearBtn" value="クリア" onClick="clearElements();">
	</td>
</tr>
</table>
</form>
<!--/margin-box-->
</div>

{if $results}
<div class="itemtop-area"><h3>■検索結果一覧</h3></div>

<table class="itemtable02" width="98%">
<tr>
	<th width="60" rowspan="2">予約<br>番号</th>
	<th width="110" rowspan="2">利用日時</th>
	<th width="23%" rowspan="2">利用施設</th>
	<th width="28%" nowrap>利用者ID</th>
	<th width="80" rowspan="2">収納<br>状態</th>
	<th width="50" rowspan="2">詳細</th>
	<th width="50" rowspan="2">取消</th>
</tr>
<tr>
	<th>利用者名</th>
</tr>
{foreach $results as $d}
<tr>
	<td align="center">{$d.yoyakunum|default:'-'}<br>{if $d.class=='H'}取消{else}{$d.YoyakuKbnName|default:'-'}{/if}</td>
	<td align="center">{$d.UseDateView}<br>{$d.UseTimeFromView}〜{$d.UseTimeToView}</td>
	<td>{$d.ShisetsuName}<br>{$d.shitsujyoname}</td>
	<td>{$d.userid|default:"<p align=\"center\">-</p>"}<div class="border-top">{$d.namesei}</div></td>
	<td align="center">
	{if $d.PayKbn == 1}<span style="color:red">
	{elseif $d.PayKbn == 5}<span style="color:blue">{/if}
	{if $d.PayKbn < 5}
		{$d.PayKbnName}
	{else}
		<a href="index.php?op=rsv_02_06_exemption&YoyakuNum={$d.yoyakunum}{if $d.class=='H'}&delFlg=1{/if}">{$d.PayKbnName}</a>
	{/if}
	{if $d.PayKbn == 1 || $d.PayKbn == 5}</span>{/if}
	</td>
	<td align="center">
	<a title="詳細" href="index.php?op=rsv_02_01_detail&YoyakuNum={$d.yoyakunum}&fromYoyakuSearch=1">詳細</a>
	</td>
	<td align="center">
	{if $d.class=='H'}-{else}<a title="取消" href="index.php?op=rsv_02_04_cancel&YoyakuNum={$d.yoyakunum}" >取消</a>{/if}
	</td>
</tr>
{/foreach}
</table>
{else}
	{if $p.searchBtn != '' && !$message}
	該当データは存在しません。<br>
	{/if}
{/if}

{include file='footer.tpl'}
