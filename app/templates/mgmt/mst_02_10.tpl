{include file='header.tpl'}
<!-- templates mst_02_10.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;
<a href="index.php?op=mst_01_01_top">マスタデータ登録</a>&nbsp;&gt;&nbsp;
<strong>割増率</strong>
</div>

<h2 class="subtitle01">割増率</h2>

<div align="center">
{if $errmsg}<div id=errorbox>{$errmsg}</div>{/if}
<form name="forma" method="post" action"index.php">
<input type="hidden" name="op" value="mst_02_10_extra">
<input type="submit" name="saveBtn" value="保存" class="btn-01">
<input type="submit" name="deleteBtn" value="削除" class="btn-01" onclick="return confirm('削除してもよろしいですか？');">
<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='index.php?op=mst_01_01_top';">
<table class="itemtable02">
  <tr>
    <th>&nbsp;</th>
    <th>割増コード</th>
    <th>名称</th>
    <th>割増倍率(%)</th>
  </tr>
  <tr align="center">
    <td>&nbsp;</td>
    <td><input type="text" name="code" value="" size="6" maxlength="2" style="ime-mode:disabled;"></td>
    <td><input type="text" name="codename" value="" size="30" maxlength="64" style="ime-mode:active;"></td>
    <td><input type="text" name="rate" value="" size="8" maxlength="6" style="text-align:right;ime-mode:disabled;"></td>
  </tr>
{foreach $results as $key => $value}
  <tr align="center">
    <td><input type="checkbox" name="checkCode[{$key}]" value="1"></td>
    <td>{$value.extracode}<input type="hidden" name="Code[{$key}]" value="{$value.extracode}"></td>
    <td><input type="text" size="30" maxlength="64" name="CodeName[{$key}]" value="{$value.extraname}" style="ime-mode:active;"></td>
    <td><input type="text" size="8" maxlength="6" name="Rate[{$key}]" value="{$value.rate}" style="text-align:right;ime-mode:disabled;"></td>
  </tr>
{/foreach}
</table>
</form>
</div>

{include file='footer.tpl'}
