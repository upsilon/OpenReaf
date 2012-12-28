{include file='header.tpl'}
<!-- templates fcl_04_10.tpl -->

{if $message}
<body onload="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>付属室場選択</u></strong>
</div>

<h2 class="subtitle01">付属室場選択</h2>

<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</td>
	<th width="50">室場名</th>
	<td width="160">{$rec.shitsujyoname}</td>
	<th width="70">適用開始日</th>
	<td width="70">{$rec.appdatefrom}</td>
</tr>
</table>
<form name="forma" method="post" action="index.php">
<input type="button" name="backBtn" value="戻る" onclick="submitTo(this.form, '{$back_url}')" class="btn-01">
<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th width="120">名称</th>
	<th>付属室場</th>
</tr>
{foreach $recs as $val}
<tr>
	<td align="center">{$val.combino}</td>
	<td>{$val.combiname}</td>
	<td id="choice_fuzoku">
	{if $mode=='ref'}
		{html_checkboxes name="fuzoku[{$val.combino}]" options=$aFuzoku checked=$req.fuzoku[$val.combino] disabled=true}
	{else}
		{html_checkboxes name="fuzoku[{$val.combino}]" options=$aFuzoku checked=$req.fuzoku[$val.combino]}
	{/if}
	</td>
</tr>
{/foreach}
{if $mode == 'mod'}
<tr>
	<td class="no-border" colspan="2" align="center">
	<input type="submit" name="updateBtn" value="登録" class="btn-01">
	</td>
	<td class="no-border">&nbsp;</td>
</tr>
{/if}
</table>
<br>

<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
</form>

{include file='footer.tpl'}
