{include file='header.tpl'}
<!-- templates fcl_01_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; <strong><u>施設一覧</u></strong>
</div>

<h2 class="subtitle01">施設一覧</h2>

<div class="margin-box">
{if $user_view.fcl != "FORBIDDEN"}
<input type="button" name="entryBtn" class="btn-01" onclick="location.href='index.php?op=fcl_02_01_01_reg';" value="施設の追加" >
{/if}

<table class="itemtable02">
<tr>
	<th width="50">施設<br>コード</th>
	<th width="50">表示順</th>
	<th width="200">施設名</th>
	<th width="90">インターネット</th>
	<th width="80">適用開始日</th>
	<th width="80">廃止日</th>
	<th width="50">室場</th>
	<th width="120">操作</th>
{if $user_view.fcl != "FORBIDDEN"}
	<th width="50">削除</th>
{/if}
</tr>
{foreach $res as $val}
<tr>
	<td align="center">{$val.shisetsucode}</td>
	<td align="center">{$val.shisetsuskbcode}</td>
	<td>{$val.shisetsuname}</td>
	<td align="center">{if $val.openflg == 1}公開{else}非公開{/if}</td>
	<td align="center">{$val.appdatefrom}</td>
	<td align="center">{$val.haishidate}</td>
	<td align="center"><a title="一覧" href="index.php?op=fcl_02_02_list&scd={$val.shisetsucode}">一覧</a></td>
	<td nowrap align="center"><a title="照会" href="index.php?op=fcl_02_01_03_ref&scd={$val.shisetsucode}">照会</a>
{if $user_view.fcl != "FORBIDDEN"}
|{if !$val.haishi}<a title="変更" href="index.php?op=fcl_02_01_01_reg&scd={$val.shisetsucode}">変更</a>{else}<span style="color:gray">変更</span>{/if}
|<a title="廃止" href="index.php?op=fcl_02_01_04_abo&scd={$val.shisetsucode}">廃止</a>
{/if}
	</td>
{if $user_view.fcl != "FORBIDDEN"}
	<td nowrap align="center">
	<a title="削除" href="index.php?op=fcl_02_01_05_del&scd={$val.shisetsucode}">削除</a>
	</td>
{/if}
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
