{include file="header.tpl"}
<!--apply_conf.tpl-->

<form method ='post' action='index.php'>
<input type="hidden" name="op" value="apply">
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_DATE_AND_TIME}</h3>
&nbsp;{$UseDateDisp}&nbsp;{$UseTime}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_REQUESTED_FACILITY}</h3>
&nbsp;{$ShisetsuName}<br>
&nbsp;{$ShitsujyoName}{if $CombiNo != 0}&nbsp;{$CombiName}{/if}
<br>
{if $FuzokuName}
{foreach $FuzokuName as $value}
&nbsp;{$value}<br>
{/foreach}
{/if}
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_USAGE_FEE}</h3>
&nbsp;{$Fee}円
<h3 style="font-size:x-small;background:#f8fbdb; text-align:left; padding:1px 1px 1px 8px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_NUMBER_OF_PEOPLE}</h3>
{if $showDanjyoNinzuFlg}
<input type="hidden" name="useninzu" value="0">
<span style="color:#cde310;">■</span>{$aNinzu.ninzu1[0]}&nbsp;<input type="text" name="ninzu1" value="" size="6" maxlength="6" istyle=4 format="*N" mode=numeric><br>
<span style="color:#cde310;">■</span>{$aNinzu.ninzu2[0]}&nbsp;<input type="text" name="ninzu2" value="" size="6" maxlength="6" istyle=4 format="*N" mode=numeric>
{else}
<span style="color:#cde310;">■</span>&nbsp;<input type="text" name="useninzu" value="" size="6" maxlength="11" istyle=4 format="*N" mode=numeric>
<input type="hidden" name="ninzu1" value="0">
<input type="hidden" name="ninzu2" value="0">
{/if}
{if $notice}<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />{$notice}{/if}
<div style="width:100%;margin:2px 0;text-align:center;">
<input type="submit" name="apply" value="{$smarty.const.OR_APPLY}">
</div>
</form>
{include file="footer.tpl"}
