{include file="header.tpl"}

{include file="page-header.tpl"}
<!--preset.tpl-->

{literal}
<script type="text/javascript">
	jQuery(document).ready(function() { var str = '.acrd-menu'; AcrdIcon(str);});
</script>
{/literal}

{if $presetCount > 0}
<dl class="acrd-menu">
{strip}
{foreach $recs as $value}
	{if $value.WebOpen}
		<dt class="set">{$value.ShisetsuName}<br />{$value.ShitsujyoName}
		{if $value.MenName}&nbsp;{$value.MenName}{/if}
		<span>{$smarty.const.OR_REQUEST_MONTH}</span>
		</dt>
		<dd>
		{foreach $YearMonths as $YM => $val}
		<a href='index.php?op=monthly&ShisetsuCode={$value.shisetsucode}&ShisetsuClassCode={$value.ShisetsuClassCode}&ShitsujyoCode={$value.shitsujyocode}&CombiNo={$value.combino}&UseYM={$YM}'>{$val.Label}</a>
		</a>
		{/foreach}
		</dd>
	{else}
		<dt class="timeout">{$value.ShisetsuName}<br />{$value.ShitsujyoName}
		{if $value.MenName}&nbsp;{$value.MenName}{/if}
		<span>{$smarty.const.OR_OUT_OF_SERVICE}</span>
		</dt>

	{/if}
{/foreach}
{/strip}
</dl>
{/if}

{include file="footer.tpl"}
