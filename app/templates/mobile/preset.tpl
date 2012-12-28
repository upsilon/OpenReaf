{include file="header.tpl"}
<!--preset.tpl-->

{if $presetCount > 0}
<ol>
{strip}
{foreach $recs as $value}
	<li style="width:100%;margin:2px 0;padding:0;border-bottom:1px solid #cccccc;">
	{if $value.WebOpen}
		{$value.ShisetsuName}<br>{$value.ShitsujyoName}
		{if $value.MenName}
			&nbsp;{$value.MenName}
		{/if}
		<br>
		{foreach $YearMonths as $YM => $val}
		<a href='index.php?op=monthly&ShisetsuCode={$value.shisetsucode}&ShisetsuClassCode={$value.ShisetsuClassCode}&ShitsujyoCode={$value.shitsujyocode}&CombiNo={$value.combino}&UseYM={$YM}'>{$val.Label}</a>&nbsp;
		{/foreach}
		<br>
	{else}
		<del>{$value.ShisetsuName}<br>{$value.ShitsujyoName}
		{if $value.MenName}
			&nbsp;{$value.MenName}
		{/if}
		</del>&nbsp;{$smarty.const.OR_OUT_OF_SERVICE}<br>
	{/if}
	</li>
{/foreach}
{/strip}
</ol>
{/if}

{include file="footer.tpl"}
