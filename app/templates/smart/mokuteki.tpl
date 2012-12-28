{include file="header.tpl"}

{include file="page-header.tpl"}
<!--mokuteki.tpl-->

<ul class="list01">
{foreach $recs as $val}
	<li>
		<a href='index.php?op=apply_conf&MokutekiCode={$val.mokutekicode}'>{$val.mokutekiname}</a>
	</li>
{/foreach}
</ul>

{include file="footer.tpl"}
