{include file='header.tpl'}
<!-- templates fcl_04_07.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>料金情報</u></strong>
</div>

<h2 class="subtitle01">料金情報</h2>

<div class="margin-box">
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
<p>
室場もしくは利用単位組合せ毎に料金情報を設定します。
</p>
<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th width="120">名称</th>
	<th width="40">操作</th>
</tr>
{foreach $res as $val}
<tr>
	<td align="center">{$val.combino}</td>
	<td nowrap>{$val.combiname}</td>
	<td align="center"><a title="選択" href="index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$val.combino}">選択</a></td>
</tr>
{foreachelse}
<tr>
	<td align="center">0</td>
	<td nowrap>{$rec.shitsujyoname}</td>
	<td align="center"><a title="選択" href="index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno=0">選択</a></td>
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
