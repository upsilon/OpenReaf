{include file='header.tpl'}
<!-- templates fcl_06_02.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_07_list&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">料金情報</a> &gt; 
<a href="index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}">料金設定期間一覧</a> &gt; 
<strong><u>曜日指定一覧</u></strong>
</div>

<h2 class="subtitle01">曜日指定一覧</h2>

<div class="margin-box">
<input type="button" value="料金設定期間一覧へ戻る" onClick="location.href='index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}'"><br><br>

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
<tr>
	<th>適用開始日</td>
	<td>{$res[0].appdatefrom}</td>
</tr>
<tr>
	<th>廃止日</th>
	<td>{$res[0].haishidate}</td>
</tr>
<tr>
	<th>料金設定期間</th>
	<td>{if $res[0].monthdayfrom<>''}{$res[0].monthdayfrom}〜{$res[0].monthdayto}{/if}</td>
</tr>
</table>
<br />

{if $mode == 'mod'}
<input type="button" name="entryBtn" value="料金表の追加" class="btn-01" onClick="location.href='index.php?op=fcl_06_01_01_reg&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&apd={$res[0].appdatefrom}&prfr={$res[0].monthdayfrom}&prto={$res[0].monthdayto}';">
{/if}
<br />
<table class="itemtable02">
<tr>
	<th width="40">番号</th>
	<th>曜日指定</th>
	<th width="40">操作</th>
	<td class="no-border">&nbsp;</td>
</tr>
{foreach $res as $val}
<tr align="center">
	<td>{$val.tourokuno}</td>
	<td nowrap>
	<input id=timeSelected0 enable type=checkbox value="1" name=YoyakuCD0{if $val.sunflg eq '1'} checked{/if} disabled>日
	<input id=timeSelected1 enable type=checkbox value="1" name=YoyakuCD0{if $val.monflg eq '1'} checked{/if} disabled>月
	<input id=timeSelected2 enable type=checkbox value="1" name=YoyakuCD0{if $val.tueflg eq '1'} checked{/if} disabled>火
	<input id=timeSelected3 enable type=checkbox value="1" name=YoyakuCD0{if $val.wedflg eq '1'} checked{/if} disabled>水
	<input id=timeSelected4 enable type=checkbox value="1" name=YoyakuCD0{if $val.thuflg eq '1'} checked{/if} disabled>木
	<input id=timeSelected5 enable type=checkbox value="1" name=YoyakuCD0{if $val.friflg eq '1'} checked{/if} disabled>金
	<input id=timeSelected5 enable type=checkbox value="1" name=YoyakuCD0{if $val.satflg eq '1'} checked{/if} disabled>土
	<input id=timeSelected5 enable type=checkbox value="1" name=YoyakuCD0{if $val.holiflg eq '1'} checked{/if} disabled>祝祭日
	</td>
	<td>
	{if $mode=='ref'}
	<a title="照会" href="index.php?op=fcl_06_01_02_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}&tcd={$val.tourokuno}">照会</a>
	{elseif $mode=='mod'}
	<a title="変更" href="index.php?op=fcl_06_01_02_mod&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}&apd={$val.appdatefrom}&prfr={$val.monthdayfrom}&prto={$val.monthdayto}&tcd={$val.tourokuno}">変更</a>
	{/if}
	</td>
	<td nowrap class="no-border">&nbsp;({$val.UpdDate}&nbsp;{$val.UpdTime}&nbsp;{$val.UpdName}&nbsp;更新)</td>
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
