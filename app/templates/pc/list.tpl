{include file="header.tpl"}
<!--list.tpl-->

<div align="center">

{if $entries}
{if $pages}
<div class="prevpage">
	{if $prevpage}
		<a href="{$prevpage}" class="day-back">{$smarty.const.OR_PREVIOUS_PAGE}</a>&nbsp;
	{/if}
	{foreach $pages as $datum}
		{if $datum.set == 1}<span>{$datum.pagenum}</span>
		{else}<a href="{$datum.url}" class="pv">{$datum.pagenum}</a>{/if}
		&nbsp;
	{/foreach}
	{if $nextpage}
		<a href="{$nextpage}" class="day-next">{$smarty.const.OR_NEXT_PAGE}</a>
	{/if}
</div>
{/if}
<table class="table-style" width="720">
<tr class="bg-gray-1">
	<th width="64"><a href="#" class="sort-mark" onclick="doClickSort('stt_asc');">▼&nbsp;</a>{$smarty.const.OR_STATUS}<a href="#" class="sort-mark" onclick="doClickSort('stt_dsc');">&nbsp;▲</a></th>
	<th width="90"><a href="#" class="sort-mark" onclick="doClickSort('num_asc');">▼&nbsp;</a>{$smarty.const.OR_RECEIPT_NUMBER}<a href="#" class="sort-mark" onclick="doClickSort('num_dsc');">&nbsp;▲</a></th>
	<th width="160"><a href="#" class="sort-mark" onclick="doClickSort('day_asc');">▼&nbsp;</a>{$smarty.const.OR_DATE_AND_TIME}<a href="#" class="sort-mark" onclick="doClickSort('day_dsc');">&nbsp;▲</a></th>
	<th><a href="#" class="sort-mark" onclick="doClickSort('fac_asc');">▼&nbsp;</a>{$smarty.const.OR_REQUESTED_FACILITY}<a href="#" class="sort-mark" onclick="doClickSort('fac_dsc');">&nbsp;▲</a></th>
</tr>
{foreach $entries as $datum}
<tr>
	<td>{$datum.StatusName}</td>
	<td>{$datum.yoyakunum}</td>
	<td nowrap>{$datum.UseDateView}<br>{$datum.UseTimeView}</td>
	<td>{$datum.ShisetsuName}
	{foreach $datum.useShowName as $value}
	<br>{$value}
	{/foreach}
	</td>
</tr>
{/foreach}
</table>
{if $fixflg}
<div id="message-area">
<font color="red">{$smarty.const.OR_NOTICE_FOR_LOTTARY}</font>
</div>
{/if}
{if $pages}
<div class="prevpage">
	{if $prevpage}
		<a href="{$prevpage}" class="day-back">{$smarty.const.OR_PREVIOUS_PAGE}</a>&nbsp;
	{/if}
	{foreach $pages as $datum}
		{if $datum.set == 1}<span>{$datum.pagenum}</span>
		{else}<a href="{$datum.url}" class="pv">{$datum.pagenum}</a>{/if}
		&nbsp;
	{/foreach}
	{if $nextpage}
		<a href="{$nextpage}" class="day-next">{$smarty.const.OR_NEXT_PAGE}</a>
	{/if}
</div>
{/if}
<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="sort" value="{$sort}">
</form>
{/if}{* for entries parameter *}

</div>

{include file="footer.tpl"}
