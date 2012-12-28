{include file='header.tpl'}
<!-- templates login.tpl -->
<body>

<div id="login">

<div id="header">
<h1 id="logo" style="color:{$smarty.const._TITLE_COLOR_};">{$smarty.const._TITLE_}</h1>
</div>

<div id="loginitem" align="center">

<div class="login-body">
<h2 align="center">ログイン</h2>
<span class="f-blue">職員ＩＤ・パスワードは半角英数字でご入力ください。</span>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="login">
<table align="center">
<tr>
	<th width="120" nowrap>職員ＩＤ</th>
	<td><input type="text" name="userid" class="text-area"></td>
</tr>
<tr>
	<th nowrap>パスワード</th>
	<td><input type="password" name="userpass" class="text-area"></td>
</tr>
</table>
<input type="submit" name="loginBtn" value="ログイン" class="login-bt" />
{if $errmsg}
	<br /><strong><span style="color:red">{$errmsg}</span></strong><br>
{/if}
{if $back}
	<input type="hidden" name="ICCARD" value="no">
	<p align="left">
	<input type="button" name="top" value="トップへ" onClick="location.href='index.php'">
	</p>
{/if}
</form>

<!--/login-body-->
</div>    
<!--/login-->
</div>
{if $back}

{/if}

<div id="footer">OpenReaf&reg;</div>

<!--main end-->
</div>

</body>
</html>
