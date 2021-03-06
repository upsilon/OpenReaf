{include file='header.tpl'}
<!-- templates usr_03_03.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <a href="index.php?op=usr_02_01_02_mod&UserID={$UserID}">利用者情報変更</a> &gt; <strong>利用権限設定</strong>
</div>

<h2 class="subtitle01">利用権限設定</h2>

<div class="margin-box">
<input type="button" name="backBtn" value="戻る" class="btn-01" onClick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#kengen';">
<table width="240" class="itemtable02">
<tr>
	<th width="100">利用者ID</th>
	<td>　{$para.userid}</td>
</tr>
<tr>
	<th>利用者名</th>
	<td nowrap>　{$para.namesei}</td>
</tr>
</table>
<br>
<div class="itemtop-area">利用者の施設利用権限を設定してください。</div>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">
<table>
<tr>
	<td width="330" valign="top">
	<!-- left listtable start-->
	<table width="310" class="itemtable02" style="margin-left:10px;">
	<tr>
		<th width="260">スポーツ施設</th>
		<th width="50" align="center"><input type="button" name="btm_userinfo" onClick="alternateCheckBox(this.form, 'ShisetsuCode_sports');" value="反転"></th>
	</tr>
{foreach $sports_list as $sports}
	<tr>
		<td>{$sports.shisetsuname}</td>
		<td align="center"><input type="checkbox" name="ShisetsuCode_sports[]" value="{$sports.shisetsucode}" {$sports.checked}></td>
	</tr>
{/foreach}
	</table>
	<!-- left listtable end-->
	</td>
	<td width="310" valign="top">
	<!-- right listtable start-->    
	<table width="310" class="itemtable02">
	<tr>
		<th width="260">文化施設</th>
		<th width="50" align="center"><input name="btm_userinfo2" type="button" onclick="alternateCheckBox(this.form, 'ShisetsuCode_culture');" value="反転"></th>
	</tr>
{foreach $culture_list as $culture}
	<tr>
		<td>{$culture.shisetsuname}</td>
		<td align="center"><input type="checkbox" name="ShisetsuCode_culture[]" value="{$culture.shisetsucode}" {$culture.checked}></td>
	</tr>
{/foreach}
	</table>
	<!-- right listtable end-->  
	</td>
</tr>
<tr>
    	<td colspan="2" align="center">
	<input type="submit" name="updateBtn" value="登録">
	<input type="button" name="backBtn" value="戻る" onClick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#kengen';">
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
