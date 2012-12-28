{include file="header.tpl"}
<!--shisetsu.tpl-->

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#c8cf8d;background:#c8cf8d;border:1px solid #c8cf8d;" />
{foreach $recs as $val}
	{strip}
	<span style="color:#da6803;">{if $val@last}┗{else}┠{/if}</span>
	<a href='index.php?op=shitsujyo&ShisetsuClassCode={$val.shisetsuclasscode}&ShisetsuCode={$val.shisetsucode}'>{$val.shisetsuname}</a>
	<br />
	{/strip}
{/foreach}
{include file="footer.tpl"}
