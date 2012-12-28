{include file="header.tpl"}
<!--monthly.tpl-->

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#c8cf8d;background:#c8cf8d;border:1px solid #c8cf8d;" />
{if $YearMonths[0].Disable}
	&lt;&lt;{$YearMonths[0].Label}
{else}
	<a href='index.php?op=monthly&UseYM={$YearMonths[0].Date}'>&lt;&lt;{$YearMonths[0].Label}</a>
{/if}
{if $YearMonths[1].Disable}
	&lt;{$YearMonths[1].Label}
{else}
	<a href='index.php?op=monthly&UseYM={$YearMonths[1].Date}'>&lt;{$YearMonths[1].Label}</a>
{/if}
[{$strYM}]
<a href='index.php?op=monthly&UseYM={$YearMonths[2].Date}'>{$YearMonths[2].Label}&gt;</a>
<a href='index.php?op=monthly&UseYM={$YearMonths[3].Date}'>{$YearMonths[3].Label}&gt;&gt;</a>
{strip}
{foreach $recs as $val}
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#c8cf8d;background:#c8cf8d;border:1px solid #c8cf8d;" />
    {foreach $val as $k}
      {if $k.day != ''}
	{if $k.wday == 6}
		<font color='blue'>
	{elseif $k.wday == 0}
		<font color='red'>
	{/if}
	<span>┠</span>{$k.month}/{$k.day}({$aWeek[$k.wday]})
	{if $k.wday == 0 || $k.wday == 6}</font>{/if}
	&nbsp;
	{if $k.open[0] == 6 || $k.open[0] == 9 || $k.open[0] == 10 || $k.open[0] == 11 || $k.open[0] == 12}
		<a href="index.php?op=daily&UseDate={$k.open[2]}">{$k.open[1]}</a>
	{elseif $k.open[0] == 17}
		<font color='red'>{$k.open[1]}</font>
	{else}
		{$k.open[1]}
	{/if}
	<br />
      {/if}
    {/foreach}
{/foreach}
{/strip}

{include file="footer.tpl"}
