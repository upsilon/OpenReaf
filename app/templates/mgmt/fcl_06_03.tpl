{include file='header.tpl'}
<!-- templates fcl_06_03.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_07_list&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">料金情報</a> &gt;
<a href="index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}">料金設定期間一覧</a> &gt;
<strong><u>料金設定{if $mode == 'abo'}廃止{else}削除{/if}</u></strong>
</div>

<h2 class="subtitle01">料金設定{if $mode == 'abo'}廃止{else}削除{/if}</h2>

<div class="margin-box">
<table width="380" class="itemtable02">
<tr>
	<th width="100">施設名</th>
	<td>{$rec.shisetsuname}</td>
</tr>
<tr>
	<th>室場名</th>
	<td>{$rec.shitsujyoname}</td>
</tr>
{if $rec.MenName}
<tr>
	<th>組合せ名称</th>
	<td>{$rec.menname}</td>
</tr>
{/if}
<tr>
	<th width="100">適用開始日</th>
	<td>{$para.appdatefrom}</td>
</tr>
<tr>
	<th>廃止日</th>
	<td>{$para.haishidate}</td>
</tr>
<tr>
	<th width="100">料金設定期間</th>
	<td>{if $para.monthdayfrom<>''}{$para.monthdayfrom}〜{$para.monthdayto}{/if}</td>
</tr>
</table>

{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
{if $mode == 'abo'}
	{if $aboSuccess}
	<p>料金設定の廃止を取り消します。</p>
	<input type="submit" name="resumeBtn" value="廃止取消">
	{else}
	<p>料金設定を廃止します。</p>
	<table>
	<tr>
		<td {if $haishiError}class="error"{/if}>
		廃止日&nbsp;<input type="text" name="HaishiDate" value="{$para.haishidate}" size="10" maxsize="8" style="ime-mode:disabled" OnKeyPress="if (event.keyCode == 13) {literal}{event.returnValue = false;}{/literal}">
		<input type="hidden" name="appdatefrom" value="{$para.appdatefrom}">
		</td>
	</tr>
	</table>
	<br />
	<input type="submit" name="expireBtn" value="廃止">
	{/if}
{else}
	<br />
	<input type="submit" name="deleteBtn" value="削除" onclick="returnn confirm('削除しますか？');" {if $success == 1}disabled{/if}>
{/if}
&nbsp;&nbsp;
<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="cno" value="{$req.cno}">
<input type="hidden" name="tcd" value="{$req.tcd}">
<input type="hidden" name="apd" value="{$req.apd}">
<input type="hidden" name="prfr" value="{$req.prfr}">
<input type="hidden" name="prto" value="{$req.prto}">
</form>
</div>

{include file='footer.tpl'}
