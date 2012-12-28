{include file='header.tpl'}
<!-- templates fcl_03_03.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<strong><u>室場{if $mode == 'abo'}廃止{else}削除{/if}</u></strong></p>
</div>

<h2 class="subtitle01">室場{if $mode == 'abo'}廃止{else}削除{/if}</h2>

<table width="300" class="itemtable02">
<tr>
	<th width="95">施設名</th>
	<td>{$rec.shisetsuname}</td>
</tr>
<tr>
	<th>室場名</th>
	<td>{$rec.shitsujyoname}</td>
</tr>
<tr>
	<th>適用開始日</th>
	<td>{$rec.appdatefrom}</td>
</tr>
</table>
<br />
{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
{if $mode == 'abo'}
	{if $aboSuccess}
	<p>室場の廃止を取り消します。</p><br>
	<input type="submit" name="resumeBtn" value="廃止取消">
	{else}
	<p>室場を廃止します。</p><br>
	<table>
	<tr>
		<td>
		廃止日&nbsp;<input type="text" name="HaishiDate" value="{$para.haishidate}" size="10" maxlength="8" style="ime-mode:disabled" OnKeyPress="if (event.keyCode == 13){literal}{event.returnValue = false;}{/literal}">
		<input type="hidden" name="appdatefrom" value="{$para.appdatefrom}">
		</td>
	</tr>
	</table>
	<br>
	<input type="submit" name="expireBtn" value="廃止">
	{/if}
{else}
	<input type="submit" name="deleteBtn" value="削除" onclick="returnn confirm('削除しますか？');" {if $success == 1}disabled{/if}>
{/if}
&nbsp;&nbsp;
<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
</form>

{include file='footer.tpl'}
