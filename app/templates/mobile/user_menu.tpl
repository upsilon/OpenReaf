{include file="header.tpl"}
<!--user_menu.tpl-->

{if $notice}
<div style="margin:5px 0 10px;color:#ff0000;">
☆{$smarty.const.OR_INFORMATION_FROM_SYSTEM}<br>
{$notice}
</div>
{/if}
<ul>
<li><a href="index.php?op={$ShisetsuSentaku}" accesskey="1">{$smarty.const.OR_RESERVATION_AND_LOTTARY_APPLICATION}[1]</a></li>
<li><a href="index.php?op=preset" accesskey="2">{$smarty.const.OR_FAVORITE_FACILITIES}[2]</a></li>
<li><a href="index.php?op=list" accesskey="3">{$smarty.const.OR_CONFIRMATION_AND_CANCELLATION}[3]</a></li>
<li><a href="index.php?op=history" accesskey="4">{$smarty.const.OR_REQUEST_HISTORY}[4]</a></li>
</ul>
<br />

{include file="footer.tpl"}
