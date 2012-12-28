{include file="header.tpl"}
<!--info.tpl-->
<br />

<h3 style="font-size:x-small;background:#f8fbdb; text-align:center; padding: 1px;border:1px solid #c8cf8d;">{$rec.title}</h3>

<div style="padding:8px 0;margin:8px 0;">
{$rec.memo|nl2br}
{if $rec.url}<br /><a href="{$rec.url}">{$rec.url}</a>{/if}
<br /><br />({if $rec.shisetsucode != '000'}{$rec.shisetsuname}<br>&nbsp;{/if}{$rec.UpdDate}&nbsp;{$smarty.const.OR_UPDATE})
</div>

{include file="footer.tpl"}
