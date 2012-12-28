{include file='header.tpl'}
<!-- templates rsv_02_04_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function setCancelFee()
{
	var CancelReason = document.forma.elements['CancelReason'].value;
	if (CancelReason == '') {
		alert('取消事由を選択してください。');
		return;
	}

	var ShisetsuFee = document.forma.elements['ShisetsuFee'].value -0;
	var Rate = explodeRate(CancelReason);
	var type = document.forma.elements['fractionflg'].value;
	var CancelFee = calcFeeWithGenmen(ShisetsuFee, Rate, type);
	document.forma.elements['CancelFee'].value = CancelFee;
	document.forma.elements['SumFee'].value = CancelFee;
	Rate = 100 - Rate;
	var msg = 'キャンセル料はかかりません。';
	if (0 < Rate) {
		msg = 'キャンセル料は施設使用料の '+Rate+'%です。';
	}
	document.getElementById('CancelRate').innerHTML = msg;
}

//-------------------------
// 支払額を算出
//-------------------------
function setPayFee()
{
	// 内訳
	var cash = document.forma.Cash.value;
	var chg = document.forma.Chg.value;
	var ticket = document.forma.Ticket.value;
	var furikomi = document.forma.KouzaFurikomi.value;
	var others = document.forma.Others.value;
	var jyutou = document.forma.Jyutou.value;
	
	// ゼロチェック
	if (isZero(cash)) cash = 0;
	if (isZero(chg)) chg = 0;
	if (isZero(ticket)) ticket = 0;
	if (isZero(furikomi)) furikomi = 0;
	if (isZero(others)) others = 0;
	if (isZero(jyutou)) jyutou = 0;

	// 数値チェック
	if (isNaN(cash)) {
		alertNum();
		document.forma.Cash.value = 0;
		return;
	}
	if (isNaN(chg)) {
		alertNum();
		document.forma.Chg.value = 0;
		return;
	}
	if (isNaN(ticket)) {
		alertNum();
		document.forma.Ticket.value = 0;
		return;
	}
	if (isNaN(furikomi)) {
		alertNum();
		document.forma.KouzaFurikomi.value = 0;
		return;
	}
	if (isNaN(others)) {
		alertNum();
		document.forma.Others.value = 0;
		return;
	}
	if (isNaN(jyutou)) {
		alertNum();
		document.forma.Jyutou.value = 0;
		return;
	}

	// 合計を計算
	var total = parseInt(cash) + parseInt(chg) + parseInt(ticket)
		+ parseInt(furikomi) + parseInt(others) + parseInt(jyutou);

	// 設定終了

	document.forma.ShiharaiFee.value = total; // 合計を表示
	document.forma.Cash.value = cash;
	document.forma.Chg.value = chg;
	document.forma.Ticket.value = ticket;
	document.forma.KouzaFurikomi.value = furikomi;
	document.forma.Others.value = others;
	document.forma.Jyutou.value = jyutou;
}

function alertNum()
{
	alert("半角数値を入力してください。");
}

function isZero(num)
{
	if (num=='' || parseInt(num)==0) return true;
	return false;
}

// フォーム送信
function confirmCancel()
{
	var CancelReason = document.forma.elements['CancelReason'].value;
	if (CancelReason == '') {
		alert('取消事由を選択してください。');
		return false;
	}
	return confirm('予約を取消します。よろしいですか？');
}

function explodeRate( n )
{
	var gen = n.split(",");
	return eval(gen[0]-0);
}

// 収納額自動計算
function ShunouKeisan()
{
	var CancelFee = 0;
	var KinouGaku = 0;
	var Cash = 0;
	var SyunouGaku = 0;

	CancelFee = document.forma.CancelFee.value - 0;
	KinouGaku = document.forma.KinouGaku.value - 0;
	Cash = document.forma.Cash.value - 0;
	if (KinouGaku == 0) Cash = 0;
	if (CancelFee > KinouGaku) {
		SyunouGaku = CancelFee - KinouGaku;
	}
	document.forma.Cash.value = SyunouGaku + Cash;
	document.forma.ShiharaiFee.value = SyunouGaku;
}
// -->
</script>
{/literal}

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
{if $fmode=='rsv_02_07'}
	予約管理 &gt; <a href="index.php?op=rsv_01_03_search&back=1">予約状況確認/変更/取消</a> &gt; <a href="{$returnUrl}">予約・抽選一覧</a>
{elseif $fmode=='rsv_01_04'}
	収納管理 &gt; <a href="{$returnUrl}">使用料等受付/使用許可</a>
{else}
	予約管理 &gt; <a href="{$returnUrl}">予約状況検索</a>
{/if}
&nbsp;&gt;&nbsp;<strong><u>予約取消</u></strong>
</div>

<h2 class="subtitle01">予約取消</h2>

<div class="margin-box">
{if $rec.class == 'y'}
<h4>■予約内容を取り消します。</h4>
{else}
<h4><span class="f-red">取消前の状態を表示しています</span></h4>
{/if}
<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_02_04_cancel">
<input type="hidden" name="YoyakuNum" value="{$rec.yoyakunum}">
<input type="hidden" name="KinouGaku" value="{$rec.KinouGaku}" >
<input type="hidden" name="fractionflg" value="{$rec.fractionflg}" >
<table class="itemtable02">
{if $rec.class == 'h'}
<tr>
	<th width="100">取消日時</th>
	<td colspan="3">{$rec.LstUpdDate}&nbsp;{$rec.LstUpdTime}&nbsp;{$rec.CancelStaffName|default:'&nbsp;'}</td>
</tr>
{/if}
<tr>
	<th width="100">予約番号</th>
	<td colspan="3">{$rec.yoyakunum}</td>
</tr>
<tr>
	<th>利用者ID</th>
	<td width="200">{$rec.userid}</td>
	<th width="100">利用者名</th>
	<td width="320" nowrap>{$rec.namesei}　{$rec.nameseikana}
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
	<td colspan="3">{$rec.ShisetsuName}　{$rec.shitsujyoname}</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>{$rec.MokutekiName}</td>
	<th>利用者人数</th>
	<td>{$rec.useninzu}人&nbsp;</td>
</tr>
<tr>
	<th>備考</th>
	<td colspan="3"><textarea name="Bikou" cols="60" rows="3" style="ime-mode:active;">{$rec.bikou}</textarea></td>
</tr>
<tr>
	<th>予約受付</th>
	<td colspan="3">{$rec.AppDateView}&nbsp;{$rec.AppTimeView}&nbsp;{$rec.DaikouStaffName|default:'&nbsp;'}</td>
</tr>
</table>
<br />
<table class="itemtable02">
<tr>
	<th width="100">状態</th>
	<th width="100">収納</th>
	<td width="420" align="center" class="no-border">※予約を取り消す理由を指定してください。</td>
</tr>
<tr align="center">
	<td>{$rec.HonYoyakuKbnName}</td>
	<td>{$rec.PayKbnName}</td>
	<td align="center" class="no-border">
	<label for="select">取消事由</label>
	<select name="CancelReason">
	<option value="">-------------</option>
	{html_options options=$aCancel selected=$req.CancelReason}
	</select>
	</td>
</tr>
</table>
<h3 class="subtitle02">料金情報</h3>

<table class="itemtable02">
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
	<input type="text" name="ShisetsuFee" value="{$rec.ShisetsuFee}" size="10" style="text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="ChouseiGaku" value="{$rec.ChouseiGaku}" size="10" style="text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="SumFee" value="{$rec.SumFee}" size="10" style="text-align:right;" readonly>&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="ShiharaiFee" value="{$rec.ShunouzumiFee|default:'0'}" size="10" readonly style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	{$rec.KinouGaku}&nbsp;円&nbsp;
	</td>
</tr>
<tr>
	<th>キャンセル料</th>
	<td align="right">
	<input name="CancelFee" type="text" value="{$rec.CancelFee|default:'0'}" size="10" style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td colspan="4">
	<input type="button" name="cancelFeeBtn" value="キャンセル料計算" onclick="setCancelFee();">&nbsp;<span id="CancelRate">{$rec.CancelDesc}</span>
	</td>
</tr>
</table>
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
	<input name="Cash" type="text" value="{$rec.Receipt[1]}" onchange="setPayFee();" size="10" maxlength="9" style="text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="Chg" type="text" value="{$rec.Receipt[2]}" onchange="setPayFee();" size="10" maxlength="9" style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="Ticket" type="text" value="{$rec.Receipt[3]}" onchange="setPayFee();" size="10" maxlength="9" style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="KouzaFurikomi" type="text" value="{$rec.Receipt[4]}" onchange="setPayFee();" size="10" maxlength="9" style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="Others" type="text" value="{$rec.Receipt[5]}" onchange="setPayFee();" size="10" maxlength="9" style="text-align:right;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input name="Jyutou" type="text" value="{$rec.Receipt[6]}" size="10" style="text-align:right;" readonly="true">&nbsp;円&nbsp;
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
	<td colspan="2" class="no-border" align="center">
	<input type="button" name="calcBtn" value="収納額計算" class="btn-01" onclick="ShunouKeisan();">
	</td>
	<td colspan="4" class="no-border" align="center">
	<input type="submit" name="cancelBtn" value="予約取消" class="btn-01" onclick="return confirmCancel();">
	<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="location.href='{$returnUrl}';" />
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
