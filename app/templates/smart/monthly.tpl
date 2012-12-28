{include file="header.tpl"}

{include file="page-header.tpl"}
<!--monthly.tpl-->

{*** 表示月変更リンク ***}
<div class="move-day">
<table width="100%">
<tr>
	<td width="17%" align="left">
	{if $YearMonths[0].Disable}
		<span class="week">&lt;&lt;{$YearMonths[0].Label}</span>
	{else}
		<a href='index.php?op=monthly&UseYM={$YearMonths[0].Date}' class="week">&lt;&lt;{$YearMonths[0].Label}</a>
	{/if}
	</td>
	<td width="17%" align="left">
	{if $YearMonths[1].Disable}
		<span class="day">&lt;{$YearMonths[1].Label}</span>
	{else}
		<a href='index.php?op=monthly&UseYM={$YearMonths[1].Date}' class="day">&lt;{$YearMonths[1].Label}</a>
	{/if}
	</td>
	<td class="move-data"><h3>{$strYM}</h3></td>
	<td width="17%" align="right"><a href='index.php?op=monthly&UseYM={$YearMonths[2].Date}' class="day">{$YearMonths[2].Label}&gt;</a></td>
	<td width="17%" align="right"><a href='index.php?op=monthly&UseYM={$YearMonths[3].Date}' class="week">{$YearMonths[3].Label}&gt;&gt;</a></td>
</tr>
</table>
</div>

<table class="calendar">
<tr>
{foreach $aWeek as $n => $day}
	<th{if $n == 0} class="sun"{elseif $n == 6} class="sat"{/if}>{$day}</th>
{/foreach}
</tr>
{strip}
{foreach $recs as $key}
<tr>
    {foreach $key as $k}
	{if $k.day == ''}
		<td><span class="null">&nbsp;<br />&nbsp;</span></td>
		{else}
		<td>		
		{if $k.open[0] == 12}
			<a href="index.php?op=daily&UseDate={$k.open[2]}" class="rsv">{$k.day}<br />{$k.open[1]}</a>
		{elseif $k.open[0] == 6 || $k.open[0] == 9 || $k.open[0] == 10 || $k.open[0] == 11}
			<a href="index.php?op=daily&UseDate={$k.open[2]}">{$k.day}<br />{$k.open[1]}</a>
		{elseif $k.open[0] == 17}
			<span class="rest">{$k.day}<br />休館</span>
		{elseif $k.open[0] == 105}
			<span class="open">{$k.day}<br />{$k.open[1]}</span>
		{else}
			<span>{$k.day}<br />{$k.open[1]}</span>
		{/if}
		</td>
	{/if}
    {/foreach}
</tr>
{/foreach}
{/strip}
</table>

{include file="footer.tpl"}
