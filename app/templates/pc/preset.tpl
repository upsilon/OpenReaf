{include file="header.tpl"}

<div align="center">
<form name="form1" method="post" action="index.php">
{if $presetCount > 0}
<table class="koma-table" width="720">
<tr>
	<th>{$smarty.const.OR_PRESET_FACILITY}</th>
	<th colspan="4">{$smarty.const.OR_REQUEST_MONTH}</th>
</tr>
{foreach $recs as $value}
<tr>
	<td>{$value.ShisetsuName}&nbsp;
		{$value.ShitsujyoName}&nbsp;{$value.MenName}
	</td>
	{if $value.WebOpen}
	{foreach $YearMonths as $YM => $val}
	<td width="56">
	<a href='index.php?op=monthly&ShisetsuCode={$value.shisetsucode}&ShisetsuClassCode={$value.ShisetsuClassCode}&ShitsujyoCode={$value.shitsujyocode}&CombiNo={$value.combino}&UseYM={$YM}' class="month-bt">{$val.Label}</a>
	</td>
	{/foreach}
	{else}
	<td colspan="4"><span class="disp-bt-no">{$smarty.const.OR_OUT_OF_SERVICE}</span></td>
	{/if}
</tr>
{/foreach}
</table>
{/if}
</form>
</div>

{include file="footer.tpl"}
