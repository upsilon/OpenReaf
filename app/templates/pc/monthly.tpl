{include file="header.tpl"}
{include file="side_menu.tpl"}

<!--monthly.tpl-->
<div id="right">

<table width="570">
<tr>
	<td width="90">
	{if $YearMonths[0].Disable}
	<span class="week-back">{$YearMonths[0].Label}</span>
	{else}
	<a href='index.php?op=monthly&UseYM={$YearMonths[0].Date}' class="week-back">{$YearMonths[0].Label}</a>
	{/if}
	</td>
	<td width="90">
	{if $YearMonths[1].Disable}
	<span class="day-back">{$YearMonths[1].Label}</span>
	{else}
	<a href='index.php?op=monthly&UseYM={$YearMonths[1].Date}' class="day-back">{$YearMonths[1].Label}</a>
	{/if}
	</td>
	<th class="month-cal-head">{$strYM}</th>
	<td width="90" align="right">
	<a href='index.php?op=monthly&UseYM={$YearMonths[2].Date}' class="day-next">{$YearMonths[2].Label}</a>
	</td>
	<td width="90" align="right">
	<a href='index.php?op=monthly&UseYM={$YearMonths[3].Date}' class="week-next">{$YearMonths[3].Label}</a>
	</td>
</tr>
</table>

<table width="570" class="table-style">
<tr height="15">
{foreach $aWeek as $dow => $day}
	<th width="70" align="center" {if $dow == 0}class="bg-red-1"{elseif $dow == 6}class="bg-blue-1"{else}class="bg-gray-1"{/if}>{$day}</th>
{/foreach}
</tr>
{foreach $recs as $val}
<tr height="15">
    {foreach $val as $k}
	{if $k.day == ''}
	    <td>&nbsp;</td>
	{elseif $k.wday == 6}
	    <td align="center" class="bg-blue-2">{$k.day}</td>
	{elseif $k.wday == 0}
	    <td align="center" class="bg-red-2">{$k.day}</td>
	{else}
	    <td align="center" class="bg-gray-2">{$k.day}</td>
	{/if}
    {/foreach}
</tr>
<tr height="46">
    {foreach $val as $k}
	{if $k.day == ''}
	    <td bgcolor="#ffffff" align="center"><strong><font size="4">
	{elseif $k.wday == 6}
	    <td align="center" class="bg-blue-3"><strong><font color="#0000ff" size="4">
	{elseif $k.wday == 0}
	    <td align="center" class="bg-red-3"><strong><font color="#0000ff" size="4">
	{else}
	    <td align="center" class="bg-gray-3"><strong><font color="#0000ff" size="4">
	{/if}
	{if $k.open[0] == 12}
	    <span class="f-ore"><a href="index.php?op=daily&UseDate={$k.open[2]}">{$k.open[1]}</a></span>
	{elseif $k.open[0] == 6 || $k.open[0] == 9 || $k.open[0] == 10 || $k.open[0] == 11}
	    <a href="index.php?op=daily&UseDate={$k.open[2]}">{$k.open[1]}</a>
	{elseif $k.open[0] == 17}
	    <span class="f-red">休館</span>
	{elseif $k.open[0] == 1 || $k.open[0] == 103 || $k.open[0] == 104}
	    <span class="f-black">{$k.open[1]}</span>
	{elseif $k.open[0] == 105}
	    <span class="f-gre">{$k.open[1]}</span>
	{else}
	    {$k.open[1]}
	{/if}
	</font></strong></td>
    {/foreach}
</tr>
{/foreach}
</table>

{include file="monthly_ex.tpl"}

<!--/right-->
</div>
<br clear="left" />

<!-- sentaku-area end -->
</div>
{include file="footer.tpl"}

