{include file="header.tpl"}
<!--apply.tpl-->

{literal}
<script type="text/javascript" language="javascript">
<!--
window.onload = function()
{
	var msg = {/literal}'{$PresetResult}'{literal};
	if (msg != '') alert(msg);
}
// -->
</script>
{/literal}

<div align="center">
<h2 class="time-title" style="width:600px;">{$smarty.const.OR_USE_INFORMATION}</h2>

<table width="600" class="table-style">
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_RECEIPT_NUMBER}</th>
	<td>{$YoyakuNum}</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_DATE_AND_TIME}</th>
	<td>{$info.UseDateDisp}&nbsp;{$info.UseTime}</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_REQUESTED_FACILITY}</th>
	{strip}
	<td>
	{$info.ShisetsuName}<br>{$info.ShitsujyoName}{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
	{if $info.Fuzoku}
	{foreach $info.Fuzoku as $value}
	<br>{$value}
	{/foreach}
	{/if}
	</td>
	{/strip}
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_SPECIFIED_PURPOSE}</th>
	<td>{$info.MokutekiName}</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_USAGE_FEE}</th>
	<td>{$Fee}円</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_STATUS}</th>
	<td>{$YoyakuCondition}</td>
</tr>
</table>
<br />

{if $showFeePayLimit}
<div class="meisai-box" style="width:600px;">
<strong>{$smarty.const.OR_ATTENTION}</strong><br />
{$showFeePayLimit}に、ご利用料金をお支払いください。
</div>
<br />
{/if}

{if $smarty.const._SHOW_PRINT_BUTTON_}
<div align="center">
<a href="index.php?op=print" target="_blank"><img src="image/button_print_page.gif" alt="印刷ページへ" width="200" height="39" border="0"></a>
</div>
{/if}

<div align="right" style="margin-right:10px;">
{$smarty.const.OR_CLICK_HERE_TO_PRESET}<a href="index.php?op=apply&preset=1"><img src="image/touroku.gif" alt="登録" width="70" height="39" border="0"></a>
</div>

</div>

{include file="footer.tpl"}
