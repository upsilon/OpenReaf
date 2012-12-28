{include file='header.tpl'}
<!-- templates fcl_04_08.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>利用単位情報（利用単位一覧）</u></strong>
</div>

<h2 class="subtitle01">利用単位情報（利用単位一覧）</h2>

<input type="button" name="backBtn" value="処理選択へ戻る" onclick="location.href='index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';" class="btn-01">

<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</td>
	<th width="50">室場名</th>
	<td width="160">{$rec.shitsujyoname}</td>
	<th width="70">適用開始日</th>
	<td width="70">{$rec.appdatefrom}</td>
</tr>
</table>
<br />
{if $mode == 'mod'}
<input type="button" name="entryBtn" value="利用単位の追加" class="btn-01" onclick="location.href='index.php?op=fcl_05_06_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';"{if $stj_using} disabled{/if}>
{/if}
<br />
<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th width="120">名称</th>
	<th width="80">適用開始日</th>
	<th width="80">廃止日</th>
	<th width="80">操作</th>
	{if $user_view.fcl != "FORBIDDEN" && $mode == 'mod'}
	<th width="40">削除</th>
	{/if}
</tr>
{foreach $res as $val}
<tr align="center">
	<td>{$val.mencode}</td>
	<td align="left" nowrap>{$val.menname}</td>
	<td>{$val.appdatefrom}</td>
	<td>{$val.menhaishidate}</td>
	<td nowrap>
	{if $mode == 'ref'}
	<a title="照会" href="index.php?op=fcl_05_06_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&mcd={$val.mencode}">照会</a>
	{else}
	{if $val.Haishi}<span style="color:gray">変更</span>
	{else}
	<a title="変更" href="index.php?op=fcl_05_06_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&mcd={$val.mencode}">変更</a>
	{/if}|
	<a title="廃止" href="index.php?op=fcl_05_07_01_abo&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&mcd={$val.mencode}">廃止</a>
	{/if}
	</td>
	{if $user_view.fcl != "FORBIDDEN" && $mode == 'mod'}
	<td>
	<a title="削除" href="index.php?op=fcl_05_07_02_del&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&mcd={$val.mencode}">削除</A>
	</td>
	{/if}
</tr>
{/foreach}
</table>

{include file='footer.tpl'}
