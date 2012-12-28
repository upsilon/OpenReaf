{include file="header.tpl"}

{include file="page-header.tpl"}
<!--preset_res.tpl-->

<h3 class="subtitle01">{$smarty.const.OR_REQUESTED_FACILITY}</h3>
<p align="left" style="padding: 0 8px;">
{$info.ShisetsuName}<br>
{$info.ShitsujyoName}{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
</p>
{include file="footer.tpl"}
