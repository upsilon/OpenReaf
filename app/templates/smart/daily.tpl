{include file="header.tpl"}

{include file="page-header.tpl"}

<h3 class="time-title">{$UseDateDisp}</h3>
<div class="move-day">
<table width="100%">
<tr>
	<td width="30%" align="left"><a href="index.php?op=daily&UseDate={$NaviDate[0]}" class="week">&lt;&lt;{$smarty.const.OR_PREVIOUS_WEEK}</a></td>
	<td width="20%" align="left"><a href="index.php?op=daily&UseDate={$NaviDate[1]}" class="day">&lt;{$smarty.const.OR_PREVIOUS_DAY}</a></td>
	<td width="20%" align="right"><a href="index.php?op=daily&UseDate={$NaviDate[2]}" class="day">{$smarty.const.OR_NEXT_DAY}&gt;</a></td>
	<td width="30%" align="right"><a href="index.php?op=daily&UseDate={$NaviDate[3]}" class="week">{$smarty.const.OR_NEXT_WEEK}&gt;&gt;</a></td>
</tr>
</table>
</div>

<form name="selectTime" method="post" action="{$NextUrl}">
<input type="hidden" name="op" value="fuzoku">
<ul class="item-list">
{foreach $tData as $key => $datum}
	<li>
	{if ($datum.mark[0] == 10 || $datum.mark[0] == 11) && $htmlDisplay}
	<span onclick="doClickKoma({$key},'{$datum.mark[2]}');" id="span{$key}" class="">{$datum.line1}</span><strong class="chgicon" id="time{$key}">{$datum.mark[2]}</strong>
	{else}
	<label class="no">&nbsp;{$datum.mark[2]}&nbsp;{$datum.line1}</label>
	{/if}
	</li>
{/foreach}
</ul>
<font color="#FF0000">{$smarty.const.OR_NOTICE}</font>
{if $notice}<br>{$notice}{/if}<br>

{foreach $recs as $key => $val}
<input type="hidden" name="chkClicktime[{$key}]" id="chkClicktime{$key}" value="0">
<input type="hidden" name="UseTime[{$key}]" value="{$val.UseTimeFrom}_{$val.UseTimeTo}">
<input type="hidden" name="KomaKbn[{$key}]" value="{$val.KomaKbn}">
{/foreach}
<input type="hidden" name="multi" value="{$multi}">
<input type="hidden" name="offset" value="{$offset}">

{if $showflag}
	<input type="submit" name="yoyakuSubmit" value="{$smarty.const.OR_RESERVE}" class="rsv-btn">
{/if}
</form>
<br />
{include file="footer.tpl"}
