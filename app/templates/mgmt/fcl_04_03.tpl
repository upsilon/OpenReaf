{include file='header.tpl'}
<!-- templates fcl_04_03.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function setModify(action, year, month)
{
	document.forma.selYear.value = year;
	document.forma.selMonth.value = month;
	document.forma.op.value =action;
	document.forma.submit();
}
// -->
</script>
{/literal}

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>申込不可日設定</u></strong>
</div>

<h2 class="subtitle01">申込不可日設定</h2>

<input type="button" name="backBtn" value="処理選択へ戻る" onclick="location.href='index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';" class="btn-01">

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
<br />

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="selYear" value="">
<input type="hidden" name="selMonth" value="">
<table width="80%">
<tr>
	<td align="center">
		<table width="90%">
		<tr>
			<td width="10%">&nbsp;</td>
			<td align="left">
				<input type="button" value="定期休館日" onclick="setModify('fcl_05_03_mod', '', '');">
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr height="20">
	<td>&nbsp;</td>
</tr>
<tr height="15">
	<td align="center">
	<table width="90%">
	<tr>
		<td width="10%">&nbsp;</td>
		<td align="left"><strong>申込不可日</strong></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td width="100%" align="center">
	<table width="90%" cellspacing="4" cellpadding="4" style="outline-style:solid;outline-width:thin;">
	{foreach $res as $key => $val}
	{if $key == 1}<tr>{/if}
		<td width="16%" align="center">
		<input type="button" value="{$val.monthName}" onclick="setModify('fcl_05_04_mod', '{$val.year}', '{$val.month}')">
		</td>
		{if $key == 6}
		</tr>
		<tr>
		{elseif $key == 12}
		</tr>
		{/if}
	{/foreach}
	</table>
	</td>
</tr>
</table>
</form>

{include file='footer.tpl'}
