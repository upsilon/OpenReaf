{include file='header.tpl'}
<!-- templates fcl_04_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>予約時間割設定一覧</u></strong>
</div>

<h2 class="subtitle01">予約時間割設定一覧</h2>

<input type="button" name="backBtn" value="処理選択へ戻る" onclick="location.href='index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';" class="btn-01">

<table width="380" class="itemtable02">
<tr>
	<th width="100">施設名</th>
	<td>{$rec.shisetsuname}</td>
</tr>
<tr>
	<th>室場名</th>
	<td>{$rec.shitsujyoname}</td>
</tr>
</table>
<br />
{if $mode == 'mod'}
<input type="button" name="entryBtn" value="予約時間割の追加" class="btn-01" onclick="location.href='index.php?op=fcl_05_01_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';">
{/if}

<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th width="80">適用開始日</th>
	<th width="80">廃止日</th>
	<th width="80">設定期間</th>
	<th width="80">操作</th>
	{if $mode == 'mod'}
	<th width="40">削除</th>
	{/if}
	<td class="no-border">&nbsp;</td>
</tr>
{foreach $res as $val}
<tr>
	<td align="center" nowrap>{$val@iteration}</td>
	<td align="center" nowrap>{$val.appdatefrom}</td>
	<td align="center" nowrap>{$val.haishidate}</td>
	<td align="center" nowrap>{$val.monthdayfrom}〜{$val.monthdayto}</td>

	{if $mode == 'mod'}
	<td align="center" nowrap>
		{if $val.haishidate && $val.haishidate <= $nowDate}
		<span style="color:gray">変更</span>
		{else}
		<a title="変更"href="index.php?op=fcl_05_01_02_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">変更</a>
		{/if}|
		{if $val.haishidate}
		<a title="廃止取消"href="index.php?op=fcl_05_01_03_abo&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}&resume=1">廃止取消</a>
		{else}
		<a title="廃止"href="index.php?op=fcl_05_01_03_abo&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">廃止</a>
		{/if}
	</td>
	<td align="center" nowrap>
		<a title="削除"href="index.php?op=fcl_05_01_04_del&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">削除</a>
	</td>
	{else}
	<td align="center" nowrap>
		<a title="照会"href="index.php?op=fcl_05_01_02_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}">照会</a>
	</td>
	{/if}
	<td nowrap class="no-border">&nbsp;({$val.UpdDate}&nbsp;{$val.UpdTime}&nbsp;{$val.UpdName}&nbsp;更新)</td>
</tr>
{/foreach}
</table>

{include file='footer.tpl'}
