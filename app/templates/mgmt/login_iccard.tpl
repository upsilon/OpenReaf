{include file='header.tpl'}
<!-- templates login_iccard.tpl -->

<script type="text/javascript" language="javascript">
<!--
{literal}
	var ICCardProxyObj;
	var loginitem;
	var msgitem;
	var pinid;
	var idspan;
	var curICCardStatus = "false";
	var tid = 0;

	function myload()
	{
		try {
			ICCardProxyObj = document.getElementById("ICCardProxyObj");
			loginitem = document.getElementById("loginitem");
			msgitem = document.getElementById("msgitem");
			pinid = document.getElementById("pinid");
			idspan = document.getElementById("idspan");
			tid = setInterval("refreshLoginDiv()",100);
		} catch(e) {
			alert("ICカード異常、管理者に連絡ください。");
		}
	}

	function myunload()
	{
		clearInterval(tid);
		tid = 0;
	}

	function refreshLoginDiv()
	{
		var ICCardStatus = ICCardProxyObj.getICCardStatus();
		if (curICCardStatus != ICCardStatus) {
			curICCardStatus = ICCardStatus;
			if (ICCardStatus == "true") {
				idspan.innerHTML = ICCardProxyObj.getUserID();
				loginitem.style.visibility = 'visible';
				msgitem.style.visibility = 'hidden';
			} else {
				loginitem.style.visibility = 'hidden';
				msgitem.style.visibility = 'visible';
			}
		}
	}

	function loginSubmit()
	{
		var loginStr = ICCardProxyObj.getLoginStr(pinid.value);
		var loginArr = getLoginArr(loginStr);

		if (loginStr == '' || loginArr == null) {
			alert("パスワードを正しく入力してください！");
			return false;
		}
		document.loginform.userid.value = loginArr[0];
		document.loginform.userpass.value = loginArr[1];
		return true;
	}

	function getLoginArr(str)
	{
		var pos = str.indexOf('@@');
		if (pos == -1) {
			return null;
		} else {
			return str.split('@@');
		}
	}
{/literal}
// -->
</script>

<body onLoad="myload();" onUnload="myunload();">
<applet codebase="Applet/"
	code="jp.openreaf.ICCardProxy.class"
	archive="ICCardApplet.jar"
	id="ICCardProxyObj"
	name="ICCardProxy"
	width="0"
	height="0">
</applet>

<div id="login">

<div id="header">
<h1 id="logo" style="color:{$smarty.const._TITLE_COLOR_};">{$smarty.const._TITLE_}</h1>
</div>

<div id="msgitem" align="center">
<strong>
カードを挿入してください<br><br>
カードをお持ちではない方は<a href="index.php?op=login&ICCARD=no"><font color="red">こちら
</font></a>へ
</strong>
</div>

<div id="loginitem" align="center" style="visibility:hidden">

<div class="login-body">
<h2 align="center">ログイン</h2>
<span class="f-blue">パスワードは半角英数字でご入力ください。</span>

<form name="loginform" method="post" action="index.php">
<input type="hidden" name="op" value="login">
<input type="hidden" name="userid" value="">
<input type="hidden" name="userpass" value="">
<input type="hidden" id="ImIC" name="ImIC" value="trust me">
<input type="hidden" id="SUBMIT" name="SUBMIT" value="dont suspect">
<table align="center">
<tr>
	<th width="120" nowrap>職員ＩＤ</th>
	<td><span id="idspan" style="font-size:20px"></span></td>
</tr>
<tr>
	<th nowrap>パスワード</th>
	<td><input type="password" name="pinid" id="pinid" class="text-area"></td>
</tr>
</table>
<input type="submit" name="loginBtn" value="ログイン" class="login-bt" onclick="return loginSubmit();"/>
{if $errmsg}
	<div><br><strong><span style="color:red">{$errmsg}</span></strong></div>
{/if}
</form>
</div>

</div>
<!--login end-->
</div>

</body>
</html>
