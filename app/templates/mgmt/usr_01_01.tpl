{include file='header.tpl'}
<!-- templates usr_01_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function doSubmit(mode)
{
	var now = new Date();
	var time = now.getTime();
	if (mode == 'pdf') {
		document.forma.target = '_blank';
		document.forma.action = 'index.php?random='+time;
	} else {
		document.forma.target = '_self';
		document.forma.action = 'index.php';
	}
	document.forma.mode.value = mode;
	document.forma.submit();
}

function clearElements()
{
	document.getElementById('UserID').value='';
	document.getElementById('UserIDTo').value='';
	document.getElementById('PartialMatchFlg').checked=false;
	document.getElementById('KojinDanKbn').options[0].selected=true;
	document.getElementById('HeadName').value='';
	document.getElementById('Name').value='';
	document.getElementById('MailAdr').value='';
	document.getElementById('TelNo1').value='';
	document.getElementById('TelNo2').value='';
	document.getElementById('TelNo3').value='';
	document.getElementById('ShisetsuCode').options[0].selected=true;
	document.getElementById('MokutekiCode').options[0].selected=true;
	document.getElementById('TourokuBushoCode').options[0].selected=true;
	document.getElementById('UserJyoutaiKbn').options[0].selected=true;
	document.getElementById('UserLimitStart').value='';
	document.getElementById('UserLimitEnd').value='';
	document.getElementById('FirstApplyStart').value='';
	document.getElementById('FirstApplyEnd').value='';
	//document.getElementById('showUseList').style.display='none';
}
// -->
</script>
{/literal}

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
利用者管理 &gt; <strong><u>利用者検索</u></strong>
</div>

<h2 class="subtitle01">利用者検索</h2>

{if $user_view.usr != "FORBIDDEN"}
  <input type="button" name="entryBtn" value="利用者登録" onclick="location.href='index.php?op=usr_02_01_01_reg';">
{/if}

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="usr_01_01_search">
<input type="hidden" name="mode" value="search">
<div class="itemtop-area">
<h3>■検索条件設定</h3>
・検索する条件の項目を入力し、検索ボタンを押してください。
</div>
{if $message}<div id="errorbox">{$message}</div>{/if}
<table class="itemtable03">
<tr>
	<th width="110">利用者ID</th>
	<td>
	<input name="UserID" id="UserID" type="text" size="16" maxlength="16" value="{$p.UserID}" style="ime-mode:disabled;">&nbsp;〜&nbsp;
	<input name="UserIDTo" id="UserIDTo" type="text" size="16" maxlength="16" value="{$p.UserIDTo}" style="ime-mode:disabled;">
	&nbsp;<label>前方一致で検索<input type="checkbox" name="PartialMatchFlg" id="PartialMatchFlg" value="1" {if $p.PartialMatchFlg == '1'}checked{/if}></label>
	</td>
	<th width="110">申請日</th>
	<td>
	<input name="FirstApplyStart" id="FirstApplyStart" type="text" size="10" maxlength="8" value="{$p.FirstApplyStart}" style="ime-mode:disabled;">&nbsp;〜&nbsp;
	<input name="FirstApplyEnd" id="FirstApplyEnd" type="text" size="10" maxlength="8" value="{$p.FirstApplyEnd}" style="ime-mode:disabled;">
	</td>
</tr>
<tr>
	<th>登録区分</th>
	<td>
	<select name="KojinDanKbn" id="KojinDanKbn">
	<option value=""></option>
	{html_options options=$aKbnData selected=$p.KojinDanKbn}
	</select>
	</td>
	<th>登録部署</th>
	<td>
	<select name="TourokuBushoCode" id="TourokuBushoCode">
	<option value=""></option>
	{html_options options=$aBusho selected=$p.TourokuBushoCode}
	</select>
	</td>
</tr>
<tr>
	<th>利用者名</th>
	<td><input name="Name" id="Name" value="{$p.Name}" type="text" size="60" maxlength="60" style="ime-mode:active;">&nbsp;（部分一致）</td>
	<th>登録状態</th>
	<td>
	<select name="UserJyoutaiKbn" id="UserJyoutaiKbn">
	<option value=""></option>
	{html_options options=$aUserJyoutaiKbn selected=$p.UserJyoutaiKbn}
	</select>
	</td>
</tr>
<tr>
	<th>代表者名</th>
	<td>
	<input name="HeadName" id="HeadName" value="{$p.HeadName}" type="text" size="60" maxlength="60" style="ime-mode:active;">&nbsp;（部分一致）
	</td>
	<th>登録期限</th>
	<td>
	<input name="UserLimitStart" id="UserLimitStart" type="text" size="10" maxlength="8" value="{$p.UserLimitStart}" style="ime-mode:disabled;">&nbsp;〜&nbsp;
	<input name="UserLimitEnd" id="UserLimitEnd" type="text" size="10" maxlength="8" value="{$p.UserLimitEnd}" style="ime-mode:disabled;">
	</td>
</tr>
<tr>
	<th>メールアドレス</th>
	<td>
	<input name="MailAdr" id="MailAdr" value="{$p.MailAdr}" type="text" size="30" maxlength="30" style="ime-mode:disabled;">&nbsp;（部分一致）
	</td>
	<th>施設</th>
	<td>
	<select name="ShisetsuCode" id="ShisetsuCode">
	<option value=""></option>
	{html_options options=$aShisetsu selected=$p.ShisetsuCode}
	</select>
	</td>
</tr>
<tr>
	<th>電話番号</th>
	<td>
	<input name="TelNo1" id="TelNo1" value="{$p.TelNo1}" type="text" size="5" maxlength="4" style="ime-mode:disabled;">&nbsp;-
	<input name="TelNo2" id="TelNo2" value="{$p.TelNo2}" type="text" size="5" maxlength="4" style="ime-mode:disabled;">&nbsp;-
	<input name="TelNo3" id="TelNo3" value="{$p.TelNo3}" type="text" size="5" maxlength="4" style="ime-mode:disabled;">&nbsp;（完全一致）
	</td>
	<th>利用目的</th>
	<td>
	<select name="MokutekiCode" id="MokutekiCode">
	{html_options options=$aMokuteki selected=$p.MokutekiCode}
	</select>
	</td>
</tr>
<tr>
	<td class="no-border">&nbsp;</td>
	<td colspan="3" class="no-border">
	<input type="button" name="searchBtn" value="検索" onclick="doSubmit('search');">&nbsp;&nbsp;
	<input type="button" name="pdfBtn" value="印刷" onclick="doSubmit('pdf');">&nbsp;&nbsp;
	<input type="button" name="csvBtn" value="CSV" onclick="doSubmit('csv');">&nbsp;&nbsp;
	<input type="button" name="clearBtn" value="クリア" onclick="clearElements();">
	</td>
</tr>
</table>
</form>

{if $results}
<h3>■検索結果一覧</h3>
<table class="itemtable02" width="98%">
<caption>
<div align="left">
	・該当する利用者を選択してください。
</div>
</caption>
<tr height="20">
	<th width="80" rowspan="2">利用者ID</th>
	<th width="18%">利用者名(漢字)</th>
	<th width="13%">代表者名(漢字)</th>
	<th width="25%" rowspan="2">住所</th>
	<th width="100" rowspan="2">電話番号</th>
	<th width="80" rowspan="2">停止/抹消日</th>
	<th width="80" rowspan="2">操作</th>
	{if $user_view.usr != "FORBIDDEN"}
	  <th width="80" rowspan="2">削除</th>
	{/if}
</tr>
<tr height="20">
	<th>利用者名({$smarty.const._KANA_})</th>
	<th>代表者名({$smarty.const._KANA_})</th>
</tr>
<tbody id="showUseList">
{foreach $results as $value}
<tr>
	<td rowspan="2">{$value.userid}</td>
	<td>{$value.namesei|default:"<div align=\"center\">-</div>"}</td>
	<td>{$value.headnamesei|default:"<div align=\"center\">-</div>"}</td>
	<td rowspan="2">{$value.adr1|default:"<div align=\"center\">-</div>"}{$value.adr2}</td>
	<td rowspan="2">{$value.telno11}-{$value.telno12}-{$value.telno13}</td>
	<td rowspan="2">{$value.stoperasedate}</td>
	<td rowspan="2" align="center">
	<a class="link" title="照会" href="index.php?op=usr_02_01_03_ref&UserID={$value.userid}">照会</a><br>
	{if $user_view.usr != "FORBIDDEN"}
		<a class="link" title="変更" href="index.php?op=usr_02_01_02_mod&UserID={$value.userid}">変更</a><br>
		<a class="link" title="廃止" href="index.php?op=usr_02_02_erase&UserID={$value.userid}">停止・抹消</a>
	{/if}
	</td>
	{if $user_view.usr != "FORBIDDEN"}
		<td rowspan="2" align="center">
		<a class="link" title="削除" href="index.php?op=usr_02_03_del&UserID={$value.userid}">削除</a>
		</td>
	{/if}
</tr>
<tr>
	<td>{$value.nameseikana|default:"<div align=\"center\">-</div>"}</td>
	<td>{$value.headnameseikana|default:"<div align=\"center\">-</div>"}</td>
</tr>
{/foreach}
</tbody>
</table>
{else}
	{if $p.searchBtn && !$errormsg}該当データは存在しません。<br>{/if}
{/if}

{include file='footer.tpl'}
