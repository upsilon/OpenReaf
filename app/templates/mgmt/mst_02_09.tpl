{include file='header.tpl'}
<!-- templates mst_02_08.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;
<a href="index.php?op=mst_01_01_top">マスタデータ登録</a>&nbsp;&gt;&nbsp;
<strong>消費税率</strong>
</div>

<h2 class="subtitle01">消費税率</h2>

<div align="center">
{if $errmsg}<div id=errorbox>{$errmsg}</div>{/if}
<form name="forma" method="post" action"index.php">
<input type="hidden" name="op" value="mst_02_09_tax">
<input type="submit" name="saveBtn" value="保存" class="btn-01">
<input type="submit" name="deleteBtn" value="削除" class="btn-01" onclick="return confirm('削除してもよろしいですか？');">
<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='index.php?op=mst_01_01_top';">
<table class="itemtable02">
  <tr>
    <th>&nbsp;</th>
    <th>消費税率(%)</th>
    <th>適用開始日</th>
    <th>適用終了日</th>
  </tr>
  <tr align="center">
    <td>&nbsp;</td>
    <td><input type="text" name="rate" value="" size="4" maxlength="3" style="text-align:right;ime-mode:disabled;"></td>
    <td><input type="text" name="appdatefrom" value="" size="10" maxlength="8" style="ime-mode:disabled;"></td>
    <td><input type="text" name="limitday" value="" size="10" maxlength="8" style="ime-mode:disabled;"></td>
  </tr>
{foreach $results as $key => $value}
  <tr align="center">
    <td><input type="checkbox" name="checkCode[{$key}]" value="1"></td>
    <td>{$value.taxrate}&nbsp;<input type="hidden" name="Rate[{$key}]" value="{$value.taxrate}"></td>
    <td>{$value.appdatefrom}<input type="hidden" name="AppDateFrom[{$key}]" value="{$value.appdatefrom}"></td>
    <td><input type="text" size="10" maxlength="8" name="LimitDay[{$key}]" value="{$value.limitday}" style="ime-mode:disabled;"></td>
  </tr>
{/foreach}
</table>
</form>
</div>

{include file='footer.tpl'}
