{include file="header.tpl"}

{include file="page-header.tpl"}
<!--shisetsuclass.tpl-->

<ul class="list01">
{foreach $recs as $val}
	<li>
	<a href='index.php?op=shisetsu&ShisetsuClassCode={$val.shisetsuclasscode}'>{$val.shisetsuclassname}</a>
	</li>
{/foreach}
</ul>

{include file="footer.tpl"}
