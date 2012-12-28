{include file='header.tpl'}

<div style="padding:5px 0;">
<ul>
<li><a href="index.php?op={$ShisetsuSentaku}" accesskey="1">{$smarty.const.OR_RESERVATION_AND_LOTTARY_APPLICATION}[1]</a></li>
<li><a href="{$NEXT_URL}?op=user_menu" accesskey="2">{$smarty.const.OR_MYPAGE}[2]</a></li>
<li><a href="index.php?op=info_top" accesskey="3">{$smarty.const.OR_NEWS}[3]</a></li>
<li><a href="index.php?op=help" accesskey="4">{$smarty.const.OR_HELP_FOR_SYMBOLS}[4]</a></li>
</ul>
</div>
{if $smarty.const._FOOTER_MESSAGE_}
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#bbbbbb;background:#bbbbbb;border:1px solid #bbbbbb;" />
<div style="padding:5px 0;">
{$smarty.const.OR_SHORT_NOTICE}<br>
□{$smarty.const._FOOTER_MESSAGE_}
</div>
{/if}

<!--/contents-->
</div>

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#bbbbbb;background:#bbbbbb;border:1px solid #bbbbbb;" />
<div style="padding:5px 0;text-align:center;">
<img src="image/openreaf.gif" alt="オープンリーフ公式サイトへ" width="100" height="27">
</div>

</div>
</body>
</html>
