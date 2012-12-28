{include file='header.tpl'}
<!-- templates stf_01_02.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
職員管理 &gt; <strong><u>パスワード変更</u></strong>
</div>

<h2 class="subtitle01">パスワード変更</h2>

{if $message}<div id="errorbox">{$message}</div>{/if}

<div class="margin-box">
<form name="forma" method="post" action="index.php">
<table>
<tr>
	<td>・現在のパスワード</td>
	<td>
	<input name="oldpwd" type="password" value="{$para.oldpwd}" style="ime-mode:disabled" size="20" maxlength="16">
	</td>
</tr>
<tr>
	<td>・新しいパスワード</td>
	<td>
	<input name="pwd" type="password" value="{$para.pwd}" style="ime-mode:disabled" size="20" maxlength="16">
	</td>
</tr>
<tr>
	<td nowrap>・新しいパスワード(確認用)</td>
	<td>
	<input name="pwd2" type="password" value="{$para.pwd2}" style="ime-mode:disabled" size="20" maxlength="16">　※確認のためコピーせず直接入力してください。
	</td>
</tr>
<tr>
	<td align="right">
	<input type="submit" name="updateBtn" value="変更">
	</td>
	<td>&nbsp;</td>
</tr>
</table>
<input type="hidden" name="op" value="stf_01_02_pwd">
</form>
</div>

{include file='footer.tpl'}
