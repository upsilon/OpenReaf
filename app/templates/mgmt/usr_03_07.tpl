{include file='header.tpl'}
<!-- templates usr_03_07.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <a href="index.php?op=usr_02_01_02_mod&UserID={$UserID}">利用者情報変更</a> &gt; <strong><u>メッセージ設定</u></strong>
</div>

<h2 class="subtitle01">メッセージ設定</h2>

<div class="margin-box">
<input type="button" value="戻る" class="btn-01" onclick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#message';">

<table width="240" class="itemtable02">
<tr>
	<th width="100">利用者ID</th>
	<td>　{$para.userid}</td>
</tr>
<tr>
	<th>利用者名</th>
	<td nowrap>　{$para.namesei}</td>
</tr>
</table>
<br>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">
<table class="ri-table">
<tr>
	<th>メッセージ</th>
	<td>
	<textarea name="notice" cols="60" rows="3" style="ime-mode:active;">{$para.notice}</textarea>
	</td>
</tr>
<tr>
	<th>表示可否</th>
	<td>
	{html_radios name="notice_flg" options=$dispflg_arr selected=$para.notice_flg}
	</td>
</tr>
<tr>
	<th>表示日時</th>
	<td>
	<input type="checkbox" name="published_flg" value="1" {if $para.published_flg == 1}checked{/if}>&nbsp;表示日時を指定する<br>
	{html_select_date prefix='From' start_year='-1' end_year='+3' display_days=false display_months=false time=$para.notice_published}年
	{html_select_date prefix='From' display_days=false display_years=false month_format='%m' time=$para.notice_published}月
	{html_select_date prefix='From' display_months=false display_years=false day_value_format='%02d' time=$para.notice_published}日
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'forma');" class="f-s_down"/>
	{html_select_time prefix='From' display_minutes=false display_seconds=false time=$para.notice_published}時
	{html_select_time prefix='From' display_hours=false display_seconds=false minute_interval=5 time=$para.notice_published}分
	</td>
</tr>
<tr>
	<th>表示期限</th>
	<td>
	<input type="checkbox" name="expired_flg" value="1" {if $para.expired_flg == 1}checked{/if}>&nbsp;表示期限を指定する<br>
	{html_select_date prefix='To' start_year='-1' end_year='+3' display_days=false display_months=false time=$para.notice_expired}年
	{html_select_date prefix='To' display_days=false display_years=false month_format='%m' time=$para.notice_expired}月
	{html_select_date prefix='To' display_months=false display_years=false day_value_format='%02d' time=$para.notice_expired}日
	<input name="calendarTo" type="button" value="カレンダー" onclick="openCalendar('To', 'forma');" class="f-s_down"/>
	{html_select_time prefix='To' display_minutes=false display_seconds=false time=$para.notice_expired}時
	{html_select_time prefix='To' display_hours=false display_seconds=false minute_interval=5 time=$para.notice_expired}分
	</td>
</tr>
<tr>
  <td colspan="2" class="no-border" align="center">
    <input type="submit" name="tourokuBtn" value="登録" onclick="return confirm('登録しますか？');">
  </td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
