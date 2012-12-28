{include file='header.tpl'}
<!-- templates fcl_05_05.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_07_list&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">料金情報</a> &gt;
<strong><u>料金設定期間一覧</u></strong>
</div>

<h2 class="subtitle01">料金設定期間一覧</h2>

<div class="margin-box">
<input type="button" value="室場・利用単位組合せ一覧へ戻る" onclick="location.href='index.php?op=fcl_04_07_list&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';" class="btn-01">

<table width="380" class="itemtable02">
<tr>
	<th width="100">施設名</th>
	<td>{$rec.shisetsuname}</td>
</tr>
<tr>
	<th>室場名</th>
	<td>{$rec.shitsujyoname}</td>
</tr>
{if $rec.MenName}
<tr>
	<th>組合せ名称</th>
	<td>{$rec.MenName}</td>
</tr>
{/if}
</table>
<p>
期間毎に料金情報を{if $mode == 'mod'}設定{else}照会{/if}します。
</p>
{if $mode == 'mod'}
<input type="button" name="entryBtn" value="料金情報の追加" class="btn-01" onclick="location.href='index.php?op=fcl_06_01_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&new=1';">
{/if}

<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th width="80">適用開始日</th>
	<th width="80">廃止日</th>
	<th width="100">料金設定期間</th>
	<th width="70">操作</th>
	{if $mode == 'mod'}
	<th width="40">削除</th>
	{/if}
</tr>
{foreach $res as $val}
<tr align="center">
	<td>{$val@iteration}</td>
	<td>{$val.appdatefrom}</td>
	<td>{$val.haishidate}</td>
	<td>{$val.monthdayfrom}〜{$val.monthdayto}</td>
{if $mode == 'mod'}
	<td>
	{if $val.haishidate && $val.haishidate <= $nowDate}
	<span style="color:gray">変更</span>
	{else}
	<a title="変更"href="index.php?op=fcl_06_02_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">変更</a>
	{/if}|
	<a title="廃止"href="index.php?op=fcl_06_03_01_abo&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&tcd=1&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">廃止</a>
	</td>
	<td>
	<a title="削除"href="index.php?op=fcl_06_03_02_del&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&tcd=1&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">削除</a>
	</td>
{else}
	<td>
	<a title="照会"href="index.php?op=fcl_06_02_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">照会</a>
	</td>
{/if}
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
