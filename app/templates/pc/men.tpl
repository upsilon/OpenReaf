{include file="header.tpl"}
{include file="side_menu.tpl"}

<!--men.tpl-->
<div id="right">
<!--right-->

<table class="sentaku-table">
{foreach $recs as $val}
	{if $val@iteration is odd}<tr height="60">{/if}
	<td><a href="#" class="shisetsu-bt" onclick="doClickMen('{$val.combino}', '{$val.combiname}');"><span>{$val.combiname}</span></a></td>
	{if $val@iteration is even}</tr>{/if}
{/foreach}
{if $val@iteration is odd}<td>&nbsp;</td></tr>{/if}
<tr>
	<td>
	{if $page_no != 0}
		<a href="index.php?op=men&page_no={$page_no-1}" class="day-back">{$smarty.const.OR_PREVIOUS_LIST}</a>
	{else}&nbsp;{/if}
	</td>
	<td>
	{if $next_fg == 1}
		<a href="index.php?op=men&page_no={$page_no+1}" class="day-next">{$smarty.const.OR_NEXT_LIST}</a>
	{else}&nbsp;{/if}
	</td>
</tr>
</table>

<!--/right-->
</div>
<br clear="left" />

<!-- sentaku-area end -->
<form name="formq" method="post" action="index.php">
<input type="hidden" name="op" value="">
<input type="hidden" name="ShitsujyoCode" value="{$ShitsujyoCode}">
<input type="hidden" name="ShitsujyoName" value="{$ShitsujyoName}">
<input type="hidden" name="CombiNo" value="">
<input type="hidden" name="CombiName" value="">
</form>
</div>

{include file="footer.tpl"}
