{include file="header.tpl"}

{include file="page-header.tpl"}
<!--shitsujyo.tpl-->

<h3 class="subtitle01">{$ShisetsuName}</h3>

{strip}
<ul class="list01">
{foreach $recs as $val}
	<li>
	{if $val.WebOpen == 1}
		<a href='index.php?op=monthly&ShitsujyoCode={$val.shitsujyocode}&CombiNo={$val.combino}'>{$val.shitsujyoname}{if $val.combino != 0}&nbsp;{$val.combiname}{/if}</a>
	{else}
		<span>{$val.shitsujyoname}{if $val.combino != 0}&nbsp;{$val.combiname}{/if}&nbsp;{$smarty.const.OR_OUT_OF_SERVICE}</span>
	{/if}
	</li>
{/foreach}
</ul>
{/strip}

{include file="footer.tpl"}
