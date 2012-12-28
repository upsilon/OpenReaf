{include file='header.tpl'}
<!-- templates rsv_02_05.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function setBillingFee()
{
	var tax = document.forma.taxRate.value-0;
	var price = document.forma.ShisetsuFee.value-0;
	var option1 = document.forma.OptionFee1.value-0;
	var option2 = document.forma.OptionFee2.value-0;
	var option3 = document.forma.OptionFee3.value-0;
	var option4 = document.forma.OptionFee4.value-0;
	var option5 = document.forma.OptionFee5.value-0;
	var bihin = document.forma.BihinFee.value-0;
	if(isZero(option1)) option1 = 0;
	if(isZero(option2)) option2 = 0;
	if(isZero(option3)) option3 = 0;
	if(isZero(option4)) option4 = 0;
	if(isNaN(option1)) option1 = 0;
	if(isNaN(option2)) option2 = 0;
	if(isNaN(option3)) option3 = 0;
	if(isNaN(option4)) option4 = 0;

	document.forma.SumFee.value = calcFeeWithTax(price+option1+option2+option3+option4+option5+bihin, tax);
	document.forma.OptionFee1.value = option1;
	document.forma.OptionFee2.value = option2;
	document.forma.OptionFee3.value = option3;
	document.forma.OptionFee4.value = option4;
}

function setPayFee()
{
	var cash = document.forma.Cash.value-0;
	var chg = document.forma.Chg.value-0;
	var ticket = document.forma.Ticket.value-0;
	var furikomi = document.forma.KouzaFurikomi.value-0;
	var others = document.forma.Others.value-0;
	var jyutou = document.forma.Jyutou.value-0;
	if(isZero(cash)) cash = 0;
	if(isZero(chg)) chg = 0;
	if(isZero(ticket)) ticket = 0;
	if(isZero(furikomi)) furikomi = 0;
	if(isZero(others)) others = 0;
	if(isNaN(cash)) cash = 0;
	if(isNaN(chg)) chg = 0;
	if(isNaN(ticket)) ticket = 0;
	if(isNaN(furikomi)) furikomi = 0;
	if(isNaN(others)) others = 0;

	var total = cash+chg+ticket+furikomi+others+jyutou;
	var KinouGaku = document.forma.KinouGaku.value-0;

	document.forma.ShiharaiFee.value = total-KinouGaku;
	document.forma.Cash.value = cash;
	document.forma.Chg.value = chg;
	document.forma.Ticket.value = ticket;
	document.forma.KouzaFurikomi.value = furikomi;
	document.forma.Others.value = others;
	document.forma.Jyutou.value = jyutou;
}

function isZero(num)
{
	if(num=='' || parseInt(num)==0) return true;
	return false;
}

function gotoSubmit()
{
	var HonYoyakuKbn = document.forma.HonYoyakuKbn.value;
	var ShiharaiFee = document.forma.ShiharaiFee.value-0;
	var ShisetsuFee = document.forma.ShisetsuFee.value-0;
	var SumFee = document.forma.SumFee.value-0;
	var KinouGaku = document.forma.KinouGaku.value-0;
	var tmpfee = SumFee - KinouGaku;
	var message = '';

	if((ShiharaiFee == 0 && ShisetsuFee == 0) || (ShiharaiFee == 0 && tmpfee == 0)) {
		message = '収納額が0円ですが、受付をしますか？';
	} else if(ShiharaiFee == 0 && ShisetsuFee > 0 && HonYoyakuKbn == '01') {
		message = '収納額が0円です。収納額を入力してください。';
		alert(message);
		return false;
	} else if(ShiharaiFee != tmpfee) {
		message = '請求額と収納額が一致しませんが、受付をしますか？';
	} else {
		message = '収納を受け付けます。よろしいですか？';
	}
	
	return confirm(message);
}

// 収納額自動計算
function ShunouKeisan()
{
	var ShisetsuFee = document.forma.ShisetsuFee.value-0;
	var option1 = document.forma.OptionFee1.value-0;
	var option2 = document.forma.OptionFee2.value-0;
	var option3 = document.forma.OptionFee3.value-0;
	var option4 = document.forma.OptionFee4.value-0;
	var option5 = document.forma.OptionFee5.value-0;
	var bihin = document.forma.BihinFee.value-0;
	var KinouGaku = document.forma.KinouGaku.value-0;
	var cash = document.forma.Cash.value-0;
	var chg = document.forma.Chg.value-0;
	var ticket = document.forma.Ticket.value-0;
	var furikomi = document.forma.KouzaFurikomi.value-0;
	var others = document.forma.Others.value-0;
	var jyutou = document.forma.Jyutou.value-0;
	var uchg = document.forma.uChg.value-0;
	var uticket = document.forma.uTicket.value-0;
	var ufurikomi = document.forma.uKouzaFurikomi.value-0;
	var uothers = document.forma.uOthers.value-0;
	if(KinouGaku == 0) {
		cash = 0;
		document.forma.Cash.value = 0;
	}
	var tax = document.forma.taxRate.value-0;
	var total = cash+chg+ticket+furikomi+others+jyutou;
	var SeikyuGaku = calcFeeWithTax(ShisetsuFee+option1+option2+option3+option4+option5+bihin, tax);
	var SyunouGaku = SeikyuGaku-KinouGaku;
	if(SeikyuGaku > total) {
		document.forma.Cash.value = SyunouGaku+cash-chg-ticket-furikomi-others+uchg+uticket+ufurikomi+uothers;
	}
	document.forma.ShiharaiFee.value = SyunouGaku;
}

// 適用ボタンクリック(減免を確定する)
function tekiyo()
{
	var basefee = document.forma.baseFeeArr.value;
	var bfArr = new Array();
	if (basefee.indexOf("-", 0) == -1) bfArr[0] = basefee;
	else bfArr = basefee.split("-");
	var genapp = document.forma.genAppArr.value;
	var gaArr = new Array();
	if(basefee.indexOf("-", 0) == -1) gaArr[0] = genapp;
	else gaArr = genapp.split("-");
	var genmen = document.forma.genmenArr.value;
	var genArr = new Array();
	if(basefee.indexOf("-", 0) == -1) genArr[0] = genmen;
	else genArr = genmen.split("-");
	var tmpGen = document.forma.Genmen.value.split(",");
	var Rate = eval(tmpGen[0]-0);
	var flg = false;
	var i, genFee = 0, nogenFee = 0;
	for (i=0; i<bfArr.length; ++i) {
		flg = false;
		if(Rate != 0 && gaArr[i].match(tmpGen[1])) {
			flg = true;
			if(tmpGen[1] == '3' && !genArr[i].match(tmpGen[2])) flg = false;
		}
		if(flg) genFee += eval(bfArr[i]);
		else nogenFee += eval(bfArr[i]);
	}
	var type = document.forma.fractionflg.value;
	var tax = document.forma.taxRate.value-0;
	var ShisetsuFee = calcFeeWithGenmen(genFee, Rate, type) + nogenFee;
	var option1 = document.forma.OptionFee1.value-0;
	var option2 = document.forma.OptionFee2.value-0;
	var option3 = document.forma.OptionFee3.value-0;
	var option4 = document.forma.OptionFee4.value-0;
	var option5 = document.forma.OptionFee5.value-0;
	var bihin = document.forma.BihinFee.value-0;
	document.forma.ShisetsuFee.value = ShisetsuFee;
	document.forma.SumFee.value = calcFeeWithTax(ShisetsuFee+option1+option2+option3+option4+option5+bihin, tax);
}
// -->
</script>
{/literal}

{if $message}
<body onload="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
収納管理 &gt; <a href="index.php?op=rsv_01_04_search&back=1">使用料等受付/使用許可</a> &gt; <strong><u>受付</u></strong>
</div>

<h2 class="subtitle01">受付</h2>

<input type="button" name="backBtn" onclick="location.href='index.php?op=rsv_01_04_search&back=1';" value="戻る" />

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_02_05_receipt">
<input type="hidden" name="YoyakuNum" value="{$rec.yoyakunum}">
<input type="hidden" name="HonYoyakuKbn" value="{$rec.honyoyakukbn}">
<input type="hidden" name="ShowDanjyoNinzuFlg" value="{$rec.ShowDanjyoNinzuFlg}">
<input type="hidden" name="baseFeeArr" value="{$rec.baseFeeArr}">
<input type="hidden" name="genAppArr" value="{$rec.genAppArr}">
<input type="hidden" name="genmenArr" value="{$rec.genmenArr}">
<input type="hidden" name="fractionflg" value="{$rec.fractionflg}">
<input type="hidden" name="taxRate" value="{$taxRate|default:0}">
<input type="hidden" name="BaseShisetsuFee" value="{$rec.BaseShisetsuFee}">
<input type="hidden" name="KinouGaku" value="{$rec.KinouGaku}" >
<input type="hidden" name="BihinNum" value="{$rec.BihinNum}" >
<br>
<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="100">予約番号</th>
	<td>{$rec.yoyakunum}</td>
	<th>予約受付</th>
	<td>{$rec.DaikouStaffName}</td>
</tr>
<tr>
	<th>利用者ID</th>
	<td width="200">{$rec.userid}</td>
	<th width="100">利用者名</th>
	<td width="320" nowrap>{$rec.namesei}　({$rec.nameseikana})
{if $rec.userid === $smarty.const._UNREGISTED_USER_ID_}
		<br>
		<table class="none-table">
		<tr>
			<th>氏名</th>
			<td>{$rec.unreg_name}</td>
		</tr>
		<tr>
			<th>住所</th>
			<td>{$rec.unreg_address}</td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td>{$rec.unreg_tel}</td>
		</tr>
		<tr>
			<th>連絡先</th>
			<td>{$rec.unreg_contact}</td>
		</tr>
		</table>
{/if}
	</td>
</tr>
<tr>
	<th>利用日時</th>
	<td colspan="3">{$rec.UseDateView}　{$rec.UseTime}</td>
</tr>
<tr>
	<th>利用施設</th>
	<td colspan="3">{$rec.ShisetsuName}&nbsp;{$rec.shitsujyoname}</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>
		<select name="MokutekiCode">
		{html_options options=$aPurpose selected=$rec.mokutekicode}
		</select>
	</td>
	<th>利用人数</th>
	<td nowrap>
	{if $rec.ShowDanjyoNinzuFlg}
	<input type="hidden" name="useninzu" value="{$rec.useninzu}">
	{$aNinzu.ninzu1[0]}<input type="text" name="ninzu1" value="{$rec.ninzu1}" maxlength="6" style="width:40px;text-align:right;ime-mode:disabled;">&nbsp;人&nbsp;
	{$aNinzu.ninzu2[0]}<input type="text" name="ninzu2" value="{$rec.ninzu2}" maxlength="6" style="width:40px;text-align:right;ime-mode:disabled;">&nbsp;人
	{else}
	<input type="text" name="useninzu" value="{$rec.useninzu}" maxlength="6" style="width:40px;text-align:right;ime-mode:disabled;">&nbsp;人
	<input type="hidden" name="ninzu1" value="{$rec.ninzu1}">
	<input type="hidden" name="ninzu2" value="{$rec.ninzu2}">
	{/if}
	<input type="hidden" name="ninzu3" value="{$rec.ninzu3}">
	<input type="hidden" name="ninzu4" value="{$rec.ninzu4}">
	<input type="hidden" name="ninzu5" value="{$rec.ninzu5}">
	<input type="hidden" name="ninzu6" value="{$rec.ninzu6}">
	<input type="hidden" name="ninzu7" value="{$rec.ninzu7}">
	<input type="hidden" name="ninzu8" value="{$rec.ninzu8}">
	<input type="hidden" name="ninzu9" value="{$rec.ninzu9}">
	<input type="hidden" name="ninzu10" value="{$rec.ninzu10}">
	<input type="hidden" name="ninzu11" value="{$rec.ninzu11}">
	<input type="hidden" name="ninzu12" value="{$rec.ninzu12}">
	<input type="hidden" name="ninzu13" value="{$rec.ninzu13}">
	<input type="hidden" name="ninzu14" value="{$rec.ninzu14}">
	<input type="hidden" name="ninzu15" value="{$rec.ninzu15}">
	<input type="hidden" name="ninzu16" value="{$rec.ninzu16}">
	</td>
</tr>
</table>
</div>
<div class="margin-box">
<table class="itemtable02">
<tr>
	<th colspan="3">料金区分</th>
	<th colspan="3">減免</th>
</tr>
<tr>
	<td colspan="3" align="center">{$rec.UseKbnName|default:'----'}
	{if $rec.ExtName != ''}&nbsp;(割増&nbsp;&nbsp;{$rec.ExtName}&nbsp;{$rec.ExtRate}%){/if}
	</td>
	<td colspan="3" align="center">
	<select name="Genmen" onchange="tekiyo();">
		<option value="">減免なし【0%】</option>
		{html_options options=$aGenmen selected=$rec.Genmen}
	</select>
	</td>
</tr>
<tr>
	<th width="120">基本施設使用料</th>
	<th width="120">施設使用料</th>
	<th width="120">調整額</th>
	<th width="120">請求額</th>
	<th width="120">収納額</th>
	<th width="120">納付済額</th>
</tr>
<tr>
	<td align="right">
	{$rec.BaseShisetsuFee}&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="ShisetsuFee" value="{$rec.ShisetsuFee}" class="textBox_r" style="width:72px;text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="OptionFee4" value="{$rec.OptionFee4}" onchange="setBillingFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="SumFee" maxlength="9" value="{$rec.SumFee}" class="textBox_r" style="width:72px;text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="ShiharaiFee" type="text" value="{$rec.ShunouzumiFee|default:'0'}" class="textBox_r" style="width:72px;text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	{$rec.KinouGaku}&nbsp;円&nbsp;
	</td>
</tr>
<tr>
	<th>調整理由</th>
	<td colspan="5">
	<input type="text" name="ChouseiRiyuu" value="{$rec.ChouseiRiyuu}" maxlength="128" style="width:596px;ime-mode:active;">
	</td>
</tr>
{if $smarty.const._USE_OPTIONFEE_}
<tr>
	<th>{$aOptionFee[1]}</th>
	<th>{$aOptionFee[2]}</th>
	<th>{$aOptionFee[3]}</th>
	<th>{$aOptionFee[5]}</th>
	<th>&nbsp;</th>
	<td rowspan="2" align="center"><input type="button" name="addfeeBtn" value="加算・調整" onclick="submitTo(this.form, 'rsv_03_11_option');"></td>
</tr>
<tr>
	<td align="right">
	<input type="text" name="OptionFee1" value="{$rec.OptionFee1}" onchange="setBillingFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="OptionFee2" value="{$rec.OptionFee2}" onchange="setBillingFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="OptionFee3" value="{$rec.OptionFee3}" onchange="setBillingFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="OptionFee5" value="{$rec.OptionFee5}" class="textBox_r" style="width:72px;text-align:right;" readonly="true">&nbsp;円&nbsp;
	</td>
	<td>&nbsp;</td>
</tr>
{else}
	<input type="hidden" name="OptionFee1" value="{$rec.OptionFee1}">
	<input type="hidden" name="OptionFee2" value="{$rec.OptionFee2}">
	<input type="hidden" name="OptionFee3" value="{$rec.OptionFee3}">
	<input type="hidden" name="OptionFee5" value="{$rec.OptionFee5}">
{/if}
</table>
<input type="hidden" name="BihinFee" value="{$rec.BihinFee}">
<br>
<table class="itemtable02">
<tr>
	<th colspan="6">収納内訳</th>
</tr>
<tr>
	<th width="120">{$aKinshu['01']}</th>
	<th width="120">{$aKinshu['02']}</th>
	<th width="120">{$aKinshu['03']}</th>
	<th width="120">{$aKinshu['04']}</th>
	<th width="120">{$aKinshu['05']}</th>
	<th width="120">{$aKinshu['06']}</th>
</tr>
<tr>
	<td align="right">
	<input type="text" name="Cash" value="{$rec.Receipt[1]}" onchange="setPayFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="hidden" name="uChg" value="{$rec.Receipt[2]}">
	<input type="text" name="Chg" value="{$rec.Receipt[2]}" onchange="setPayFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="hidden" name="uTicket" value="{$rec.Receipt[3]}">
	<input type="text" name="Ticket" value="{$rec.Receipt[3]}" onchange="setPayFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="hidden" name="uKouzaFurikomi" value="{$rec.Receipt[4]}">
	<input type="text" name="KouzaFurikomi" value="{$rec.Receipt[4]}" onchange="setPayFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="hidden" name="uOthers" value="{$rec.Receipt[5]}">
	<input type="text" name="Others" value="{$rec.Receipt[5]}" onchange="setPayFee();" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="Jyutou" value="{$rec.Receipt[6]}" maxlength="9" class="textBox_r" style="width:72px;text-align:right;" readonly="true">&nbsp;円&nbsp;
	</td>
</tr>
<tr>
	<th>収納日</th>
	<td colspan="2">
	{html_select_date prefix='Rec' start_year='-2' end_year='+2' display_months=false display_days=false time=$datetime}年
	{html_select_date prefix='Rec' display_years=false display_days=false month_format='%m' time=$datetime}月
	{html_select_date prefix='Rec' display_years=false display_months=false day_value_format='%02d' time=$datetime}日<br>
	</td>
	<th>受付場所</th>
	<td colspan="2">
	<select name="ReceptPlace">
	{html_options options=$aShisetsu selected=$rec.ReceptPlace}
	</select>
	</td>
</tr>
<tr>
	<td colspan="6" class="no-border" align="center">
	<input type="button" name="calcBtn" value="収納額計算" class="btn-01" onclick="ShunouKeisan();">
	<input type="submit" name="addBtn" value="登録" class="btn-01" onclick="return gotoSubmit();">
	<input type="button" name="backBtn" class="btn-01" onclick="location.href='index.php?op=rsv_01_04_search&back=1';" value="戻る" />
	</td>
</tr>
</table>
</div>
</form>

{include file='footer.tpl'}
