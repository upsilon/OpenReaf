{include file='header.tpl'}
<!-- templates rsv_01_00.tpl -->
<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
<strong><u>本日分予約</u></strong>
</div>

<h2 class="subtitle01">本日分予約一覧</h2>

<form name="form1" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_01_00_list">
施設名&nbsp;&nbsp;&nbsp;&nbsp;
<select name="pShisetsuCode" onChange="this.form.submit()">
<option value="">全施設</option>
{html_options options=$ShisetsuOptions selected=$ShisetsuSelected}
</select>
</form>
<br>

{if count($TempReserv) < 1}
<b><font color="#FF0000">該当データがありません。</font></b>
{else}
<table width="99%" class="itemtable01">
<tr>
	<th width="8%" rowspan="2">予約番号</th>
	<th width="22%" rowspan="2">施設名<br>室場・利用単位</th>
	<th width="12%" rowspan="2">利用時間</th>
	<th width="34%">利用者ID</th>
	<th width="14%" rowspan="2">利用目的</th>
	<th width="4%" rowspan="2">利用<br>人数</th>
	<th width="6%" rowspan="2">収納<br>状態</th>
</tr>
<tr>
	<th>利用者名</th>
</tr>
{foreach $TempReserv as $val}
<tr>
	<td rowspan="2" align="center">{$val.yoyakunum}</td>
	<td rowspan="2">{$val.ShisetsuName}<br>{$val.shitsujyoname}</td>
	<td rowspan="2" align="center">{$val.UseTimeFromView}〜{$val.UseTimeToView}</td>
	<td>{$val.userid}</td>
	<td rowspan="2">{$val.MokutekiName}</td>
	<td rowspan="2" align="right">
	{if ($val.useninzu > 0)}{$val.useninzu}{else}-{/if}</td>
	<td rowspan="2" align="center">{$val.PayKbnName}</td>
</tr>
<tr>
	<td>{$val.namesei}</td>
</tr>
{/foreach}
</table>
{/if}

{include file='footer.tpl'}
