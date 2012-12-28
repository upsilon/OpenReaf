{include file='header.tpl'}
<!-- templates rsv_02_02_01.tpl -->

<body id="rsv-body">
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt; <a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt; <strong>空き状況表示</strong>
</div>

<h2 class="subtitle01">空き状況表示</h2>

<div class="itemtop-area">
<input type="button" value="検索へ戻る" onclick="location.href='index.php?op=rsv_01_02_search&back=1';">
</div>

<table class="itemtable02">
<tr height="30">
	<th width="100">指定利用目的</th>
	<td width="410">{$MokutekiName|default:'なし'}</td>
</tr>
</table>

<table>
<tr>
	<td width="10px">&nbsp;</td>
	<td><span class="y-blu">○</span>:空きあり　</td>
	<td><span class="y-ore2">△</span>:一部予約済　</td>
	<td><span class="y-red">×</span>:予約済　</td>
	{*<td><span class="y-pink">休</span>:休館日　</td>*}
	{*<td><span class="y-ore">保</span>:保守・点検　</td>*}
	{*<td><span class="y-blu4">−</span>:予約期間外</td>*}
	<td width="100px">&nbsp;</td>
	<td nowrap><a href="index.php?op=rsv_02_02_status&previous=1"><strong>&lt;&nbsp;前の期間</strong></a></td>
	<td width="20">&nbsp;</td>
	<td nowrap><a href="index.php?op=rsv_02_02_status&forward=1"><strong>次の期間&nbsp;&gt;</strong></a></td>
</tr>
</table>

{* 空き状況表示メインループ *}
{foreach $recs as $c}
<h3 class="subtitle02">{$c.ShisetsuName}</h3>
{foreach $c.shitsujyo as $d}
	{if count($d.combi) > 0}

<table class="itemtable02">
<tr>
	<th width="150" {if !$smarty.const._ROOM_STATUS_ALL_DAY_}rowspan="2"{/if}>
	{$d.shitsujyoname}
	</th>
	<th width="60" {if !$smarty.const._ROOM_STATUS_ALL_DAY_}rowspan="2"{/if}>定員</th>
	{foreach $date_list as $value}

{*** 日付表示部分 ****}
	<td height="25" {if !$smarty.const._ROOM_STATUS_ALL_DAY_}colspan="{$reserve_band_active_count}"{/if} class=
	{if $value.HolidayFlg == '1'}"sun-bg"
	{elseif $value.date|date_format:'%w' == '6'}"sat-bg"
	{elseif $value.date|date_format:'%w' == '0'}"sun-bg"
	{else}"gray"{/if} align="center">
	{if $value.HolidayFlg == '1'}<span class="y-red">
	{elseif $value.date|date_format:'%w' == '6'}<span class="y-blu">
	{elseif $value.date|date_format:'%w' == '0'}<span class="y-red">
	{else}<span>{/if}
	<strong><a href="index.php?op=rsv_03_02_status&scd={$d.shisetsucode}&date={$value.date|date_format:'%Y%m%d'}">{$value.dateView}</a></strong></span>
	</td>
{*** / 日付表示部分 ****}
	{/foreach}
</tr>
{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
<tr align="center">
	{* 午前／午後／夜間、表示部分 *}
	{foreach $date_list as $value}
		{if $reserve_band_active[0]}
			<td width="20" height="25" class=
			{if $value.HolidayFlg == '1'}"sun-bg"
			{elseif $value.date|date_format:'%w' == '6'}"sat-bg"
			{elseif $value.date|date_format:'%w' == '0'}"sun-bg"
			{else}"gray"{/if}
			>午前</td>
		{/if}
		{if $reserve_band_active[1]}
			<td width="20" height="25" class=
			{if $value.HolidayFlg == '1'}"sun-bg"
			{elseif $value.date|date_format:'%w' == '6'}"sat-bg"
			{elseif $value.date|date_format:'%w' == '0'}"sun-bg"
			{else}"gray"{/if}
			>午後</td>
		{/if}
		{if $reserve_band_active[2]}
			<td width="20" height="25" class=
			{if $value.HolidayFlg == '1'}"sun-bg"
			{elseif $value.date|date_format:'%w' == '6'}"sat-bg"
			{elseif $value.date|date_format:'%w' == '0'}"sun-bg"
			{else}"gray"{/if}
			>夜間</td>
		{/if}
	{/foreach}
</tr>
{/if}
{foreach $d.combi as $e}
<tr align="center">
	<td height="25" class="gray">{$e.combiname|default:'全面'}</td>
	<td class="gray">{if $e.teiin=='0'}-{else}{$e.teiin|default:'&nbsp;'}{/if}</td>
	{foreach $e.reserve_band as $key => $f}
	{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
		{foreach $f as $key2 => $g}
			{if $reserve_band_active[$key2]}
				<td>
				{if $g == 0}
					<div class="y-blu">
					<a href="index.php?op=rsv_03_01_status&scd={$e.shisetsucode}&rcd={$e.shitsujyocode}&mcd={$e.mencode}&cno={$e.combino|default:0}&date={$date_list[$key].dateLink}">{$e.mark[$key][$key2]}</a>
					</div>
				{elseif $g == 1}
					<div class="y-red">{$e.mark[$key][$key2]}</div>
				{elseif $g == 2}
					<div class="y-ore2">
					<a href="index.php?op=rsv_03_01_status&scd={$e.shisetsucode}&rcd={$e.shitsujyocode}&mcd={$e.mencode}&cno={$e.combino|default:0}&date={$date_list[$key].dateLink}">{$e.mark[$key][$key2]}</a>
					</div>
				{elseif $g == 3}
					<div class="y-pink">{$e.mark[$key][$key2]}</div>
				{elseif $g == 4}
					<div class="y-blu4">{$e.mark[$key][$key2]}</div>
				{/if}
				</td>
			{/if}
		{/foreach}
	{else}
		<td>
		{if $f.all == 0}
			<div class="y-blu">
			<a href="index.php?op=rsv_03_01_status&scd={$e.shisetsucode}&rcd={$e.shitsujyocode}&mcd={$e.mencode}&cno={$e.combino|default:0}&date={$date_list[$key].dateLink}">{$e.mark[$key].all}</a>
			</div>
		{elseif $f.all == 1}
			<div class="y-red">{$e.mark[$key].all}</div>
		{elseif $f.all == 2}
			<div class="y-ore2">
			<a href="index.php?op=rsv_03_01_status&scd={$e.shisetsucode}&rcd={$e.shitsujyocode}&mcd={$e.mencode}&cno={$e.combino|default:0}&date={$date_list[$key].dateLink}">{$e.mark[$key].all}</a>
			</div>
		{elseif $f.all == 3}
			<div class="y-pink">{$e.mark[$key].all}</div>
		{elseif $f.all == 4}
			<div class="y-blu4">{$e.mark[$key].all}</div>
		{/if}
		</td>
	{/if}
	{/foreach}
</tr>
{/foreach}
</table>
<br>
{/if}
{/foreach}
{/foreach}
{* /空き状況表示メインループ *}
<br />

{include file='footer.tpl'}
