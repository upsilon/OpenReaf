{include file="header.tpl"}

{include file="page-header.tpl"}
<!--shisetsu.tpl-->

<ul class="list01">
{foreach $recs as $val}
	<li>
	<a href='index.php?op=shitsujyo&ShisetsuClassCode={$val.shisetsuclasscode}&ShisetsuCode={$val.shisetsucode}'>{$val.shisetsuname}</a>
	</li>
{/foreach}
</ul>

{include file="footer.tpl"}
