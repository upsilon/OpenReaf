{include file="header.tpl"}

{include file="page-header.tpl"}
<!--apply_conf.tpl-->

<div class="conf">
<form name="dataForm" method ='post' action='index.php'>
<input type="hidden" name="op" value="apply">

<h3><span>{$smarty.const.OR_USE_INFORMATION}</span></h3>
<table>
<tr>
	<th>{$smarty.const.OR_DATE_AND_TIME}</th>
	<td>{$UseDateDisp}&nbsp;{$UseTime}</td>
</tr>
<tr>
	<th>{$smarty.const.OR_REQUESTED_FACILITY}</th>
	<td>{$ShisetsuName}<br />
	{$ShitsujyoName}
	{if $CombiNo != 0}&nbsp;{$CombiName}{/if}
	{if $FuzokuName}
		{foreach $FuzokuName as $value}
		<br />&nbsp;{$value}
		{/foreach}
	{/if}
	</td>
</tr>
<tr>
	<th>{$smarty.const.OR_USAGE_FEE}</th>
	<td>{$Fee}円</td>
</tr>
<tr>
	<th class="f-red">{$smarty.const.OR_NUMBER_OF_PEOPLE}</th>
	<td class="ninzu">
		{if $showDanjyoNinzuFlg}
		<input type="hidden" name="useninzu" value="0">
		{$aNinzu.ninzu1[0]}
		<input type="number" name="ninzu1" value="" size="6" maxlength="6"><br>
		{$aNinzu.ninzu2[0]}
		<input type="number" name="ninzu2" value="" size="6" maxlength="6"><br>
		{else}
		<input type="number" name="useninzu" value="" size="6" maxlength="11"><br>
		<input type="hidden" name="ninzu1" value="0">
		<input type="hidden" name="ninzu2" value="0">
		{/if}
	</td>
</tr>
</table>
{if $notice}{$notice}{/if}
<div class="btn-area">
	<input type="submit" name="apply" value="{$smarty.const.OR_APPLY}" class="rsv-btn">
</div>

</form>
</div>

{include file="footer.tpl"}
