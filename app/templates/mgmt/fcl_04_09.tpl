{include file='header.tpl'}
<!-- templates fcl_04_09.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>利用単位組合せ</u></strong></p>
</div>

<h2 class="subtitle01">利用単位組合せ</h2>

<form name="back" method="post" action="index.php">
<input type="hidden" name="op" value="fcl_03_02_menu">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="submit" name="backBtn"  value="処理選択へ戻る" >
</form>
<br>
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
<br>
{if $req.type != 'ref'}
<input type="button" name="entryBtn" value="組合せ登録" class="btn-01" onclick="location.href='index.php?op=fcl_05_08_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';">
<br />
<strong>予約受付可能な利用単位の組合せを指定してください。<br>１利用単位単独でも予約を受け付ける場合には、１利用単位のみで登録します。</strong>
<br />
{/if}
<br>
<table class="itemtable02">
<tr>
	<th width="80">組合せ番号</th>
	<th width="60">表示順</th>
	<th width="90">インターネット</th>
	<th width="180">組合せ利用単位</th>
	<th width="120">組合せ名</th>
	<th width="80">操作</th>
</tr>
{foreach $res as $val}
<tr align="center">
	<td>{$val.combino}</td>
	<td>{$val.combiskbno}</td>
	<td>{if $val.openflg == 1}公開{else}非公開{/if}</td>
	<td align="left">{$val.menname}</td>
	<td>{$val.combiname}</td>
	<td>
	{if $req.type == 'ref'}
	<a href="index.php?op=fcl_05_09_01_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$val.combino}">照会</a>
	{else}
	<a href="index.php?op=fcl_05_09_01_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$val.combino}">変更</a>&nbsp;|&nbsp;<a href="index.php?op=fcl_05_09_02_del&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$val.combino}">削除</a>
	{/if}
	</td>
</tr>
{/foreach}
</table>

{include file='footer.tpl'}
