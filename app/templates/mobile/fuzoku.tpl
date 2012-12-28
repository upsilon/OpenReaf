{include file="header.tpl"}
<!--fuzoku.tpl-->

<font color="#FF0000">{$smarty.const.OR_NOTICE_OF_OPTIONS}</font>
<br />
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />

<form method="post" action="index.php">
<input type="hidden" name="op" value="mokuteki">

<table style="font-size:x-small;">
{foreach $tData as $key => $val}
<tr>
	<td>
	{if $val.check == 10 || $val.check == 11}
	<input type="checkbox" name="chkClickfuzoku[{$key}]" value="1">
	<input type="hidden" name="FuzokuCode[{$key}]" value="{$val.FuzokuCode}">
	{else}
	&nbsp;{$val.mark}&nbsp;
	{/if}</td>
	<td>{$val.line1}</td>
</tr>
{/foreach}
</table>
<div style="width:100%;margin:2px 0;background:#eeeeee;border:1px solid #cccccc;text-align:center;">
<input type="submit" name="fuzokuSubmit" value="{$smarty.const.OR_RESERVE}">
</div>
</form>
{include file="footer.tpl"}
