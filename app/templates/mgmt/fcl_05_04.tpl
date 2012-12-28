{include file='header.tpl'}
<!-- templates fcl_05_04.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
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
<a href="index.php?op=fcl_04_03_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">申込不可日設定</a> &gt;
<strong><u>申込不可日{if $mode=='mod'}設定{else}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">申込不可日{if $mode=='mod'}設定{else}照会{/if}</h2>

<form name="forma" method="post" action="index.php">

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

<table width="400">
<tr>
	<td align="right">
	<select name="YoyakuKbn" onChange="document.forma.submit();">
		{html_options options=$aYoyakuKbn selected=$req.YoyakuKbn}
	</select>
	</td>
</tr>
<tr>
	<td align="center">

	<table width="350">
	<tr align="center">
		<th valign="middle" style="font-size:150%;">{$para.year}年{$para.month}月</th>
	</tr>
	</table>
	<table width="350" class="calendar-table">
	<tr height="30" align="center">
	    <th><span class="sun">日</span></th>
	    <th>月</th>
	    <th>火</th>
	    <th>水</th>
	    <th>木</th>
	    <th>金</th>
	    <th><span class="sat">土</span></th>
	</tr>
	{foreach $recs as $key => $val}
	  {if $key%7 == 0}<tr height="30" align="center">{/if}
	    <td>
	    {if $val.day == 0}
	      &nbsp;
	    {else}
	      {if $val.holiday == '1'}<span class="f-red">{$val.day}</span>{else}{$val.day}{/if}<br>
	      {if $val.closed == '2'}<span class="f-red">休</span>{else}&nbsp;{/if}<br>
	      {if $mode == 'mod'}
	      <input type="checkbox" name="Day[{$val.day}]" value="1" {if $val.closed > 100}checked {if $val.closed != $para.YoyakuKbn}disabled="true"{/if}{/if}>
	      {else}
	      <input type="checkbox" name="Day[{$val.day}]" value="1" {if $val.closed > 100}checked{/if} disabled="true">
	      {/if}
	    {/if}
	    </td>
	  {if $key%7 == 6}</tr>{/if}
	{/foreach}
	</table>

	</td>
</tr>
<tr>
	<td align="center">
	{if $mode=='mod'}
	<input type="submit" name="updateBtn" value="更新">
	{/if}
	<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="selYear" value="{$req.selYear}">
<input type="hidden" name="selMonth" value="{$req.selMonth}">
</form>

{include file='footer.tpl'}
