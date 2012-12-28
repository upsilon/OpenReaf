{include file='header.tpl'}
<!-- templates rsv_02_06.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
// フォーム送信
function goToSubmit(mode)
{
	document.forma.mode.value=mode;
	var message = '';

	if (mode == 1) {
		message = '還付を受け付けます。よろしいですか？';
	} else if (mode == 2) {
		document.forma.op.value = 'rsv_03_05_jyutou';
		document.forma.submit();
		return;
	} else if (mode == 3) {
		message = '収納状態を「還付なし」にします。よろしいですか？';
	} else if (mode == 6) {
		message = '収納状態の「還付なし」を取り消します。よろしいですか？';
	}
	if(confirm(message)){
		document.forma.op.value = 'rsv_02_06_exemption';
		document.forma.submit();
	}
}

function goToCancel(mode, UketsukeNo)
{
	document.forma.op.value = 'rsv_02_06_exemption';
	document.forma.mode.value = mode;
	document.forma.UketsukeNo.value = UketsukeNo;

	if(mode == 4) {
		message = '還付を取り消します。よろしいですか？';
	} else if (mode == 5) {
		message = '充当を取り消します。よろしいですか？';
	}
	if(confirm(message)){
		document.forma.submit();
	}
}
//-->
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
予約管理 &gt; <a href="{$returnUrl}">予約状況</a> &gt; <strong><u>還付・充当</u></strong>
</div>

<h2 class="subtitle01">還付・充当</h2>

<input type="button" value="戻る" onclick="location.href='{$returnUrl}'" />

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_02_06_exemption">
<h4 class="subtitle02">収納情報</h4>
<table class="itemtable02">
<tr>
	<th width="100">予約番号</th>
	<td width="100" align="center">{$rec.YoyakuNum}</td>
	<td colspan="3" class="no-border">&nbsp;</td>
</tr>
<tr>
	<th>基本施設使用料</th>
	<th>施設使用料</th>
	<th width="100">合計額</th>
	<th width="100">納付済金額</th>
	<th width="100">収納状態</th>
</tr>
<tr>
	<td align="right">{$rec.BaseShisetsuFeeView}円</td>
	<td align="right">{$rec.ShisetsuFeeView}円</td>
	<td align="right">{$rec.SumFee}円</td>
	<td align="right">{$rec.ReceiptSumFeeView}円</td>
	<td align="center">{$rec.ReceiptStatus}</td>
</tr>
<tr>
	<th>還付率</th>
	<th>未還付額</th>
	<td colspan="3" class="no-border">&nbsp;</td>
</tr>
<tr>
	<td align="right">{$rec.KanpuRate}&nbsp;%</td>
	<td align="right">{$rec.KanpuFeeView}円</td>
	<td colspan="3" class="no-border">&nbsp;</td>
</tr>
</table>

<h4 class="subtitle02">還付・充当処理</h4>

{** 受付の場合 **}
<table class="itemtable02">
<tr>
	<th width="100">還付・充当額</th>
	<td>
	<input type="text" name="KanpuFee" value="{$rec.KanpuFee}" size="10" style="text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	<select name="KinshuCode">
	{html_options options=$aKinshu selected=$req.KinshuCode|default:'01'}
	</select>
	</td>
</tr>
<tr>
	<th>還付・充当日</th>
	<td>
	{html_select_date prefix='Reg' start_year='-2' end_year='+2' display_months=false display_days=false}年
	{html_select_date prefix='Reg' display_years=false display_days=false month_format='%m'}月
	{html_select_date prefix='Reg' display_years=false display_months=false day_value_format='%02d'}日
	</td>
</tr>
</table>
<div class="bt-area">
<input name="today" type="button" value="　還付する　" onclick="goToSubmit(1);" {if $kannouStatusFlg}disabled{/if}>&nbsp;&nbsp;
<input name="today" type="button" value="　充当する　" onclick="goToSubmit(2);" {if $kannouStatusFlg || $rec.userid === $smarty.const._UNREGISTED_USER_ID_}disabled{/if}>
</div>

<h4 class="subtitle02">収納状態還付なし処理</h4>
{if !$kannouStatusFlg}
<div class="itemtop-area">
(※)納付済金額に関わらず、収納状態を「還付なし」にします。<br>
</div>
<div class="bt-area">
<input name="today" type="button" value="　還付なし　" onclick="goToSubmit(3);">
</div>
{else}
<h4>収納状態還付なし処理</h4>
<div class="itemtop-area">
(※)「還付なし」状態をキャンセルし、納付済金額により収納状態を判別します。<br>
</div>
<div class="bt-area">
<input name="today" type="button" value="　取消　" onclick="goToSubmit(6);">
</div>
{/if}
<input type="hidden" name="mode" value="">
<input type="hidden" name="UketsukeNo" value="">
<input type="hidden" name="UserID" value="{$rec.userid}">
<input type="hidden" name="srcYoyaku" value="{$rec.YoyakuNum}">
{if $req.delFlg}
<input type="hidden" name="delFlg" value="{$req.delFlg}">
{/if}
</form>

<h4 class="subtitle02">還付・充当履歴</h4>

<table class="itemtable02">
<tr>
	<th width="30px">番号</th>
	<th width="50px">状態</th>
	<th width="120px">還付・充当</th>
	<th width="60px">金額</th>
	<th width="95px">還付・充当日</th>
	<th width="95px">受付者</th>
	<th width="110px">受付日時</th>
	<th>&nbsp;</th>
</tr>
{foreach $aKanpu as $val}
<tr align="center">
	<td>{$val.uketsukeno}</td>
	<td>{if $val.cancelflg==1}<span style="color:red">取消</span>{else}<span style="color:blue">受付済</span>{/if}</td>
	<td>{if $val.status==1}還付&nbsp;({$aKinshu[$val.kinshucode|default:'01']}){else}充当&nbsp;({$val.destyoyakunum|default:'-'}){/if}</td>
	<td align="right">{$val.FeeView}円</td>
	<td>{$val.KanpuJyutouDateView}</td>
	<td>{if $val.cancelflg==1}{$val.CancelStaffName}{else}{$val.ReceiptStaffName}{/if}</td>
	<td>{if $val.cancelflg==1}{$val.canceldatetime}{else}{$val.receiptdatetime}{/if}</td>
	<td>{if $val.cancelflg!=1 && $val.status==1}<input name="cancelBtn" type="button" value="取消" onclick="goToCancel(4,{$val.uketsukeno});">{else}&nbsp;{/if}</td>
</tr>
{/foreach}
</table>

{include file='footer.tpl'}
