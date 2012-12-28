{include file='header.tpl'}
<!-- templates fcl_05_07.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_08_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">利用単位情報（利用単位一覧）</a> &gt;
<strong><u>利用単位情報{if $mode == 'abo'}廃止{else}削除{/if}</u></strong></p>
</div>

<h2 class="subtitle01">利用単位情報{if $mode == 'abo'}廃止{else}削除{/if}</h2>

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
	<th>利用単位名</th>
	<td>{$para.menname}</td>
</tr>
<tr>
	<th>適用開始日</th>
	<td>{$para.appdatefrom}</td>
</tr>
</table>
<br />
{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
{if $mode == 'abo'}
	{if $aboSuccess}
	<p>利用単位の廃止を取り消します。</p><br>
	<input type="submit" name="resumeBtn" value="廃止取消">
	{else}
	<p>利用単位を廃止します。</p><br>
	<table>
	<tr>
		<td width="60" align="center">廃止日</td>
		<td>
		<input type="text" name="MenHaishiDate" value="{$para.menhaishidate}" size="10" maxlength="8" style="ime-mode:disabled" {if $err.HaishiDate}class="error"{/if} OnKeyPress="if (event.keyCode == 13){literal}{event.returnValue = false;}{/literal}">
		<input type="hidden" name="appdatefrom" value="{$para.appdatefrom}">
		</td>
	</tr>
	</table>
	<br>
	<input type="submit" name="expireBtn" value="廃止">
	{/if}
{else}
	<input type="submit" name="deleteBtn" value="削除" onclick="return confirm('削除しますか？');" {if $success == 1}disabled{/if}>
{/if}
&nbsp;&nbsp;
<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="mcd" value="{$req.mcd}">
</form>

{include file='footer.tpl'}
