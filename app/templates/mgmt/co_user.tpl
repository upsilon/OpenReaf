{include file='header.tpl'}
<!-- templates co_user.tpl -->

<script type="text/javascript" language="javascript">
{literal}
<!--
function windowClose(f)
{
	window.opener.document.forma.UserID.value = f.UserID.value;
	window.opener.document.forma.NameSei.value = f.NameSei.value;
	window.opener.document.forma.NameSeiKana.value = f.NameSeiKana.value;
	window.opener.callUserID();
	window.close();
}

function clearElements()
{
	document.forma.UserID.value = '';
	document.forma.Name.value = '';
	document.forma.Address.value = '';
	document.forma.TelNo1.value = '';
	document.forma.TelNo2.value = '';
	document.forma.TelNo3.value = '';
	document.forma.KojinDanKbn.options[0].selected = true;
	document.forma.MokutekiCode.options[0].selected = true;
}
//-->
</script>
{/literal}

<body>
<div id="contents">

<h3 class="subtitle01">利用者情報の検索</h3>
<input type="button" name="closeBtn" onclick="window.close()" value="閉じる" />
<br />
<p>◇利用者情報の検索を行います。<br>
下記の検索項目いずれかを指定して、「検索ボタン」を押してください。</p>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="popup_user">
<table class="itemtable03">
<tr>
	<th width="100">利用者ID</th>
	<td><input type="text" name="UserID" value="{$req.UserID}" style="ime-mode:disabled;" />&nbsp;<label>前方一致で検索<input type="checkbox" name="PartialMatchFlg" value="1" {if $req.PartialMatchFlg == '1'}checked{/if}></label></td>
</tr>
<tr>
	<th>登録区分</th>
	<td>
	<select name="KojinDanKbn">
	{html_options options=$aKbn selected=$req.KojinDanKbn|default:2}
	</select>
	</td>
</tr>
<tr>
	<th>利用者名</th>
	<td><input type="text" name="Name" size="35" maxlength="50" value="{$req.Name}" style="ime-mode:active;" />&nbsp;（部分一致）</td>
</tr>
<tr>
	<th>住所</th>
	<td><input type="text" name="Address" size="35" maxlength="35" value="{$req.Address}" style="ime-mode:active;" />&nbsp;（部分一致）</td>
</tr>
<tr>
	<th>電話番号</th>
	<td>
	<input type="text" name="TelNo1" value="{$req.TelNo1}" size="5" maxlength="4" style="ime-mode:disabled;" />&nbsp;-
	<input type="text" name="TelNo2" value="{$req.TelNo2}" size="5" maxlength="4" style="ime-mode:disabled;" />&nbsp;-
	<input type="text" name="TelNo3" value="{$req.TelNo3}" size="5" maxlength="4" style="ime-mode:disabled;" />&nbsp;（部分一致）
	</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>
	<select name="MokutekiCode">
	{html_options options=$aMokuteki selected=$req.MokutekiCode|default:'00'}
	</select></td>
</tr>
<tr>
	<td class="no-border">&nbsp;</td>
	<td class="no-border">
	<input type="submit" name="searchBtn" value="検索">&nbsp;&nbsp;
	<input type="button" name="clearBtn" value="クリア" onclick="clearElements();"/>
	</td>
</tr>
</table>
</form>

{if $message}<div id="errorbox">{$message}</div>{/if}

<h4>◇&nbsp;検索結果&nbsp;◇</h4>

<table width="592" class="itemtable02">
<tr height="18">
	<th width="70">利用者ID</th>
	<th width="140">利用者名</th>
	<th width="140">{$smarty.const._KANA_}</th>
	<th width="66">利用者登録</th>
</tr>
{foreach $recs as $key => $value}
<tr>
	<td align="center">{$value.userid}</td>
	<td>{$value.namesei}</td>
	<td>{$value.nameseikana}</td>
	<td align="center">
	<form>
		<input name="btn_sendResult" type="button" value="決定" onclick="windowClose(this.form);" />
		<input type="hidden" name="UserID" value="{$value.userid}">
		<input type="hidden" name="NameSei" value="{$value.namesei}">
		<input type="hidden" name="NameSeiKana" value="{$value.nameseikana}">
		<input type="hidden" name="UserJyoutaiKbn" value="{$value.userjyoutaikbn}">
	</form>
	</td>
</tr>
{foreachelse}
{/foreach}
</table>

</div>
</body>
</html>
