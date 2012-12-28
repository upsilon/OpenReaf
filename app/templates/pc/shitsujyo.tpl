{include file="header.tpl"}
{include file="side_menu.tpl"}

<!--shitsujyo.tpl-->
<div id="right">
<table class="sentaku-table">
{foreach $recs as $val}
	{if $val@iteration is odd}<tr>{/if}
	<td>
	{if $val.WebOpen == 1}
	<a href="#" class="shisetsu-bt" onclick="doClickShitsujyo('{$val.shitsujyocode}', '{$val.shitsujyoname}', '{$val.Dest}');"><span>{$val.shitsujyoname}</span></a>
	{else}
	<div class="shisetsu-bt_off"><span>{$val.shitsujyoname}<strong>({$smarty.const.OR_OUT_OF_SERVICE})</strong></span></div>
	{/if}
	</td>
	{if $val@iteration is even}</tr>{/if}
{/foreach}
{if $val@iteration is odd}<td>&nbsp;</td></tr>{/if}
<tr>
	<td>
	{if $page_no != 0}
		<a href="index.php?op=shitsujyo&page_no={$page_no-1}" class="day-back">{$smarty.const.OR_PREVIOUS_LIST}</a>
	{else}&nbsp;{/if}
	</td>
	<td>
	{if $next_fg == 1}
		<a href="index.php?op=shitsujyo&page_no={$page_no+1}" class="day-next">{$smarty.const.OR_NEXT_LIST}</a>
	{else}&nbsp;{/if}
	</td>
</tr>
</table>

<!--/right-->
</div>
<br clear="left" />

<form name="formq" method="post" action="index.php">
<input type="hidden" name="op" value="">
<input type="hidden" name="ShitsujyoCode" value="">
<input type="hidden" name="ShitsujyoName" value="">
<input type="hidden" name="CombiNo" value="0">
<input type="hidden" name="CombiName" value="">
</form>
<div>

{include file="footer.tpl"}
