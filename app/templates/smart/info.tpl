{include file="header.tpl"}

{include file="page-header.tpl"}
<!--info.tpl-->

<div class="info">
<h3>{$rec.title}</h3>
<p>
{$rec.memo|nl2br}
{if $rec.url}<br /><a href="{$rec.url}">{$rec.url}</a>{/if}
<br /><br />({if $rec.shisetsucode != '000'}{$rec.shisetsuname}<br>&nbsp;{/if}{$rec.UpdDate}&nbsp;{$smarty.const.OR_UPDATE})
</p>
</div>

{include file="footer.tpl"}
