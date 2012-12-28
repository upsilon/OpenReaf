{include file='header.tpl'}
<!-- templates mst_01_03_02.tpl -->

{if $success == 1}
<body onload="alert('{$message}')">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
発番管理 &gt; <a href="index.php?op=mst_01_03_number">発番情報</a> &gt; <strong><u>発番情報変更</u></strong>
</div>

<h2 class="subtitle01">発番情報変更</h2>

<div class="margin-box">
{if $message && $success < 0}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="SaibanCode" value="{$rec.saibancode}">
<h3 class="subtitle02">{$rec.saibanName}</h3>
<table class="ri-table">
<tr>
	<th width="130">番号</th>
	<td><input size="20" maxlength="11" name="saibanno" type="text" value="{$rec.saibanno}" style="ime-mode:disabled;"><font class="f-red">（必須）</font></td>
</tr>
<tr>
	<th>ケタ数</th>
	<td><input size="20" maxlength="3" name="saibannolng" type="text" value="{$rec.saibannolng}" style="ime-mode:disabled;"></td>
</tr>
<tr>
	<th>プレフィックス</th>
	<td><input size="20" maxlength="8" name="prefix" type="text" value="{$rec.prefix}" style="ime-mode:disabled;"><font class="f-red">（8文字以内）</font></td>
</tr>
<tr>
	<th>サフィックス</th>
	<td><input size="20" maxlength="8" name="suffix" type="text" value="{$rec.suffix}" style="ime-mode:disabled;"><font class="f-red">（8文字以内）</font></td>
</tr>
{if $rec.enabledSaibanFlg}
<tr>
	<th>自動発番</th>
	<td>{html_radios name='saibanflg' options=$saibanList checked=$rec.saibanflg}</td>
</tr>
{/if}
<tr>
	<td class="no-border" align="center">
	<input type="submit" name="updateBtn" value="変更">&nbsp;&nbsp;
	<input type="button" name="backBtn" value="戻る" onclick="window.location.href='index.php?op=mst_01_03_number';">
	</td>
	<td class="no-border">&nbsp;</td>
</tr>
</table>
</form>
</div>
{include file='footer.tpl'}
