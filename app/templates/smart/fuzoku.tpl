{include file="header.tpl"}

{include file="page-header.tpl"}
<!--fuzoku.tpl-->

<span class="f-ore">{$smarty.const.OR_NOTICE_OF_OPTIONS}</span>

<form name="selectFuzoku" method="post" action="index.php">
<input type="hidden" name="op" value="mokuteki">

<ul class="item-list">
	{foreach $tData as $key => $val}
	<li>
	{if $val.check == 10 || $val.check == 11}
	<span onclick="doClickFuzoku({$key},'{$val.mark}');" id="span{$key}" class="nochks">{$val.line1}</span><strong class="chgicon" id="fuzoku{$key}">{$val.mark}</strong>
	{else}
	<label>&nbsp;{$val.mark}&nbsp;{$val.line1}</label>
	{/if}
	<input type="hidden" name="FuzokuCode[{$key}]" id="FuzokuCode[$key}]" value="{$val.FuzokuCode}">
	<input type="hidden" name="chkClickfuzoku[{$key}]" id="chkClick{$key}" value="0">
	</li>
	{/foreach}
</ul>

<p>
<input type="submit" name="fuzokuSubmit" value="{$smarty.const.OR_RESERVE}" class="rsv-btn">
<p>

</form>
<br />
{include file="footer.tpl"}
