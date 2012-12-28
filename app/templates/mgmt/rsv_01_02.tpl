{include file='header.tpl'}
<!-- templates rsv_01_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function doSubmit(obj, op, mode)
{
	obj.searchMode.value = mode;
	obj.op.value = op;
	obj.submit();
}
//-->
</script>
{/literal}

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
予約管理 &gt; <strong><u>空き状況照会/予約申込</u></strong>
</div>

<h2 class="subtitle01">空き状況照会／予約申込</h2>
<div class="itemtop-area">
<h3>■検索条件設定</h3>
・条件を入力し、検索ボタンを押してください。
</div>
{if $message}<div id="errorbox">{$message}</div>{/if}
<form name="form1" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_01_02_search">
<input type="hidden" name="searchMode" value="">
<table class="itemtable03">
<tr>
	<th width="80">施設</th>
	<td>
	<select name="ShisetsuCode" onchange="doSubmit(this.form, 'rsv_01_02_search', 0);">
	<option value="">指定しない</option>
	{html_options options=$aShisetsu selected=$p.ShisetsuCode}
	</select>
	</td>
	<th width="80">室場</th>
	<td>
	<select name="ShitsujyoCode">
	<option value="">指定しない</option>
	{html_options options=$aShitsujyo selected=$p.ShitsujyoCode}
	</select>
	</td>
</tr>
<tr>
	<th>表示期間</th>
	<td colspan="3">
	{html_select_date prefix='From' start_year='-3' end_year='+3' display_months=false display_days=false time=$dateFrom}年
	{html_select_date prefix='From' display_years=false display_days=false month_format='%m' time=$dateFrom}月
	{html_select_date prefix='From' display_years=false display_months=false day_value_format='%02d' time=$dateFrom}日から
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'form1');" class="f-s_down" />
	&nbsp;{html_radios name="Duration" options=$aDuration checked=$p.Duration|default:1}
	</td>
</tr>
{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
<tr>
	<th>表示時間帯</th>
	<td colspan="3">
	{html_checkboxes name="TimeArea" options=$aTimeArea checked=$p.TimeArea}
	<input type="button" name="reverseBtn" value="時間帯一括反転" onclick="alternateCheckBox(this.form, 'TimeArea');">
	</td>
</tr>
{/if}
<tr>
	<th>曜日</th>
	<td colspan="3">
	{html_checkboxes name="DayOfWeek" options=$aWeekJ checked=$p.DayOfWeek}
	&nbsp; 
	<input type="button" name="reverseBtn" value="曜日一括反転" onclick="alternateCheckBox(this.form, 'DayOfWeek');">
	</td>
</tr>
<tr>
	<th class="no-border">&nbsp;</th>
	<td class="no-border" colspan="3">
	<input type="button" name="searchBtn" value="検索" onclick="doSubmit(this.form, 'rsv_02_02_status', 1);">&nbsp;&nbsp;
	<input type="submit" name="clearBtn" value="クリア">
	</td>
</tr>	
</table>
<br>
<table class="itemtable03">
<tr>
	<th width="80" valign="top">利用目的</th>
	<td>
	<table>
		{if $aMokuteki01}
		<tr><td colspan="5">&lt;スポーツ施設&gt;</td></tr>
		{foreach $aMokuteki01 as $key => $val}
			{if $val@iteration mod 5 == 1}<tr>{/if}
			<td class="no-border"><label><input type="checkbox" name="chkGenre[{$key}]" value="{$key}" {if $p.chkGenre[$key]}checked{/if}>{$val}</label></td>
			{if $val@iteration mod 5 == 0}</tr>{/if}
		{/foreach}
		{if $val@iteration mod 5 == 1}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 2}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 3}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 4}<td class="no-border">&nbsp;</td></tr>{/if}
		{/if}
		{if $aMokuteki02}
		<tr><td colspan="5">&lt;文化施設&gt;</td></tr>
		{foreach $aMokuteki02 as $key => $val}
			{if $val@iteration mod 5 == 1}<tr>{/if}
			<td class="no-border"><label><input type="checkbox" name="chkGenre[{$key}]" value="{$key}" {if $p.chkGenre[$key]}checked{/if}>{$val}</label></td>
			{if $val@iteration mod 5 == 0}</tr>{/if}
		{/foreach}
		{if $val@iteration mod 5 == 1}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 2}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 3}<td class="no-border">&nbsp;</td><td class="no-border">&nbsp;</td></tr>{/if}
		{if $val@iteration mod 5 == 4}<td class="no-border">&nbsp;</td></tr>{/if}
		{/if}
	</table>
	</td>
</tr>
<tr>
	<th class="no-border">&nbsp;</th>
	<td colspan="2" class="no-border">
	<input type="button" name="searchBtn" value="検索" onclick="doSubmit(this.form, 'rsv_02_02_status', 1);">&nbsp;&nbsp;
	<input type="submit" name="clearBtn" value="クリア">
	</td>
</tr>
</table>
<br>
</form>

{include file='footer.tpl'}
