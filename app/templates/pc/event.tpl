{include file="header.tpl"}
<!--event.tpl-->

<div align="center">

<table class="month-cal-top" width="720">
<tr>
	<th class="shisets-name">{$ShisetsuName}<br /><span>{$strYM}</span></th>
	</td>
{foreach $YearMonths as $YM => $val}
	<td style="padding: 0 8px; width:75px;">
	{if $YM == $SelectedMonth}
	<div class="month-bt_on">{$val.Label}</div>
	{else}
	<a href="#" class="month-bt" onclick="doClickEvent('{$ShisetsuCode}', '{$YM}');">{$val.Label}</a>
	{/if}
	</td>
{/foreach}
</tr>
</table>

{strip}
<table width="720" class="table-style">
{foreach $recs as $day => $k}
<tr>
	{if $k.dow == 6}
	    <td width ="30" class="bg-blue-1">{$day}</td>
	    <td width="30" class="bg-blue-2">{$aWeek[$k.dow]}</td>
	    <td align="left" class="bg-blue-3">
	{elseif $k.dow == 0}
	    <td width="30" class="bg-red-1">{$day}</td>
	    <td width="30" class="bg-red-2">{$aWeek[$k.dow]}</td>
	    <td align="left" class="bg-red-3">
	{else}
	    <td width="30" class="bg-gray-1">{$day}</td>
	    <td width="30" class="bg-gray-2">{$aWeek[$k.dow]}</td>
	    <td align="left" class="bg-gray-3">
	{/if}
	{foreach $k.event as $val}<font color="#0000ff" size="3">{$val.name}</font>&nbsp;({$val.shitsujyoname}{if $val.combiname != ''}&nbsp;{$val.combiname}{/if}&nbsp;{$val.usetime})<br>{/foreach}</td>
</tr>
{/foreach}
</table>
{/strip}

</div>

<form name="formq" method="post" action="index.php">
<input type="hidden" name="op" value="">
<input type="hidden" name="ShisetsuCode" value="">
<input type="hidden" name="UseYM" value="">
</form>

{include file="footer.tpl"}
