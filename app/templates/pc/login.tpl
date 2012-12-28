{include file="header.tpl"}

<div id="login-box">
<form name="account" method="post" action="index.php">
<input type="hidden" name="op" value="login" />
<input type="hidden" name="check" value="1" />

{if $smarty.const._USE_NUMERICKEY_}
<div id="left">
<div align="center">

<table width="300">
<tr>
	<td width="33%"><a href="#" class="num-bt" onclick="addField(7,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>７</span></a></td>
	<td width="33%"><a href="#" class="num-bt" onclick="addField(8,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>８</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(9,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>９</span></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="addField(4,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>４</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(5,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>５</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(6,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>６</span></a></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="addField(1,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin})"><span>１</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(2,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>２</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(3,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>３</span></a></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="backspaceField()"><span>{$smarty.const.OR_BACK}</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField(0,{$sys.useridlng},{$sys.pwdlng},{$sys.useridlngmin},{$sys.pwdlngmin});"><span>０</span></a></td>
	<td><a href="#" class="num-bt" onclick="clearField()"><span>{$smarty.const.OR_CLEAR}</span></a></td>
</tr>
</table>
</div>
</div>

<div id="right"> 
{else}
<div id="right" style="margin-left:180px;"> 
{/if}
<div class="login-area">
	<div class="passID">
	<strong>{$smarty.const.OR_USERID}</strong><br>
	<input type="text" name="UserIdTextBox" size="{$sys.userid_size}" maxlength="{$sys.useridlng}" style="font-size:large;ime-mode:disabled;" onclick="setFocusObj('id');"><br >
	</div>
	<div class="passID">
	<strong>{$smarty.const.OR_PASSWORD}</strong><br>
	<input type="password" name="PasswordTextBox" size="{$sys.pwd_size}" maxlength="{$sys.pwdlng}" style="font-size:large;ime-mode:disabled;" onclick="setFocusObj('pw');"><br />
	</div>
	<div class="passID">
	<input type="image" src="image/confirmation.gif" alt="確認"><br>
	<input name="return" type="hidden" value="{$return}">
	</div>
</div>
</div>
<br clear="left">
</form>
</div>

{include file="footer.tpl"}
