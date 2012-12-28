{include file='header.tpl'}
<!-- templates usr_02_03.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者登録</a> &gt; <strong><u>利用者削除</u></strong>
</div>

<h2 class="subtitle01">利用者削除</h2>

<p><input type="button" value="検索へ戻る" onclick="location.href='index.php?op=usr_01_01_search&back=1';"></p>

{if $message}<div id =errorbox>{$message}</div>{/if}

<h4 class="subtitle02">基本情報</h4>

{include file='usr_basic.tpl'}

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">
<table width="650" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" name="deleteBtn" value="削除" onclick="return confirm('削除しますか？');" {if $message}disabled{/if}></td>
</tr>
</table>
</form>

{include file='footer.tpl'}
