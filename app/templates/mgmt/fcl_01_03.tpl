{include file='header.tpl'}
<!-- templates fcl_01_03.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
施設管理 &gt; <strong><u>施設分類一覧</u></strong>
</div>

<h2 class="subtitle01">施設分類一覧</h2>

<div class="margin-box">

<input type="button" name="entryBtn" value="施設分類追加" class="btn-01" onclick="location.href='index.php?op=fcl_02_03_01_reg';">
{if $addConfirm}<div id="errorbox">{$addConfirm}</div>{/if}
<table class="itemtable02">
<tr>
    <th width="120">施設分類コード</th>
    <th width="50">表示順</th>
    <th width="90">インターネット</th>
    <th width="200">施設分類名称</th>
    <th width="80">操作</th>
    <th width="50">削除</th>
</tr>
{foreach $res as $val}
<tr align="center">
    <td>{$val.shisetsuclasscode}</td>
    <td>{$val.shisetsuclassskbcode}</td>
    <td>{if $val.delflg}非公開{else}公開{/if}</td>
    <td>{$val.shisetsuclassname|default:"&nbsp;"}</td>
	<td><a href='index.php?op=fcl_02_03_03_ref&ccd={$val.shisetsuclasscode}'>照会</a> | <a href='index.php?op=fcl_02_03_01_reg&ccd={$val.shisetsuclasscode}'>変更</a> </td>
	<td><a href='index.php?op=fcl_02_03_04_del&ccd={$val.shisetsuclasscode}'>削除</a></td>
</tr>
{/foreach}
</table>
</div>
{include file='footer.tpl'}
