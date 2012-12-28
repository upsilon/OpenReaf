{include file='header.tpl'}
<!-- templates stf_02_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
// 施設コードに関連する室場『名』の配列を返す
function getAryShitsujyoCode(argShisetsuCode)
{
	{/literal}
	{foreach $aShitsujyo as $rowShitsetsu}
		var aryText{$rowShitsetsu.ShisetsuCode} = new Array({$rowShitsetsu.strShitsujyoName});
	{/foreach}
	{literal}

	return eval( "aryText" + argShisetsuCode) ;
}

// 施設コードに関連する室場『値』の配列を返す
function getAryShitsujyoValue(argShisetsuCode)
{
	{/literal}
	{foreach $aShitsujyo as $rowShitsetsu}
		var aryValue{$rowShitsetsu.ShisetsuCode} = new Array({$rowShitsetsu.strShitsujyoCode});
	{/foreach}
	{literal}

	return eval( "aryValue" + argShisetsuCode) ;
}

// swapOptions 関数にて使用されている。
function fnc_showRooms()
{
	// 施設セレクトボックスで選択されている値を取得する。
	var selectedValue = document.getElementById('idfacilities').value;
	
	var aryText  = getAryShitsujyoCode( selectedValue );
	var aryValue = getAryShitsujyoValue( selectedValue );
	
	document.formx.rooms.length = aryValue.length ;
	
	for (loop=0; loop < aryValue.length; loop++)
	{
		document.formx.rooms.options[loop].text  = aryText[loop];
		document.formx.rooms.options[loop].value = aryValue[loop];
	}
}

// 選択されている『施設』を対象施設に追加する。
function addUserFacility()
{
	var roomCount = document.formx.rooms.options.length;
	if (roomCount == 0) {
		alert('室場が未登録の施設は追加できません。');
		return;
	}

	var sw = 0;
	var count = document.formx.elements['userfacilities[]'].options.length;

	// 施設が選択されていない場合は処理しない。
	objString = new String(document.getElementById('idfacilities').value);
	if ( objString < 1 ) {
		return true ;
	}

	for (loop=0; loop < count; loop++ )
	{
		// すでに対象施設に追加されているかどうかをチェックする。
		if ( document.formx.elements['userfacilities[]'].options[loop].text == document.formx.facilities.options[document.formx.facilities.options.selectedIndex].text ) {
			sw = 1;
		}
	}
	if (sw == 0)
	{
		var nextIndex = document.formx.elements['userfacilities[]'].options.length++;
		document.formx.elements['userfacilities[]'].options[nextIndex].value 
		= document.formx.facilities.options[document.formx.facilities.options.selectedIndex].value;
		document.formx.elements['userfacilities[]'].options[nextIndex].text 
		= document.formx.facilities.options[document.formx.facilities.options.selectedIndex].text;
	}  
}

// 選択されている『室場』を対象施設に追加する。
function addUserRoom()
{
	var sw = 0;
	var count = document.formx.elements['userfacilities[]'].options.length;
	if (document.formx.facilities.options.selectedIndex<0) {
	  return;
	}
	if (document.formx.rooms.options.selectedIndex<0) {
		return;
	}
	var nowSelectedText 
	  = document.formx.facilities.options[document.formx.facilities.options.selectedIndex].text
	  + '　'
	  + document.formx.rooms.options[document.formx.rooms.options.selectedIndex].text
		
	var nowSelectedValue 
	  = document.formx.facilities.options[document.formx.facilities.options.selectedIndex].value
	  + ':'
	  + document.formx.rooms.options[document.formx.rooms.options.selectedIndex].value
		
	for (loop=0; loop < count; loop++ )
	{
		// すでに対象施設に追加されているかどうかをチェックする。
		if ( document.formx.elements['userfacilities[]'].options[loop].value == nowSelectedValue ) {
			sw = 1;
		}
	}
	
	// 対象施設にまだ追加されていない場合
	if (sw == 0) {
		var nextIndex = document.formx.elements['userfacilities[]'].options.length++;
		
		document.formx.elements['userfacilities[]'].options[nextIndex].value = nowSelectedValue ;
		document.formx.elements['userfacilities[]'].options[nextIndex].text = nowSelectedText ;
	}  
}

// 対象施設 から削除する
function deleteUserFacilitys()
{
	var nowIndex = document.formx.elements['userfacilities[]'].options.selectedIndex ;
	if (nowIndex>=0){
		document.formx.elements['userfacilities[]'].options[nowIndex] = null;
	}
}

// 登録ボタンを押したときに確認ダイヤログを表示する。
function fnc_confirm(msg)
{
	var count = document.formx.elements['userfacilities[]'].options.length ;
	if (count < 1) {
		if (confirm("対象施設が割り当てられていません。\n業務権限の予約管理は強制的にチェックが外れます。\n登録しますか？")) {
			selectAllUserfacilities() ;
			return true;
		} else {
			return false;
		}
	}

	if (confirm(msg)) {
		selectAllUserfacilities() ;
		return true;
	} else {
		return false;
	}
}

// 送信するときに対象施設のセレクトボックスの値をすべて選択する
// fnc_confirm() 内で呼び出される。
function selectAllUserfacilities()
{
	// 対象施設のセレクトボックスのオプションの数
	var count = document.formx.elements['userfacilities[]'].options.length ;
	for ( i=0; i<count; i++ )
	{
		document.formx.elements['userfacilities[]'].options[i].selected = true;
	}
}

function checkFromTourokuKbn(mode)
{
	if (mode==3)
	{
		document.formx.kengencode1.checked=true;
		document.formx.kengencode2.checked=true;
		document.formx.kengencode3.checked=true;
		document.formx.kengencode4.checked=true;
		document.formx.kengencode5.checked=true;
		//document.formx.kengencode6.checked=true;
	}
	if (mode==2)
	{
		document.formx.kengencode1.checked=false;
		document.formx.kengencode2.checked=true;
		document.formx.kengencode3.checked=false;
		document.formx.kengencode4.checked=true;
		document.formx.kengencode5.checked=true;
		//document.formx.kengencode6.checked=true;
	}
	if (mode==1)
	{
		document.formx.kengencode1.checked=false;
		document.formx.kengencode2.checked=false;
		document.formx.kengencode3.checked=false;
		document.formx.kengencode4.checked=true;
		document.formx.kengencode5.checked=true;
		//document.formx.kengencode6.checked=true;
	}
}
//-->
</script>
{/literal}

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
職員管理&nbsp;&gt;&nbsp; 
<a href="index.php?op=stf_01_01_top">職員一覧</a>&nbsp;&gt;&nbsp; 
<strong><u>職員{if $mode == 'reg'}情報登録{elseif $mode == 'mod'}情報変更{else}情報照会{/if}</u></strong>
</div>

<h2 class="subtitle01">職員{if $mode == 'reg'}情報登録{elseif $mode == 'mod'}情報変更{else}情報照会{/if}</h2>

<div class="margin-box">
<input type="button" class="backBtn" value="一覧へ戻る" onClick="location.href='index.php?op=stf_01_01_top';">
<br>

{if $message}<div id="errorbox">{$message}</div><br>{/if}

<form name="formx" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">

<table width="550">
<tr>
	<td width="110">・職員ID</td>
	<td>
	<input type="text" name="staffid" value="{$para.staffid}" size="12" maxlength="10" {if $err.StaffID}class="error"{/if} {if $mode == 'ref' || $mode == 'mod'}readonly="true" class="textBox_r"{else}style="ime-mode:disabled;"{/if}>
	</td>
	<td width="80" nowrap>・職員名</td>
	<td>
	<input type="text" name="staffname" value="{$para.staffname}" size="20" maxlength="20" {if $err.StaffName}class="error"{/if} {if $mode == 'ref'}readonly="true" class="textBox_r"{else}style="ime-mode:active;"{/if}>
	</td>
</tr>
<tr>
	<td>・所属部署</td>
	<td width="170">
	{if $mode == 'ref'}
	{foreach $aBusho as $key => $value}
	{if $key == $para.bushocode}<input type="text" value="{$value}"readonly="true" class="textBox_r">{/if}
	{/foreach}
	{else}
	<select name="bushocode">
	{html_options options=$aBusho selected=$para.bushocode}
	</select>
	{/if}
	</td>
	<td nowrap>・職員番号</td>
	<td width="170">
	<input type="text" name="staffnum" value="{$para.staffnum}"size="20" maxlength="20" {if $err.StaffNum}class="error"{/if} {if $mode == 'ref'}readonly="true" class="textBox_r"{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
<tr>
	<td>・適用開始日</td>
	<td colspan="3">
	  <input type="text" name="appdatefrom" value="{$para.appdatefrom}" size="20" maxlength="20" {if $err.AppDateFrom}class="error"{/if} {if $mode == 'ref'}readonly="true" class="textBox_r"{else}style="ime-mode:disabled;"{/if}>
	  (入力例：{$smarty.const._EXAMPLE_DATE_})
	</td>
</tr>
<tr>
	<td>・パスワード</td>
	<td colspan="3">
	  <input type="password" name="pwd" value="{$para.pwd}" size="16" maxlength="16" {if $err.Pwd}class="error"{/if} {if $mode == 'ref'}readonly="true" class="textBox_r"{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
{if $mode != 'ref'}
<tr>
	<td nowrap>・パスワード(確認用)</td>
	<td colspan="3">
	  <input type="password" name="pwd2" value="{$para.pwd2}" size="16" maxlength="16" {if $err.Pwd2}class="error"{/if} {if $mode == 'ref'}readonly="true" class="textBox_r"{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
{else}
<tr>
	<td>・廃止日</td>
	<td colspan="3">
	  <input type="text" name="haishidate" value="{$para.haishidate}" size="20" maxlength="20" readonly="true" class="textBox_r">
	</td>
</tr>
{/if}
<tr>
	<td>・登録区分</td>
	<td colspan="3" {if $err.TourokuKbn && $SystemMOn}class="error"{/if}>
	<input type="radio" {if $err.TourokuKbn && $SystemMOn}class="error"{/if}
		name="tourokukbn" value="3" id="btn_01"
		{if $para.tourokukbn eq '3'} checked{/if}
		{if $mode == 'ref' || !$SystemMOn} disabled class="textBox_r"{else} onClick="checkFromTourokuKbn(3);"{/if}>
	<label for="btn_01">システム管理者</label>
	&nbsp;
	<input type="radio" {if $err.TourokuKbn && $ShisetsuMOn}class="error"{/if}
		name="tourokukbn" value="2" id="btn_02"
		{if $para.tourokukbn eq '2'} checked{/if}
		{if $mode == 'ref' || !$ShisetsuMOn} disabled class="textBox_r"{else} onClick="checkFromTourokuKbn(2);"{/if}>
	<label for="btn_02">施設管理者</label>
	&nbsp;
	<input type="radio" {if $err.TourokuKbn && $ShisetsuCOn}class="error"{/if}
		name="tourokukbn" value="1" id="btn_03"
		{if $para.tourokukbn eq '1'} checked{/if}
		{if $mode == 'ref' || !$ShisetsuCOn} disabled class="textBox_r"{else} onClick="checkFromTourokuKbn(1);"{/if}>
	<label for="btn_03">施設担当者</label>
	</td>
</tr>
</table>

<table width="500">
<tr>
	<td width="110">・業務権限</td>
	<td>職員管理</td>
	<td>
	<input type="checkbox" name="kengencode1" {if $para.kengencode1 == '10'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="10" id="chk_01">
	<label for="chk_01">職員登録</label></td>
	<td>予約管理</td>
	<td>
	<input type="checkbox" name="kengencode4" {if $para.kengencode4 == '40'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="40" id="chk_04" >
	<label for="chk_04">予約受付</label></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>利用者管理</td>
	<td>
	<input type="checkbox" name="kengencode2" {if $para.kengencode2 == '20'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="20" id="chk_02">
	<label for="chk_02">利用者登録</label></td>
	<td>収納管理</td>
	<td>
	<input type="checkbox" name="kengencode5" {if $para.kengencode5 == '50'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="50" id="chk_05">
	<label for="chk_05">使用料等受付</label><td>
</tr>
<tr>
	<td></td>
	<td>施設管理</td>
	<td>
	<input type="checkbox" name="kengencode3" {if $para.kengencode3 == '30'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="30" id="chk_03">
	<label for="chk_03">施設登録</label></td>
	<td colspan="2">&nbsp;</td>
{*
	<td>予約管理</td>
	<td>
	<input type="checkbox" name="kengencode6" {if $para.kengencode6 == '60'}checked{/if} {if $mode == 'ref'}disabled class="textBox_r"{/if} value="60" id="chk_06">
	<label for="chk_06">利用受付</label>
	</td>
*}
</tr>
</table>
<br />

<table width="500">
<tr>
	<td>&lt;&lt;選択可能施設&gt;&gt;<br>
	<select name="facilities" id="idfacilities" size="5" class="facilities-select" onChange="fnc_showRooms()" {if $mode == 'ref'} disabled {/if}>
	  {html_options options=$aShisetsu selected=$tplShisetsuSelected}
	</select>
	</td>
	<td width="64" height="89" align="center">
	<input type="button" name="btn_add" value="追加&gt;" onclick="addUserFacility()" {if $mode == 'ref'}disabled{/if}>
	<br>
	<br>
	<input type="button" name="btn_del" value="&lt;削除" onclick="deleteUserFacilitys()" {if $mode == 'ref'}disabled{/if}>
	</td>
	<td width="212" rowspan="2" {$err.UserFacilities}>
	&lt;&lt;対象施設&gt;&gt;<br>
	<select multiple name="userfacilities[]" id="iduserfacilities" size="13" class="facilities-select" {if $mode == 'ref'}disabled{/if}>
	  {html_options options=$para.userfacilities}
	</select>
	</td>
</tr>
<tr>
	<td>
	&lt;&lt;選択可能室場&gt;&gt;<br>
	<select name="rooms" id="idrooms" size="5" class="facilities-select" {if $mode == 'ref'} disabled {/if}>
	</select>
	</td>
	<td>
	  <input type="button" name="btn_add" value="追加&gt;" onclick="addUserRoom()" {if $mode =='ref'}disabled{/if}>
	  <br>
	  <br>
	  <input type="button" name="btn_del" value="&lt;削除" onclick="deleteUserFacilitys()" {if $mode == 'ref'}disabled{/if}>
	</td>
</tr>
<tr>
	<td colspan="3" align="center">
	<br>
	{if $mode == 'reg'}
	<input type="submit" name="insertBtn" value="登録" {if $success == 1}disabled{/if} onclick="return fnc_confirm('登録しますか？');" >
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="btn_top" type="reset" value="クリア">
	{elseif $mode == 'mod'}
	<input type="submit" name="updateBtn" value="変更" onclick="return fnc_confirm('変更しますか？');">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="btn_top" type="reset" value="クリア">
	{/if}
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
