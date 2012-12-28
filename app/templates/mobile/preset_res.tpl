{include file="header.tpl"}
<!--preset_res.tpl-->

<h3 style="font-size:x-small;background:#f8fbdb;text-align:center;padding:1px;margin:5px 0;border:1px solid #c8cf8d;color:#be5500;">{$smarty.const.OR_REQUESTED_FACILITY}</h3>
&nbsp;{$info.ShisetsuName}<br />
&nbsp;{$info.ShitsujyoName}{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
<br /><br />
{include file="footer.tpl"}
