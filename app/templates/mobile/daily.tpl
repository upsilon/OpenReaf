{include file="header.tpl"}
<!--daily.tpl-->

<h3 style="font-size:x-small;text-align:center;color:#ff6600;padding:1px;">{$UseDateDisp}</h3>
<div style="text-align:center;background:#eeeeee;border:1px solid #bbbbbb;padding:1px 0;">
<table width="100%" style="font-size:x-small;">
<tr>
	<td align="left" width="28%"><a href="index.php?op=daily&UseDate={$NaviDate[0]}">&lt;&lt;{$smarty.const.OR_PREVIOUS_WEEK}</a></td>
	<td align="left" width="22%"><a href="index.php?op=daily&UseDate={$NaviDate[1]}">&lt;{$smarty.const.OR_PREVIOUS_DAY}</a></td>
	<td align="right" width="22%"><a href="index.php?op=daily&UseDate={$NaviDate[2]}">{$smarty.const.OR_NEXT_DAY}&gt;</a></td>
	<td align="right" width="28%"><a href="index.php?op=daily&UseDate={$NaviDate[3]}">{$smarty.const.OR_NEXT_WEEK}&gt;&gt;</a></td>
</tr>
</table>
</div>

<form method="post" action="{$NextUrl}">
<input type="hidden" name="op" value="fuzoku">
<table>
{foreach $tData as $key => $datum}
<tr>
	<td>
	{if ($datum.mark[0] == 10 || $datum.mark[0] == 11) && $htmlDisplay}
	<input type="checkbox" name="chkClicktime[{$key}]" value="1">
	{else}
	&nbsp;
	{/if}
	</td>
	<td>{$datum.mark[2]}</td>
	<td>{$datum.line1}</td>
</tr>
{/foreach}
</table>
<font color="#FF0000">{$smarty.const.OR_NOTICE}</font>
{if $notice}<br>{$notice}{/if}<br>

{foreach $recs as $key => $val}
<input type="hidden" name="UseTime[{$key}]" value="{$val.UseTimeFrom}_{$val.UseTimeTo}">
<input type="hidden" name="KomaKbn[{$key}]" value="{$val.KomaKbn}">
{/foreach}
<input type="hidden" name="multi" value="{$multi}">
<input type="hidden" name="offset" value="{$offset}">

{if $showflag}
<div style="width:100%;margin:2px 0;text-align:center;">
	<input type="submit" name="yoyakuSubmit" value="{$smarty.const.OR_RESERVE}">
</div>
{/if}
</form>
{include file="footer.tpl"}
