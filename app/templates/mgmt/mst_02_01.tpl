{include file='header.tpl'}
<!-- templates mst_02_01.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;
<a href="index.php?op=mst_01_01_top">マスタデータ登録</a>&nbsp;&gt;&nbsp;
<strong>システムコード</strong>
</div>

<h2 class="subtitle01">システムコード</h2>

<div align="center">
{if $errmsg}<div id=errorbox>{$errmsg}</div>{/if}
<form name="forma" method="post" action"index.php">
<input type="hidden" name="op" value="mst_02_01_system">
<input type="submit" name="saveBtn" value="保存" class="btn-01">
<input type="submit" name="deleteBtn" value="削除" class="btn-01" onclick="return confirm('削除してもよろしいですか？');">
<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='index.php?op=mst_01_01_top';">
<br>
コードID：
<select name="code_id" class="btn-01" onchange="this.form.submit();">
<option value="">指定なし</option>
{html_options options=$type_options selected=$code_id}
</select>
<table class="itemtable02">
  <tr>
    <th>&nbsp;</th>
    <th>コードID</th>
    <th>コード</th>
    <th>名称</th>
  </tr>
  <tr align="center">
    <td>&nbsp;</td>
    <td><input type="text" name="codeid" value="" size="20" maxlength="32" style="ime-mode:disabled;"></td>
    <td><input type="text" name="code" value="" size="3" maxlength="2" style="ime-mode:disabled;"></td>
    <td><input type="text" name="codename" value="" size="30" maxlength="64" style="ime-mode:active;"></td>
  </tr>
{foreach $results as $key => $value}
  <tr align="center">
    <td><input type="checkbox" name="checkCode[{$key}]" value="1"></td>
    <td>{$value.codeid}<input type="hidden" name="CodeID[{$key}]" value="{$value.codeid}"></td>
    <td>{$value.code}<input type="hidden" name="Code[{$key}]" value="{$value.code}"></td>
    <td><input type="text" size="30" maxlength="64" name="CodeName[{$key}]" value="{$value.codename}" style="ime-mode:active;"></td>
  </tr>
{/foreach}
</table>
</form>
</div>

{include file='footer.tpl'}
