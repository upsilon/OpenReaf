{include file="header.tpl"}
<!--mokuteki.tpl-->

<ol>
{foreach $recs as $val}
<li><a href='index.php?op=apply_conf&MokutekiCode={$val.mokutekicode}'>{$val.mokutekiname}</a></li>
{/foreach}
</ol>

{include file="footer.tpl"}
