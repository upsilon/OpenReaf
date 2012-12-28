{include file="header.tpl"}

{include file="side_menu.tpl"}

<!--fuzoku.tpl-->
<div id="right">
<!--right-->


<h3 class="time-title"><strong>{$UseDateDisp}</strong></h3>
<p>{$smarty.const.OR_NOTICE_OF_OPTIONS}</p>

<form name = "selectFozuku" method="post" action="index.php">
<table width="570" class="table-style">
{foreach $tData as $key1 => $tr}
<tr>
	{foreach $tr as $key2 => $tt}
	<th class="bg-gray-2" height="15" width="{math equation='x/y' x=100 y=$htmlTdCount}%">
	<strong>{$tt.line1|default:'&nbsp;'}</strong>
	</th>
	{/foreach}
</tr>
<tr>
	{foreach $tr as $key3 => $td}
	<td align="center" height="46" width="{math equation='x/y' x=100 y=$htmlTdCount}%">
	{if $td.line1}
	{if $td.check == 10|| $td.check == 11}
	<strong class="f-sizeup">
	<a href="#" onclick="doClickFuzoku('fuzoku{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}','{$td.mark}');"><span id="fuzoku{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}">{$td.mark}</span></a>
	</strong>
	<input type="hidden" name="FuzokuCode[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" id="FuzokuCode[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" value="{$td.FuzokuCode}">
	<input type="hidden" name="chkClickfuzoku[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" id="chkClickfuzoku{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}" value="0">
	{else}
	{$td.mark}
	<input type="hidden" name="FuzokuCode[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" id="FuzokuCode[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" value="{$td.FuzokuCode}">
	<input type="hidden" name="chkClickfuzoku[{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}]" id="chkClickfuzoku{math equation='x*y+z' x=$key1 y=$htmlTdCount z=$key3}" value="0">
	{/if}
	{else}
	&nbsp;
	{/if}
	</td>
{/foreach}
</tr>
{/foreach}
</table>

{include file="fuzoku_ex.tpl"}

<div align="right" class="yoyaku-bt">
<input name="fuzokuSubmit" type="image" src="image/yoyaku-button.gif" value="{$smarty.const.OR_RESERVE}" onClick="doCheckFuzoku();">
</div>
<input type="hidden" name="op" value="">
</form>

<!--/right-->
</div>
<br clear="left" />

<!-- sentaku-area end -->
</div>

{include file="footer.tpl"}
