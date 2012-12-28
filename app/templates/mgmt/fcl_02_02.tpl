{include file='header.tpl'}
<!-- templates fcl_02_02.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; <a href="index.php?op=fcl_01_01_list">施設選択</a> &gt; <strong><u>室場選択</u></strong>
</div>

<h2 class="subtitle01">室場選択</h2>

<div class="margin-box">
<input type="button" name="entryBtn" class="btn-01" onclick="location.href='index.php?op=fcl_03_01_01_reg&scd={$rec.shisetsucode}';" value="室場の追加" >
　<input type="button" value="施設選択へ戻る" onClick="location.href='index.php?op=fcl_01_01_list';" class="btn-01">
<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</th>
</tr>
</table>
<br />

<table class="itemtable02">
<tr>
	<th width="50">室場<br>コード</th>
	<th width="200">室場名</th>
	<th width="100">区分</th>
	<th width="90">インターネット</th>
	<th width="80">適用開始日</th>
	<th width="80">廃止日</th>
	<th width="120">操作</th>
	{if $user_view.fcl != "FORBIDDEN"}
	<th width="50">削除</th>
	{/if}
</tr>
{foreach $res as $val}
<tr>
	<td nowrap align="center">{$val.shitsujyocode}</td>
	<td nowrap>{$val.shitsujyoname}</td>
	<td nowrap align="center">{$aShitsujyoKbn[$val.shitsujyokbn]}</td>
	<td nowrap align="center">{$val.openflg_view}</td>
	<td nowrap align="center">{$val.appdatefrom}</td>
	<td nowrap align="center">{$val.haishidate}</td>
	<td nowrap align="center">
	<a title="照会" href="index.php?op=fcl_03_02_menu&type=ref&scd={$val.shisetsucode}&rcd={$val.shitsujyocode}">照会</a>
	{if $user_view.fcl != "FORBIDDEN"}
	|
	{if $val.haishi}<span style="color:gray">変更</span>
	{else}
	<a title="変更" href="index.php?op=fcl_03_02_menu&type=mod&scd={$val.shisetsucode}&rcd={$val.shitsujyocode}">変更</a>
	{/if}|
	<a title="廃止" href="index.php?op=fcl_03_03_01_abo&scd={$val.shisetsucode}&rcd={$val.shitsujyocode}">廃止</a>	  
	{/if}
	</td>
	{if $user_view.fcl != "FORBIDDEN"}
	<td nowrap align="center">
	<a title="削除" href="index.php?op=fcl_03_03_02_del&scd={$val.shisetsucode}&rcd={$val.shitsujyocode}">削除</a>
	</td>
	{/if}
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
