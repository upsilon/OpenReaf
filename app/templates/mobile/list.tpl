{include file="header.tpl"}
<!--list.html-->

{if $pages}
<div style="background:#f2ea8f;padding:2px;border:1px solid #d9ce51;">
	{foreach $pages as $datum}
		{if $datum@iteration > 1}/{/if}
		{if $datum.set == 1}<blink>{$datum.pagenum}</blink>
		{else}<a href="{$datum.url}">{$datum.pagenum}</a>{/if}
	{/foreach}
</div>
<div style="text-align:center;background:#eeeeee;border:1px solid #bbbbbb;margin:5px 0;">
{if $prevpage}<a href="{$prevpage}">&lt;{$smarty.const.OR_PREVIOUS_PAGE}</a>{/if}
&nbsp;
{if $nextpage}<a href="{$nextpage}">{$smarty.const.OR_NEXT_PAGE}&gt;</a>{/if}
</div>
{/if}

{foreach $entries as $datum}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:center; padding: 1px;border:1px solid #c8cf8d;">{$datum.order_num}&nbsp;{$total_num}</h3>

<div style="padding:5px 0;">
	<span style="color:#cde310;">■</span>{$datum.yoyakunum}&nbsp;{$datum.StatusName}<br />
	<span style="color:#cde310;">■</span>{$datum.UseDateView}&nbsp;{$datum.UseTimeView}<br />
	<span style="color:#cde310;">■</span>{$datum.ShisetsuName}
	{foreach $datum.useShowName as $value}
	<br />　{$value}
	{/foreach}
</div>
{/foreach}

{if $pages}
<div style="text-align:center;background:#eeeeee;border:1px solid #bbbbbb;margin:5px 0;">
{if $prevpage}<a href="{$prevpage}">&lt;{$smarty.const.OR_PREVIOUS_PAGE}</a>{/if}
&nbsp;
{if $nextpage}<a href="{$nextpage}">{$smarty.const.OR_NEXT_PAGE}&gt;</a>{/if}
</div>
{/if}
{include file="footer.tpl"}
