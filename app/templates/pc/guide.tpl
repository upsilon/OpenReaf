{include file="header.tpl"}
<div align="center">
<strong>{$smarty.const.OR_FACILITY_GUIDE}</strong>
</div>

<table width="760" id="annai-table">
<tr align="center">
	<th>{$smarty.const.OR_FACILITY_NAME}</th>
	<th>{$smarty.const.OR_ADDRESS}</th>
	<th>{$smarty.const.OR_PHONE}</th>
	{if $event_button == 1}<th width="80">{$smarty.const.OR_EVENT}</th>{/if}
</tr>
{foreach $recs as $val}
<tr>
	<th class="shisetsu-neme">
	{if $val.guideurl}
	<a href="{$val.guideurl}" target="_blank">{$val.shisetsuname}</a>
	{else}
	{$val.shisetsuname}
	{/if}
	</th>
	<td align="left">{$val.adr}</td>
	<td align="left">{$val.telno21}-{$val.telno22}-{$val.telno23}</td>
	{if $event_button == 1}
	<td>{if $val.showeventflg == '1'}<a href="#" class="disp-bt" onclick="doClickEvent('{$val.shisetsucode}', '{$UseYM}');">{$smarty.const.OR_DISPLAY}</a>{else}&nbsp;{/if}</td>
	{/if}
</tr>
{foreachelse}
<tr>
	<td>
	{$smarty.const.NO_FACILITY}
	</td>
</tr>
{/foreach}
</table>

<form name="formq" method="post" action="index.php">
<input type="hidden" name="op" value="">
<input type="hidden" name="ShisetsuCode" value="">
<input type="hidden" name="UseYM" value="">
</form>

{include file="footer.tpl"}
