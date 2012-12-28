{include file="header.tpl"}
<!--shitsujyo.tpl-->

<h3 style="font-size:x-small;background:#f8fbdb; text-align:center; padding: 1px;border:1px solid #c8cf8d;">選択内容</h3>
{$ShisetsuName}<br />

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#c8cf8d;background:#c8cf8d;border:1px solid #c8cf8d;" />
{foreach $recs as $val}
	{strip}
	<span style="color:#da6803;">{if $val@last}┗{else}┠{/if}</span>
	{if $val.WebOpen == 1}
		<a href='index.php?op=monthly&ShitsujyoCode={$val.shitsujyocode}&CombiNo={$val.combino}'>{$val.shitsujyoname}{if $val.combino != 0}&nbsp;{$val.combiname}{/if}</a>
	{else}
		<del>{$val.shitsujyoname}{if $val.combino != 0}&nbsp;{$val.combiname}{/if}</del>&nbsp;{$smarty.const.OR_OUT_OF_SERVICE}
	{/if}
	<br />
	{/strip}
{/foreach}
{include file="footer.tpl"}
