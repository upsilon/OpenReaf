{include file="header.tpl"}
<!--login.ptl-->

<div style="width:100%;padding:5px 0;background:#f5f7e4;border:1px solid #c8cf8d;text-align:center;">

<form method="post" action="index.php">
<input type="hidden" name="op" value="login" />
<input type="hidden" name="check" value="1" />
{$smarty.const.OR_USERID}<br />
<input type="text" name="UserIdTextBox" size="{$sys.userid_size}" maxlength="{$sys.useridlng}" istyle=4 mode="numeric"><br />
{$smarty.const.OR_PASSWORD}<br />
<input type="password" name="PasswordTextBox" size="{$sys.pwd_size}" maxlength="{$sys.pwdlng}" istyle=4 mode=numeric><br /><br />
<input type="submit" name="exec" value="{$smarty.const.OR_LOGIN}">&nbsp;
<input type="reset" value="{$smarty.const.OR_CLEAR}">
<input type="hidden" name="return" value="{$return}">
</form>
</div>
<br />
{include file="footer.tpl"}
