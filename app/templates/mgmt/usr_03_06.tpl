{include file='header.tpl'}
<!-- templates usr_03_06.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <a href="index.php?op=usr_02_01_02_mod&UserID={$UserID}">利用者情報変更</a> &gt; <strong><u>アクセス状態変更</u></strong>
</div>

<h2 class="subtitle01">アクセス状態変更</h2>

<div class="margin-box">
<input type="button" value="戻る" class="btn-01" onclick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#access';">

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
	<th>最終ログイン時間</th>
	<td>{$para.LastLogin}</td>
</tr>
<tr>
	<th>パスワードエラー回数</th>
	<td>{$para.loginerr_count}&nbsp;回
	{if $para.LockOut == 1}
	&nbsp;<strong class="f-red">ロックアウト中</strong>
	{/if}
	</td>
</tr>
<tr>
  <td colspan="2" class="no-border" align="center">
    <input type="submit" name="tourokuBtn" value="エラー回数クリア" onclick="return confirm('実行しますか？');">
  </td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
