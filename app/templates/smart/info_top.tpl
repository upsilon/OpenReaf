{include file='header.tpl'}

{include file="page-header.tpl"}
<!--info_top.tpl-->

<ul class="list01">
{foreach $recs as $val}
<li><a href="index.php?op=info&sCode={$val.shisetsucode}&tDate={$val.tourokudate}&tTime={$val.tourokutime}&seq={$val.seqno}"><strong>{$val.title}</strong>{if $val.shisetsucode != '000'}<br>　({$val.shisetsuname}){/if}</a></li>
{foreachelse}
<li>{$smarty.const.OR_NO_NEWS}</li>
{/foreach}
</ul>

{include file='footer.tpl'}
