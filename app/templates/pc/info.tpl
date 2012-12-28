{include file="header.tpl"}

<div id="info-area">
{foreach $recs as $val}
<h3>{$val.title}</h3>
<p>
{$val.memo|nl2br}
{if $val.url}
<br /><a href="{$val.url}" target="_blank">{$val.url}</a>
{/if}
<br /><br /><font size="2">({if $val.shisetsucode != '000'}{$val.shisetsuname}&nbsp;{/if}{$val.UpdDate}&nbsp;{$smarty.const.OR_UPDATE})</font>
</p>
{/foreach}
</div>

{include file="footer.tpl"}
