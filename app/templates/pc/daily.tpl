{include file="header.tpl"}
{include file="side_menu.tpl"}

<!--daily.tpl-->
<div id="right">
<!--right-->

<form name="selectTime" action="{$NextUrl}" method="post">

<p>{$smarty.const.OR_NOTICE}{if $notice}<br>{$notice}{/if}</p>

<table width="570">
<tr>
	<td width="17%"><a href="#" class="week-back" onclick = "moveDayWeek('{$NaviDate[0]}')">{$smarty.const.OR_PREVIOUS_WEEK}</a></td>
	<td width="15%"><a href="#" class="day-back" onclick = "moveDayWeek('{$NaviDate[1]}')">{$smarty.const.OR_PREVIOUS_DAY}</a></td>
	<th align="center">{$UseDateDisp}</th>    
	<td width="15%" align="right"><a href="#" class="day-next" onclick = "moveDayWeek('{$NaviDate[2]}')">{$smarty.const.OR_NEXT_DAY}</a></td>
	<td width="17%" align="right"><a href="#" class="week-next" onclick = "moveDayWeek('{$NaviDate[3]}')">{$smarty.const.OR_NEXT_WEEK}</a></td>
</tr>
</table>

<table width="570" class="table-style">
{foreach $tData as $tr}
<tr>
	{foreach $tr as $tt}
	<th class="bg-gray-2" height="15" width="{math equation='x/y' x=100 y=$htmlTdCount}%">
	<strong>{$tt.line1|default:'&nbsp;'}</strong>
	</th>
	{/foreach}
</tr>
<tr>
	{foreach $tr as $td}
		<td align="center" height="46" width="{math equation='x/y' x=100 y=$htmlTdCount}%">
		{if $td.line1}
			{if $htmlDisplay}
				{if $td.mark[0] == 10 || $td.mark[0] == 11}
					<strong class="f-sizeup">
					<a href="#" onclick="doClickKoma({math equation='x-y' x=$td.KomaKbn y=$offset},'{$td.mark[2]}',{$multi});"><span id="time{math equation='x-y' x=$td.KomaKbn y=$offset}">{$td.mark[2]}</span></a></strong>
				{else}
					<strong class="f-sizeup">{$td.mark[2]}</strong>
				{/if}
			{else}
				<strong class="f-sizeup">{$td.mark[2]}</strong>
			{/if}
		{else}
			&nbsp;
		{/if}
		</td>
	{/foreach}
</tr>
{/foreach}
</table>

{include file="daily_ex.tpl"}

{foreach $recs as $key => $val}
<input type="hidden" name="hiddenMark[{$key}]" id="hiddenMark{$key}" value="{$val.mark[2]}">
<input type="hidden" name="chkClicktime[{$key}]" id="chkClicktime{$key}" value="0">
<input type="hidden" name="hdntimefrom[{$key}]" id="hdntimefrom{$key}" value="{$val.UseTimeFrom}">
<input type="hidden" name="hdntimeto[{$key}]" id="hdntimeto{$key}" value="{$val.UseTimeTo}">
<input type="hidden" name="hdntime[{$key}]" id="hdntime{$key}">
<input type="hidden" name="hdnnametime[{$key}]" id="hdnnametime{$key}" value="">
<input type="hidden" name="KomaKbn[{$key}]" value="{$val.KomaKbn}">
{/foreach}

<input type="hidden" name="Komasu" id="Komasu" value="{$Komasu}">
<input type="hidden" name="timeFrom" id="timeFrom" value="">
<input type="hidden" name="timeTo" id="timeTo" value="">
<input type="hidden" name="UseDate" value="{$UseDate}">
<input type="hidden" name="page_no" value="">
<input type="hidden" name="op" value="">

<!--{$button}-->
{if $showflag}
	<p align="right">
	<input name="yoyakuSubmit" type="image" src="image/yoyaku-button.gif" value="予約へ" onclick="return doCheckKoma({$tData[0][0].KomaKbn}, {$multi});">
	</p>
{/if}
</form>

<!--/right-->
</div>
<br clear="left" />

<!-- sentaku-area end -->
</div>
{include file="footer.tpl"}
