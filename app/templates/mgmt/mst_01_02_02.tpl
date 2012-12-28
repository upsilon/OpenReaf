{include file='header.tpl'}
<!-- templates mst_01_02_02.tpl -->

{if $success == 1}
<body onload="alert('{$message}')">
{else}
<body>
{/if}

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;<a href="index.php?op=mst_01_02_system">システムデータ登録</a>&nbsp;&gt;&nbsp;<strong>システムデータ変更</strong>
</div>

<h2 class="subtitle01">システムデータ変更</h2>

<div class="margin-box">
<input type="button" name="backBtn" value="戻る" class="btn-01" onClick="location.href='index.php?op=mst_01_02_system';" />
{if $message && $success < 0}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="mst_01_02_system">
<input type="hidden" name="mode" value="edit">
<table class="itemtable03">
<tr>
	<th colspan="2" align="left">自治体コード</th>
	<td>{$rec.localgovcode}</td>
</tr>
<tr>
	<th colspan="2" align="left">自治体名称</th>
	<td><input type="text" name="localgovname" value="{$rec.localgovname}" size="50" maxlength="24" style="ime-mode:active;" /></td>
</tr>
<tr>
	<th colspan="2" align="left">{$smarty.const._MAYOR_}名</th>
	<td><input type="text" name="mayorname" value="{$rec.mayorname}" size="50" maxlength="32" style="ime-mode:active;" /></td>
</tr>
<tr>
	<th width="120" rowspan="3">時間帯</th>
	<th width="180">午前</th>
	<td>
	<select name="AMFromH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.AMFromH}</select> :
	<select name="AMFromM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.AMFromM}</select> ～
	<select name="AMToH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.AMToH}</select> :
	<select name="AMToM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.AMToM}</select>
	</td>
</tr>
<tr>
	<th>午後</th>
	<td>
	<select name="PMFromH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.PMFromH}</select> :
	<select name="PMFromM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.PMFromM}</select> ～
	<select name="PMToH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.PMToH}</select> :
	<select name="PMToM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.PMToM}</select>
	</td>
</tr>
<tr>
	<th>夜間</th>
	<td>
	<select name="NTFromH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.NTFromH}</select> :
	<select name="NTFromM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.NTFromM}</select> ～
	<select name="NTToH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.NTToH}</select> :
	<select name="NTToM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.NTToM}</select>
	</td>
</tr>
<tr>
	<th rowspan="2">桁数の設定</th>
	<th>利用者ID</th>
	<td>最小桁数&nbsp;<input type="text" name="useridlngmin" value="{$rec.useridlngmin}" size="6" maxlength="4" style="text-align:right;ime-mode:disabled;" />&nbsp;最大桁数&nbsp;<input type="text" name="useridlng" value="{$rec.useridlng}" size="6" maxlength="4" style="text-align:right;ime-mode:disabled;" />&nbsp;入力サイズ&nbsp;<input type="text" name="userid_size" value="{$rec.userid_size}" size="6" maxlength="3" style="text-align:right;ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>パスワード</th>
	<td>最小桁数&nbsp;<input type="text" name="pwdlngmin" value="{$rec.pwdlngmin}" size="6" maxlength="4" style="text-align:right;ime-mode:disabled;" />&nbsp;最大桁数&nbsp;<input type="text" name="pwdlng" value="{$rec.pwdlng}" size="6" maxlength="4" style="text-align:right;ime-mode:disabled;" />&nbsp;入力サイズ&nbsp;<input type="text" name="pwd_size" value="{$rec.pwd_size}" size="6" maxlength="3" style="text-align:right;ime-mode:disabled;" /></td>
</tr>
<tr>
	<th rowspan="2">文字種の設定</th>
	<th>利用者ID</th>
	<td>{html_radios name="useridtype" options=$rec.InputTypeOptions selected=$rec.useridtype}</td>
</tr>
<tr>
	<th>パスワード</th>
	<td>{html_radios name="pwdtype" options=$rec.InputTypeOptions selected=$rec.pwdtype}</td>
</tr>
<tr>
	<th colspan="2" align="left">利用者パスワード自動発番</th>
	<td>{html_radios name="userpassautoflg" options=$rec.UserPassAutoFlgOptions selected=$rec.userpassautoflg}</td>
</tr>
<tr>
	<th colspan="2" align="left">利用者登録期限の使用</th>
	<td>{html_radios name="userlimitdispflg" options=$rec.UserLimitDispFlgOptions selected=$rec.userlimitdispflg}</td>
</tr>
<tr>
	<th colspan="2" align="left">施設分類の選択画面の使用</th>
	<td>{html_radios name="shisetsuclassscreenflg" options=$rec.ShisetsuClassScreenFlgOptions selected=$rec.shisetsuclassscreenflg}</td>
</tr>
<tr>
	<th colspan="2" align="left">施設利用権限の使用</th>
	<td>{html_radios name="shisetsurestrictionflg" options=$rec.ShisetsuRestrictionFlgOptions selected=$rec.shisetsurestrictionflg}</td>
</tr>
<tr>
	<th colspan="2" align="left">ログイン時間制限の使用</th>
	<td>{html_radios name="loginkbn" options=$rec.LoginKbnOptions selected=$rec.loginkbn}
	&nbsp;ログイン時間&nbsp;
	<select name="LoginTimeFromH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.LoginTimeFromH}</select> :
	<select name="LoginTimeFromM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.LoginTimeFromM}</select> から
	<select name="LoginTimeToH">{html_options values=$rec.hourValues output=$rec.hourValues selected=$rec.LoginTimeToH}</select> :
	<select name="LoginTimeToM">{html_options values=$rec.minuteValues output=$rec.minuteValues selected=$rec.LoginTimeToM}</select>
	</td>
</tr>
<tr>
	<th colspan="2" align="left">パスワードエラー時のロックアウト</th>
	<td>
	{html_radios name="lockoutflg" options=$rec.LockOutFlgOptions selected=$rec.lockoutflg}
	<br>ロックアウトまでのエラー回数&nbsp;<input type="text" name="lockout_count" size="4" maxlength="2" value="{$rec.lockout_count}" style="text-align:right;ime-mode:disabled;" />&nbsp;回&nbsp;&nbsp;自動解除時間&nbsp;<input type="text" name="reentry_interval" size="4" maxlength="2" value="{$rec.reentry_interval}" style="text-align:right;ime-mode:disabled;" />&nbsp;分
	</td>
</tr>
<tr>
	<th colspan="2" align="left">サイト閉鎖</th>
	<td>
	{html_radios name="sitecloseflg" options=$rec.SiteCloseFlgOptions selected=$rec.sitecloseflg}<br>
	開始{html_select_date prefix='From' start_year='-1' end_year='+3' display_days=false display_months=false time=$rec.siteclosefrom}年
	{html_select_date prefix='From' display_days=false display_years=false month_format='%m' time=$rec.siteclosefrom}月
	{html_select_date prefix='From' display_months=false display_years=false day_value_format='%02d' time=$rec.siteclosefrom}日
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'forma');" class="f-s_down"/>
	{html_select_time prefix='From' display_minutes=false display_seconds=false time=$rec.siteclosefrom}時
	{html_select_time prefix='From' display_hours=false display_seconds=false minute_interval=5 time=$rec.siteclosefrom}分<br>
	終了{html_select_date prefix='To' start_year='-1' end_year='+3' display_days=false display_months=false time=$rec.sitecloseto}年
	{html_select_date prefix='To' display_days=false display_years=false month_format='%m' time=$rec.sitecloseto}月
	{html_select_date prefix='To' display_months=false display_years=false day_value_format='%02d' time=$rec.sitecloseto}日
	<input name="calendarTo" type="button" value="カレンダー" onclick="openCalendar('To', 'forma');" class="f-s_down"/>
	{html_select_time prefix='To' display_minutes=false display_seconds=false time=$rec.sitecloseto}時
	{html_select_time prefix='To' display_hours=false display_seconds=false minute_interval=5 time=$rec.sitecloseto}分
	</td>
</tr>
<tr>
	<th colspan="2" align="left">サイト閉鎖時の表示メッセージ</th>
	<td><textarea name="siteclosemessage" cols="60" rows="3" style="ime-mode:active;">{$rec.siteclosemessage}</textarea></td>
</tr>
<tr>
	<th rowspan="2">URL</th>
	<th>自治体のホームページ</th>
	<td><input type="text" name="homepageurl" value="{$rec.homepageurl}" size="72" maxlength="160" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>システムトップURL</th>
	<td><input type="text" name="topmenuurl" value="{$rec.topmenuurl}" size="72" maxlength="160" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th rowspan="3">メールアドレス</th>
	<th>送信元メールアドレス</th>
	<td><input type="text" name="mailfromaddr" value="{$rec.mailfromaddr}" size="72" maxlength="128" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>送信元名</th>
	<td><input type="text" name="mailfromname" value="{$rec.mailfromname}" size="72" maxlength="64" /></td>
</tr>
<tr>
	<th>BCCメールアドレス</th>
	<td><input type="text" name="mailbccaddr" value="{$rec.mailbccaddr}" size="72" maxlength="128" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th rowspan="4">メールサーバ</th>
	<th>サーバ</th>
	<td><input type="text" name="mailhost" value="{$rec.mailhost}" size="32" maxlength="64" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>ポート番号</th>
	<td><input type="text" name="mailhostport" value="{$rec.mailhostport}" size="10" maxlength="6" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>アクセスID</th>
	<td><input type="text" name="mailhostuserid" value="{$rec.mailhostuserid}" size="32" maxlength="64" style="ime-mode:disabled;" /></td>
</tr>
<tr>
	<th>アクセスパスワード</th>
	<td><input type="text" name="mailhostuserpass" value="{$rec.mailhostuserpass}" size="32" maxlength="64" style="ime-mode:disabled;" /></td>
</tr>
</table>
<br />
<div align="center">
<input type="submit" name="updateBtn" value="変更" />&nbsp;&nbsp;
<input type="button" name="backBtn" value="戻る" onClick="location.href='index.php?op=mst_01_02_system';" />
</div>
</form>
</div>
{include file='footer.tpl'}
