{include file='header.tpl'}
<!-- templates mst_02_02.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;
<a href="index.php?op=mst_01_01_top">マスタデータ登録</a>&nbsp;&gt;&nbsp;
<strong>閉庁日・祝祭日</strong>
</div>

<h2 class="subtitle01">閉庁日・祝祭日</h2>

<div align="center">
{if $errmsg}<div id=errorbox>{$errmsg}</div>{/if}
<form name="forma" method="post" action"index.php">
<input type="hidden" name="op" value="mst_02_02_holiday">
<input type="hidden" name="page_no" value="{$page_no}">
<input type="submit" name="saveBtn" value="保存" class="btn-01">
<input type="submit" name="deleteBtn" value="削除" class="btn-01" onclick="return confirm('削除してもよろしいですか？');">
<input type="submit" name="copyBtn" value="コピー" class="btn-01" onclick="return confirm('コピーしてもよろしいですか？');">
<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='index.php?op=mst_01_01_top';">
{if $pages}
<br>
{foreach $pages as $datum}
	{if $datum.set == 1}{$datum.pagenum}
	{else}<a href="{$datum.url}">[{$datum.pagenum}]</a>{/if}
	&nbsp;
{/foreach}
{/if}
<table class="itemtable02">
<tr>
	<th>&nbsp;</th>
	<th>年月日</th>
	<th>閉庁日・祝祭日名称</th>
	<th>祝祭日</th>
</tr>
<tr align="center">
	<td>&nbsp;</td>
	<td>
	{html_select_date prefix='From' start_year='-2' end_year='+5' display_months=false display_days=false time=$dateFrom}年
	{html_select_date prefix='From' display_days=false display_years=false month_format='%m' time=$dateFrom}月
	{html_select_date prefix='From' display_months=false display_years=false day_value_format='%02d' time=$dateFrom}日
	<input name="calendarFrom" type="button" value="カレンダー" onClick="openCalendar('From', 'forma')" class="f-s_down"/>
	</td>
	<td><input type="text" name="codename" value="" size="30" maxlength="64" style="ime-mode:active;"></td>
	<td>
	<select name="flg">
	{html_options options=$aHoliFlg selected=1}
	</select>
	</td>
</tr>
{foreach $results  as $key => $value}
<tr align="center">
	<td><input type="checkbox" name="checkCode[{$key}]" value="1"></td>
	<td>{$value.heichouholiday}<input type="hidden" name="Code[{$key}]" value="{$value.heichouholiday}"></td>
	<td><input type="text" size="30" maxlength="64" name="CodeName[{$key}]" value="{$value.heichouholidayname}" style="ime-mode:active;"></td>
	<td>
	<select name="Flg[{$key}]">
	{html_options options=$aHoliFlg selected=$value.holiflg}
	</select>
	</td>
</tr>
{/foreach}
</table>
</form>
</div>

{include file='footer.tpl'}
