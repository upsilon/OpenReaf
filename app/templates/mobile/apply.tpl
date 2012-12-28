{include file="header.tpl"}
<!--apply.tpl-->

<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_RECEIPT_NUMBER}</h3>
&nbsp;{$YoyakuNum}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_DATE_AND_TIME}</h3>
&nbsp;{$info.UseDateDisp}&nbsp;{$info.UseTime}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_REQUESTED_FACILITY}</h3>
&nbsp;{$info.ShisetsuName}<br />
&nbsp;{$info.ShitsujyoName}{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
<br />
{if $info.Fuzoku}
{foreach $info.Fuzoku as $value}
&nbsp;{$value}<br />
{/foreach}
{/if}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_USAGE_FEE}</h3>
&nbsp;{$Fee}円
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_STATUS}</h3>
&nbsp;{$YoyakuCondition}
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />
<img src="image/mobile/i-star.gif" alt="よく使う施設への登録" width="16" height="16"><a href='index.php?op=apply&preset=1'>{$smarty.const.OR_PRESET}</a><br>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />
{if $showFeePayLimit}
<font color='#FF0000'>{$smarty.const.OR_ATTENTION}<br>
{$showFeePayLimit}に、<br />
ご利用料金をお支払いください。
</font>
{/if}
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />
<img src="image/mobile/i-text.gif" alt="繰り返し予約" width="16" height="16"><a href="{$REPEAT_LINK}">繰り返し予約</a><br>
{include file="footer.tpl"}
