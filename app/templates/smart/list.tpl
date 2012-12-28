{include file="header.tpl"}

{include file="page-header.tpl"}
<!--list.html-->

{if $pages}
<div class="page-move">
	{foreach $pages as $datum}
		{if $datum@iteration > 1}{/if}
		{if $datum.set == 1}<span>{$datum.pagenum}</span>
		{else}<a href="{$datum.url}">{$datum.pagenum}</a>{/if}
	{/foreach}
<br clear="all" />
</div>
{/if}

{foreach $entries as $datum}
<div class="listbox">
<h3><span>{$datum.order_num}&nbsp;{$total_num}</span>{$datum.yoyakunum}&nbsp;{$datum.StatusName}</h3>

<table>
<tr>
	<th>{$smarty.const.OR_DATE_AND_TIME}</th><td>：{$datum.UseDateView}&nbsp;{$datum.UseTimeView}</td>
</tr>
<tr>
	<th>{$smarty.const.OR_REQUESTED_FACILITY}</th>
	<td>：{$datum.ShisetsuName}
	{foreach $datum.useShowName as $value}
	<br /> 　{$value}
	{/foreach}
	</td>
</tr>
</table>
</div>
{/foreach}

{if $pages}
<div class="page-move">
	{foreach $pages as $datum}
		{if $datum@iteration > 1}{/if}
		{if $datum.set == 1}<span>{$datum.pagenum}</span>
		{else}<a href="{$datum.url}">{$datum.pagenum}</a>{/if}
	{/foreach}
<br clear="all" />
</div>
{/if}

{include file="footer.tpl"}
