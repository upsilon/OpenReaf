{include file='header.tpl'}
<!-- templates rsv_04_02_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function goNext(obj)
{
	var msg = '';

	if(obj.UserID.value == '') {
		msg += "「利用者ID」を指定してください。\n";
	}
	if (obj.useCheckFlag.value != '1') {
		msg += "「呼び出し」ボタンを押してください。\n";
	}
	if (msg != '') {
		alert(msg);
		return false;
	}
	return true;
}

function changeUserID()
{
	document.forma.UserJyoutai.value = '';
	document.forma.useCheckFlag.value = '0';
}

function callUserID()
{
	document.forma.callUser.value = 1;
	document.forma.submit();
}

function submitUserID(UseDate)
{
	var userid = document.forma.UserID.value;

	if (userid == '') {
		alert('利用者IDを入力してください。');
		return false;
	}

	var url = 'index.php?op=ajax&cmd=user&id='+userid+'&date='+UseDate;

	getByAjax(url, setUserStatus);

	return true;
}
//-->
</script>
{/literal}

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt; 
<a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt; 
<a href="index.php?op=rsv_02_02_status">空き状況表示</a> &gt; 
<a href="index.php?op=rsv_03_01_status&back=1">空き状況詳細表示</a> &gt; 
<u><strong>予約情報入力</strong></u>
</div>

<h2 class="subtitle01">予約情報入力</h2>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_04_02_input">
<input type="hidden" name="mode" value="">
<input type="hidden" name="useCheckFlag" value="{$info.usecheckflag}">
<input type="hidden" name="UseKbn" value="{$info.usekbn}">
<input type="hidden" name="callUser" value="0">

<h4 class="subtitle02">予約申込内容</h4>

<div class="margin-box">
{if $message}<div id="errorbox">{$message}</div>{/if}
<table class="itemtable02">
<tr height="25">
	<th width="100">日時</th>
	<td width="200">
		{$aMain.DateView}&nbsp;&nbsp;{$aMain.UseTimeFromView}〜{$aMain.UseTimeToView}
	</td>
	<th width="100">施設</th>
	<td width="320" nowrap>{$aMain.ShisetsuName}&nbsp;{$aMain.ShitsujyoName}&nbsp;{$aMain.MenName}</td>
</tr>
<tr height="29">
	<th>付属室場</th>
	<td colspan="3" width="620" id="choice_fuzoku">
		{foreach $aFuzoku as $key => $value}
		<label><input type="checkbox" name="FuzokuCode[{$key}]" value="{$value.fuzokucode}"
		{if $value.arr_flg}disabled="true"{/if}
		{if $value.fuzokucode == $info.FuzokuCode[$key]}checked{/if}>
		{$value.shitsujyoname}</label>
		{/foreach}
	</td>
</tr>
<tr>
	<th>利用目的</th>
	<td valign="middle">
		<select name="MokutekiCode">
		{html_options options=$aPurpose selected=$info.MokutekiCode}
		</select>
	</td>
	<th>利用者人数</th>
	<td nowrap>
	{if $aMain.ShowDanjyoNinzuFlg}
		<input type="hidden" name="useninzu" value="0">
		{$aNinzu.ninzu1[0]}&nbsp;<input type="text" name="ninzu1" size="4" maxlength="4" value="{$info.ninzu1|default:''}" style="text-align:right;ime-mode:disabled;" />&nbsp;人&nbsp;
		{$aNinzu.ninzu2[0]}&nbsp;<input type="text" name="ninzu2" size="4" maxlength="4" value="{$info.ninzu2|default:''}" style="text-align:right;ime-mode:disabled;" />&nbsp;人
	{else}
		<input type="text" name="useninzu" size="8" maxlength="6" value="{$info.useninzu|default:''}" style="text-align:right;ime-mode:disabled;" />&nbsp;人
		<input type="hidden" name="ninzu1" value="{$info.ninzu1|default:'0'}">
		<input type="hidden" name="ninzu2" value="{$info.ninzu2|default:'0'}">
	{/if}
	<input type="hidden" name="ninzu3" value="{$info.ninzu3|default:'0'}">
	<input type="hidden" name="ninzu4" value="{$info.ninzu4|default:'0'}">
	<input type="hidden" name="ninzu5" value="{$info.ninzu5|default:'0'}">
	<input type="hidden" name="ninzu6" value="{$info.ninzu6|default:'0'}">
	<input type="hidden" name="ninzu7" value="{$info.ninzu7|default:'0'}">
	<input type="hidden" name="ninzu8" value="{$info.ninzu8|default:'0'}">
	<input type="hidden" name="ninzu9" value="{$info.ninzu9|default:'0'}">
	<input type="hidden" name="ninzu10" value="{$info.ninzu10|default:'0'}">
	<input type="hidden" name="ninzu11" value="{$info.ninzu11|default:'0'}">
	<input type="hidden" name="ninzu12" value="{$info.ninzu12|default:'0'}">
	<input type="hidden" name="ninzu13" value="{$info.ninzu13|default:'0'}">
	<input type="hidden" name="ninzu14" value="{$info.ninzu14|default:'0'}">
	<input type="hidden" name="ninzu15" value="{$info.ninzu15|default:'0'}">
	<input type="hidden" name="ninzu16" value="{$info.ninzu16|default:'0'}">
	</td>
</tr>
<tr>
	<th>予約区分</th>
	<td colspan="3">
		{strip}
		<select name="YoyakuKbn">
		{html_options options=$aYoyakuKbn selected=$info.YoyakuKbn|default:'02'}
		</select>
		<input type="hidden" name="ShinsaFlg" value="0">
		{/strip}
	</td>
</tr>
<tr>
	<th>催事名</th>
	<td colspan="3">
		<input type="text" name="YoyakuName" size="44" maxlength="50" value="{$info.YoyakuName|default:''}" />
	</td>
</tr>
<tr>
	<th>備考</th>
	<td colspan="3">
	<textarea name="Bikou" rows="3" cols="60" wrap="physical" style="ime-mode:active;">{$info.Bikou|default:''}</textarea>
	</td>
</tr>
</table>
</div>

<h4 class="subtitle02">利用者情報&nbsp;<input type="button" name="Submit3" value="利用者検索" onclick="openUserList();" /></h4>

<div class="margin-box">
<table width="720" class="itemtable02">
<tr>
	<th width="100">利用者ID</th>
	<td>
		<input type="text" name="UserID" size="12" maxlength="16" value="{$info.userid|default:''}" style="ime-mode:disabled;" onchange="changeUserID();">
		<input type="button" name="callUserBtn" value="呼び出し" onclick="callUserID();">
		<input type="button" name="Submit32" value="→詳細表示" onclick="gotoUserPage(this.form);">
		<input name="UserJyoutai" type="text" size="48" value="{$info.userjyoutai}" class="textBox_r_red" readonly>
		<input type="hidden" name="UserJyoutaiKbn" value="{$info.userjyoutaikbn}">
	</td>
</tr>
<tr>
	<th>利用者名</th>
	<td>
		<table class="none-table">
		<tr>
			<th colspan="2">氏名（団体の場合は団体名）</th>
		</tr>
		<tr>
			<th>漢字</th>
			<td>
				<input type="text" name="NameSei" size="80" maxlength="128" value="{$info.namesei|default:''}" readonly />
			</td>
		</tr>
		<tr>
			<th>{$smarty.const._KANA_}</th>
			<td>
				<input type="text" name="NameSeiKana" size="80" maxlength="128" value="{$info.nameseikana|default:''}" readonly />
			</td>
		</tr>
		</table>
	</td>
</tr>
{if $info.UserID === $smarty.const._UNREGISTED_USER_ID_}
<tr>
	<th>未登録者情報</th>
	<td>
		<table class="none-table">
		<tr>
			<th>氏名</th>
			<td>
			<input type="text" name="UnregUserName" value="{$info.UnregUserName}" size="80" maxlength="128" style="ime-mode:active;" >
			</td>
		</tr>
		<tr>
			<th>住所</th>
			<td>
			<input type="text" name="UnregAddress" value="{$info.UnregAddress}" size="80" maxlength="128" style="ime-mode:active;" >
			</td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td>
			<input type="text" name="UnregTel" value="{$info.UnregTel}" size="20" maxlength="16" style="ime-mode:disabled;" >&nbsp;ハイフン区切り
			</td>
		</tr>
		<tr>
			<th>連絡先</th>
			<td>
			<input type="text" name="UnregContact" value="{$info.UnregContact}" size="20" maxlength="16" style="ime-mode:disabled;" >&nbsp;ハイフン区切り
			</td>
		</tr>
		</table>
	</td>
</tr>
{/if}
</table>
<br />

<table width="226" height="25">
<tr align="center">
	<td>
	<input type="submit" name="nextBtn" value="次へ" onclick="return goNext(this.form);">
	</td>
	<td>
	<input type="button" name="backBtn" value="戻る" onclick="location.href='index.php?op=rsv_03_01_status&back=1';" />
	</td>
	<td>
	<input type="button" name="cancelBtn" value="取消" onclick="location.href='index.php?op=rsv_01_02_search&back=1';" />
	</td>
</tr>
</table>
</div>

</form>

{include file='footer.tpl'}
