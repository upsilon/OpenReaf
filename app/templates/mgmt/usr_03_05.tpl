{include file='header.tpl'}
<!-- templates usr_03_05.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <a href="index.php?op=usr_02_01_02_mod&UserID={$UserID}">利用者情報変更</a> &gt; <strong><u>減免設定</u></strong>
</div>

<h2 class="subtitle01">減免設定</h2>

<div class="margin-box">
<input type="button" value="戻る" class="btn-01" onclick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#genmen';">

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
<div class="itemtop-area">減免情報を設定します。</div>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">
<table class="itemtable02">
<tr>
	<th width="100" height="30">適用減免</th>
	<td align="left">
	<select name="KoteiGenCode">
	<option value="" {if !$para.koteigencode}selected{/if}>減免なし</option>
	{html_options options=$aGenmen selected=$para.koteigencode}
	</select>
	<input name="KeizokuFlg" type="radio" value="0" {if $para.keizokuflg=="0"}checked{/if}>新規
	<input name="KeizokuFlg" type="radio" value="1" {if $para.keizokuflg!="0"}checked{/if}>継続
	</td>
</tr>
<tr>
	<th height="30">適用開始</th>
	<td>
	{html_select_date prefix='AppDay' start_year='-3' end_year='+3' display_months=false display_days=false time=$para.appday|default:0}年
	{html_select_date prefix='AppDay' display_years=false display_days=false month_format='%m' time=$para.appday|default:0}月
	{html_select_date prefix='AppDay' display_years=false display_months=false day_value_format='%02d' time=$para.appday|default:0}日
	</td>
</tr>
<tr>
	<th height="30">有効期限</th>
	<td>
	{html_select_date prefix='LimitDay' start_year='-3' end_year='+3' display_months=false display_days=false time=$para.limitday|default:0}年
	{html_select_date prefix='LimitDay' display_years=false display_days=false month_format='%m' time=$para.limitday|default:0}月
	{html_select_date prefix='LimitDay' display_years=false display_months=false day_value_format='%02d' time=$para.limitday|default:0}日
	</td>
</tr>
<tr>
	<td colspan="2" class="no-border" align="center">
	<input type="submit" name="updateBtn" value="登録" onclick="return confirm('登録しますか？');">
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
