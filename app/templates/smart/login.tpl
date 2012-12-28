{include file="header.tpl"}

{include file="page-header.tpl"}
<!--login.ptl-->

<form name="account" method="post" action="index.php" class="login">
<input type="hidden" name="op" value="login" />
<input type="hidden" name="check" value="1" />
<p>
<span class="icon-01">&nbsp;&nbsp;{$smarty.const.OR_USERID}&nbsp;&nbsp;</span>
<input type="text" name="UserIdTextBox" size="20" maxlength="{$sys.useridlng}" />
</p>
<p>	
<span class="icon-01">{$smarty.const.OR_PASSWORD}</span>
<input type="password" name="PasswordTextBox" size="20" maxlength="{$sys.pwdlng}" />
</p>
<div class="login-btn">
<noscript>
<p style="font-size:120%;" class="f-ore">{$smarty.const.OR_NO_SCRIPT}</p>
</noscript>	
<input name="exec" type="submit" value="{$smarty.const.OR_LOGIN}" class="rsv-btn">&nbsp;
<input type="reset" value="{$smarty.const.OR_CLEAR}" class="rsv-btn">
</div>
<input name="return" type="hidden" value="{$return}">
</form>

{include file="footer.tpl"}
