{include file='header.tpl'}
<!-- templates stf_01_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
職員管理 &gt; <strong><u>職員一覧</u></strong>
</div>

<h2 class="subtitle01">職員一覧</h2>

<div class="margin-box">
<input type="button" name="entryBtn" value="職員登録" onclick="location.href='index.php?op=stf_02_01_01_reg';">
</div>
<div align="center">
<table class="itemtable02">
<tr>
      <th>職員ID</th>
      <th>職員番号</th>
      <th>職員名</th>
      <th>所属部署</th>
      <th>登録区分</th>
      <th>適用開始日</th>
      <th>廃止日</th>
      <th>操作</th>
      {if $user_type == 3}
      <th>削除</th>
      {/if}
</tr>
{foreach $results as $val}
<tr>
    {foreach $val as $key => $item}
      {if $key!="DisableForP" && $key!="DisableForH"}
      <td>{$item}</td>
      {/if}
    {/foreach}
      <td>
<a title="照会" href="index.php?op=stf_02_01_03_ref&staffid={$val.staffid}">照会</a>| 
{if !$val.DisableForP && !$val.DisableForH}<a title="変更" href="index.php?op=stf_02_01_02_mod&staffid={$val.staffid}">変更</a>{else}<span style="color:gray">変更</span>{/if}| 
{if !$val.DisableForP}<a title="廃止" href="index.php?op=stf_02_02_01_abo&staffid={$val.staffid}">廃止</a>{else}<span style="color:gray">廃止</span>{/if}
</td>
{if $user_type == 3}
      <td><a href="index.php?op=stf_02_02_02_del&staffid={$val.staffid}">削除</a></td>
{/if}
</tr>
{/foreach}
</table>
</div>

{include file='footer.tpl'}
