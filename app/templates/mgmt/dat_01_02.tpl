{include file='header.tpl'}
<!-- templates dat_01_02.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理 &gt; <strong><u>{$page_title}</u></strong>
</div>

<h2 class="subtitle01">{$page_title}</h2>

<div id="room-menu">
<form name="main" method="post" enctype="multipart/form-data" action="index.php">
<input type="hidden" name="op" value="{$op}">
<table width="500">
<tr>
	<td align="left" width="70">ファイル名：</td>
	<td align="left">
	<input type="file" name="uploadfile" size="64">
	</td>
</tr>
<tr>
	<td align="left">&nbsp;</td>
	<td align="left">
	{if $append}追加モード<input type="checkbox" name="append" value="1"><br><br>{/if}
	<input type="submit" name="commitBtn" value="実行" style="width:60px"> 
	</td>
	
</tr>
<tr>
	<td align="left">&nbsp;</td>
	<td align="left">
	<font color="red">
	{foreach from=$messages item=msg}{$msg}<br/>{/foreach}
	</font>
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
