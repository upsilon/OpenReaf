{include file='header.tpl'}
<!-- templates rsv_04_01.tpl -->

<body>
<div id="contents">

<h2 id="title">予約管理</h2>
<h3 class="subtitle01">予約内容詳細</h3>

<table class="itemtable02" width="95%">
<tr>
	<th width="15%">予約番号</th>
	<td>{$d.yoyakunum}</td>
	<th width="15%">予約区分</th>
	<td>{$d.YoyakuKbnName|default:'&nbsp;'}</td>
</tr>
<tr>
	<th>利用者ID</th>
	<td colspan="3">{$d.userid}</td>
</tr>
<tr>
	<th>利用者名</th>
	<td colspan="3">{$d.namesei}　{$d.nameseikana}
{if $d.userid === $smarty.const._UNREGISTED_USER_ID_}
		<br>
		<table class="none-table">
		<tr>
			<th>氏名</th><td>{$d.unreg_name}</td>
		</tr>
		<tr>
			<th>住所</th><td>{$d.unreg_address}</td>
		</tr>
		<tr>
			<th>電話番号</th><td>{$d.unreg_tel}</td>
		</tr>
		<tr>
			<th>連絡先</th><td>{$d.unreg_contact}</td>
		</tr>
		</table>
{/if}
	</td>
</tr>
<tr>
	<th>利用日時</th>
	<td colspan="3">{$d.UseDateView}　{$d.UseTime}</td>
</tr>
<tr>
	<th>利用施設</th>
	<td colspan="3">{$d.ShisetsuName}&nbsp;{$d.shitsujyoname}</td>
</tr>
<tr>
	<th>利用目的</th>
	<td colspan="3">{$d.MokutekiName}</td>
</tr>
<tr>
	<th>催事名</th>
	<td colspan="3">{$d.yoyakuname}</td>
</tr>
<tr>
	<th>予約状態</th>
	<td colspan="3">{$d.HonYoyakuKbnName}</td>
</tr>
<tr>
	<th>収納状態</th>
	<td colspan="3">{$d.PayKbnName}</td>
</tr>
</table>

<div class="bt-area" align="center">
<input type="button" value="閉じる" onClick="window.close();">
</div>

</div>
</body>
</html>
