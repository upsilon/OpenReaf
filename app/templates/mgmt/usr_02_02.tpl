{include file='header.tpl'}
<!-- templates usr_02_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function mysubmit()
{
	var i, n
	n = document.forma.userjyoutaikbn.length;
	for (i = 0; i < n; ++i) {
		if (document.forma.userjyoutaikbn[i].checked) {
			break;
		}
	}

	if(i != 0) {
		if (document.forma.stoperasedate.value == '') {
			alert("停止/抹消日が入力されていません");
			return false;
		}
		if (i == 1 && document.forma.stopenddate.value == '') {
			if(!confirm('利用停止解除日の指定がないので、自動での利用停止解除は行われません。')) {
				return false;
			}
		}
	}
}
//-->
</script>
{/literal}

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <strong><u>利用停止・抹消</u></strong>
</div>

<h2 class="subtitle01">利用停止・抹消</h2>

<p><input type="button" value="検索へ戻る" onClick="location.href='index.php?op=usr_01_01_search&back=1'"></p>
{if $message}<div id =errorbox>{$message}</div>{/if}

<h4 class="subtitle02">基本情報</h4>

{include file='usr_basic.tpl'}

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op"  value="{$op}">
<input type="hidden" name="UserID" value="{$UserID}">

<div class="margin-box">
<h4>登録状態を指定してください。</h4>
<table class="ri-table">
<tr>
	<th width="150">{$col.userjyoutaikbn[0]}</th>
	<td>
	{html_radios name="userjyoutaikbn" options=$col.userjyoutaikbn[4] selected=$para.userjyoutaikbn}
	</td>
</tr>
<tr>
	<th>{$col.stoperasejiyu[0]}</th>
	<td><textarea name="stoperasejiyu" cols="60" rows="5" >{$para.stoperasejiyu}</textarea></td>
</tr>
<tr>
	<th>{$col.stoperasedate[0]}</th>
	<td nowrap>
	<input type="text" name="stoperasedate" value="{$para.stoperasedate}" {if $err.stoperasedate}class="error"{/if} size="9" maxlength="9" style="ime-mode:disabled;">(入力例：{$smarty.const._EXAMPLE_DATE_})&nbsp;&nbsp;{$col.stopenddate[0]}&nbsp;<input type="text" name="stopenddate" value="{$para.stopenddate}" {if $err.stopenddate}class="error"{/if} size="9" maxlength="9" style="ime-mode:disabled;"> (入力例：{$smarty.const._EXAMPLE_DATE_})
	</td>
</tr>
</table>

<table width="650" cellspacing="0" cellpadding="0">
<tr>
	<td width="650" align="center"><input type="submit" name="updateBtn" value="登録" onclick="return mysubmit();"></td>
</tr>
</table>
</div>
</form>

{include file='footer.tpl'}
