{include file='header.tpl'}
<!-- templates rsv_01_04.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--

var courts = {/literal}{$array_court}{literal};

function onChangePlace( id )
{
	document.forma.ShitsujyoCode.options.length = 1;
	var i, j = 1;
	for (i = 0; i < courts.length - 1; ++i) // 末尾のdummy arrayぶんを引く
	{
		if (courts[i][0] == document.forma.ShisetsuCode.options[id].value )
		{
			document.forma.ShitsujyoCode.options[j++] = new Option( courts[i][2], courts[i][1] );
		}
	}
}

function onSelectCode(scd, rcd)
{
	var i, len;
	len = document.forma.ShisetsuCode.length;
	for (i = 0; i < len; ++i)
	{
		if (document.forma.ShisetsuCode.options[i].value == scd) {
			document.forma.ShisetsuCode.selectedIndex = i;
			break;
		}
	}
	var id = i;
	document.forma.ShitsujyoCode.options.length = 1;
	len = courts.length - 1; // 末尾のdummy arrayぶんを引く
	var j = 1;
	for (i = 0; i < len; ++i)
	{
		if (courts[i][0] == document.forma.ShisetsuCode.options[id].value )
		{
			document.forma.ShitsujyoCode.options[j++] = new Option( courts[i][2], courts[i][1] );
		}
	}
	if (rcd == '') {
		document.forma.ShitsujyoCode.selectedIndex = 0;
	} else {
		len = document.forma.ShitsujyoCode.length;
		for (i = 0; i < len; ++i)
		{
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
	var i;
	var d = new Date();
	var yearFrom = document.forma.FromYear;
	var len = yearFrom.length;
	for (i=0; i<len; i++)
	{
		if(yearFrom.options[i].value==d.getFullYear()) yearFrom.options[i].selected=true;
	}
	var monthFrom = document.forma.FromMonth;
	len = monthFrom.length;
	for (i=0; i<len; i++)
	{
		if (monthFrom.options[i].value==(d.getMonth()+1)) monthFrom.options[i].selected=true;
	}
	var dayFrom = document.forma.FromDay;
	len = dayFrom.length;
	for (i=0; i<len; i++)
	{
		if (dayFrom.options[i].value==d.getDate()) dayFrom.options[i].selected=true;
	}
	document.forma.ShisetsuCode.options[0].selected = true;
	document.forma.ShitsujyoCode.length = 1;
	onChangePlace(0);
	document.forma.ShitsujyoCode.options[0].selected = true;
}
//-->
</script>
{/literal}

{if $p.ShisetsuCode != ''}
<body onLoad="onSelectCode('{$p.ShisetsuCode}','{$p.ShitsujyoCode}');">
{else if}
<body>
{/if}

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
収納管理 &gt; <strong><u>使用料等受付/使用許可</u></strong>
</div>

<h2 class="subtitle01">使用料等受付/使用許可</h2>

<div class="margin-box">
<div id="itemtop-area">
・条件を指定し、検索ボタンを押してください。
</div>
<br>
<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_01_04_search">
<table class="itemtable03">
<tr>
	<th width="80" nowrap>予約番号</th>
	<td colspan="4">
	<input name="YoyakuNum" value="{$p.YoyakuNum}" type="text" size="16" maxlength="16" style="ime-mode:disabled;">&nbsp;(完全一致)
	</td>
</tr>
<tr>
	<th nowrap>利用日</th>
	<td colspan="4">
	{html_select_date prefix='From' start_year='-3' end_year='+3' display_months=false display_days=false time=$p.dateFrom}年
	{html_select_date prefix='From' display_years=false display_days=false month_format='%m' time=$p.dateFrom}月
	{html_select_date prefix='From' display_years=false display_months=false day_value_format='%02d' time=$p.dateFrom}日
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'forma');" class="f-s_down" />
	</td>
</tr>
<tr>
	<th>施設</th>
	<td>
	<select name="ShisetsuCode" onChange="onChangePlace(this.selectedIndex);" >
	{html_options options=$aShisetsu selected=$p.ShisetsuCode}
	</select>
	</td>
	<th width="60">室場</th>
	<td>
	<select name="ShitsujyoCode">
	<option value="">指定しない</option>
	{html_options options=$ShitsujyoArr selected=$p.ShitsujyoCode}
	</select>
	</td>
	<td>
	<label><input type="checkbox" name="WithoutCancelFlg" value="1" {if $p.WithoutCancelFlg == '1'}checked{/if}>予約のみ</label>
	</td>
</tr>
<tr>
	<td class="no-border">&nbsp;</td>
	<td class="no-border" colspan="4">
	<input type="submit" name="searchBtn" value="検索">&nbsp;&nbsp;
	<input type="button" name="clearBtn" value="クリア" onclick="clearElements();">
	</td>
</tr>
</table>
</form>
<!--/margin-box-->
</div>

{if $results}
<table class="itemtable02" width="99%">
<tr>
	<th width="7%">予約<br>番号</th>
	<th width="20%">室場・利用単位</th>
	<th width="7%">利用<br>時間</th>
	<th width="20%">利用者名</th>
	<th width="5%">料金</th>
	<th width="7%">詳細</th>
	<th width="7%">利用<br>受付</th>
	<th width="7%">利用<br>取消</th>
	<th width="7%">不来場</th>
</tr>
{foreach $results as $val}
<tr align="center">
	<td {if !$val.PayStatus}class="get"{/if}>{$val.yoyakunum}<br>{$val.YoyakuKbnName}</td>
	<td align="left" {if !$val.PayStatus}class="get"{/if}>{$val.ShisetsuName}<br>{$val.shitsujyoname}</td>
	<td {if !$val.PayStatus}class="get"{/if}>{$val.UseTimeFromView}〜{$val.UseTimeToView}</td>
	<td align="left" {if !$val.PayStatus}class="get"{/if}>{$val.namesei}</td>
	<td {if !$val.PayStatus}class="get"{/if}>{$val.PayKbnName}</td>
	<td {if !$val.PayStatus}class="get"{/if}>
		<input type="button" name="Submit" value="詳細" onclick="location.href='index.php?op=rsv_02_01_detail&YoyakuNum={$val.yoyakunum}&fmode=rsv_01_04';" {if $val.escapeflg==1}disabled{/if}>
	</td>
	{****
	<td nowrap {if !$val.PayStatus}class="get"{/if}>
		{if $val.honyoyakukbn=='02'}
			収納済<br /><input type="button" name="Submit3" value="領収書" onclick="openReceiptPdf('{$val.yoyakunum}');">
		{else}
			未収納
		{/if}
	</td>
	****}
	<td nowrap {if !$val.PayStatus}class="get"{/if}>
		{if $val.useukeflg}済{else}未{/if}<br>
		<input type="button" name="Submit2" value="受付" onclick="location.href='index.php?op=rsv_02_05_receipt&YoyakuNum={$val.yoyakunum}';" value="受付" {if $val.class=='H'}disabled{/if}>
	</td>
	<td nowrap {if !$val.PayStatus}class="get"{/if}>
		<input type="button" name="Submit2" value="取消" onclick="location.href='index.php?op=rsv_02_04_cancel&YoyakuNum={$val.yoyakunum}';" {if $val.escapeflg==1}disabled{/if}>
	</td>
	<td nowrap {if !$val.PayStatus}class="get"{/if}>
	<form name="formb" method="post" action="index.php">
	<input type="hidden" name="op" value="rsv_01_04_search">
	<input type="hidden" name="YoyakuNum" value="{$val.yoyakunum}">
		{if $val.escapeflg == 1}
		<input type="hidden" name="escapeFlg" value="0">
		受付済<br><input type="submit" name="dontComeBtn" value="取消" onclick="return confirm('不来場を取消しますか？');">
		{else}
		<input type="hidden" name="escapeFlg" value="1">
		<input type="submit" name="dontComeBtn" value="受付" onclick="return confirm('不来場を受付けますか？');" {if $val.class=='H'}disabled{/if}>
		{/if}
	</form>
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
