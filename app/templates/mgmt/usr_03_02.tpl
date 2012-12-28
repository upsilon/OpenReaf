{include file='header.tpl'}
<!-- templates usr_03_02.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <a href="index.php?op=usr_02_01_02_mod&UserID={$UserID}">利用者情報変更</a> &gt; <strong><u>詳細情報変更</u></strong>
</div>

<h2 class="subtitle01">詳細情報変更</h2>

<div class="margin-box">
<input type="button" value="戻る" class="btn-01" onclick="location.href='index.php?op=usr_02_01_02_mod&UserID={$UserID}#detail';">

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

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">
<table class="ri-table">
<tr>
	<th>{$col.katudogaiyou[0]}</th>
	<td><input type="text" name="katudogaiyou" value="{$para.katudogaiyou}" {$err.katudogaiyou} size="70" maxlength="70"></td>
</tr>
<tr>
	<th>{$col.kaihijyouhou[0]}</th>
	<td><input type="text" name="kaihijyouhou" value="{$para.kaihijyouhou}" {$err.kaihijyouhou}></td>
</tr>
<tr>
	<th>{$col.katudodate[0]}</th>
	<td><input type="text" name="katudodate" value="{$para.katudodate}" {$err.katudodate} size="30" maxlength="30"></td>
</tr>
<tr>
	<th>{$col.lecturerjyouhou[0]}</th>
	<td><input type="text" name="lecturerjyouhou" value="{$para.lecturerjyouhou}" {$err.lecturerjyouhou}>
</tr>
<tr>
	<th>{$col.thanksjyouhou[0]}</th>
	<td><input type="text" name="thanksjyouhou" value="{$para.thanksjyouhou}" {$err.thanksjyouhou} size="40" maxlength="40">
</tr>
<tr>
	<th>{$col.bikou[0]}</th>
	<td>
	<textarea name="bikou" cols="60" rows="3" style="ime-mode:active;">{$para.bikou}</textarea>
	</td>
</tr>
<tr>
  <td colspan="2" class="no-border" align="center">
    <input type="submit" name="tourokuBtn" value="登録" onclick="return confirm('登録しますか？');">
  </td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
