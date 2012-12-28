{include file='header.tpl'}
<!-- templates mst_02_03.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;
<a href="index.php?op=mst_01_01_top">マスタデータ登録</a>&nbsp;&gt;&nbsp;
<strong>利用目的</strong>
</div>

<h2 class="subtitle01">利用目的</h2>

<div align="center">
{if $errmsg}<div id=errorbox>{$errmsg}</div>{/if}
<form name="forma" method="post" action"index.php">
<input type="hidden" name="op" value="mst_02_03_purpose">
<input type="submit" name="saveBtn" value="保存" class="btn-01">
<input type="submit" name="deleteBtn" value="削除" class="btn-01" onclick="return confirm('削除してもよろしいですか？');">
<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='index.php?op=mst_01_01_top';">
<table class="itemtable02">
  <tr>
    <th>&nbsp;</th>
    <th>利用目的コード</th>
    <th>表示順</th>
    <th>名称</th>
    <th>大分類</th>
    <th>表示フラグ</th>
  </tr>
  <tr align="center">
    <td>&nbsp;</td>
    <td><input type="text" name="code" value="" size="3" maxlength="2" style="ime-mode:disabled;"></td>
    <td><input type="text" name="order" value="" size="3" maxlength="2" style="ime-mode:disabled;"></td>
    <td><input type="text" name="codename" value="" size="30" maxlength="64" style="ime-mode:active;"></td>
    <td>
      <select name="daicode">
      {html_options options=$aMokutekiDaiCode}
      </select>
    </td>
    <td>
      <select name="flg">
      {html_options options=$aDelFlg}
      </select>
    </td>
  </tr>
{foreach $results as $key => $value}
  <tr align="center">
    <td><input type="checkbox" name="checkCode[{$key}]" value="1"></td>
    <td>{$value.mokutekicode}<input type="hidden" name="Code[{$key}]" value="{$value.mokutekicode}"></td>
    <td><input type="text" size="3" maxlength="2" name="Order[{$key}]" value="{$value.mokutekiskbcode}"></td>
    <td><input type="text" size="30" maxlength="64" name="CodeName[{$key}]" value="{$value.mokutekiname}" style="ime-mode:active;"></td>
    <td>
      <select name="MokutekiDaiCode[{$key}]">
      {html_options options=$aMokutekiDaiCode selected=$value.mokutekidaicode}
      </select>
    </td>
    <td>
      <select name="Flg[{$key}]">
      {html_options options=$aDelFlg selected=$value.delflg}
      </select>
    </td>
  </tr>
{/foreach}
</table>
</form>
</div>

{include file='footer.tpl'}
