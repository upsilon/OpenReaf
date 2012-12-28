{include file='header.tpl'}
<!-- templates rsv_03_05.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function checkFee()
{
	var val = document.forma.KanpuFee.value;
	if (val == '' || val == '0') {
		alert('充当額を入力してください。');
		return false;
	} else if (val.match(/[^0-9]/)) {
		alert('充当額は半角数字で入力してください。');
		return false;
	}

	var msg = '充当を行ないます。よろしいですか？';
	var i, n, selectFee = 0;
	if (document.forma.YoyakuNum.length) {
		n = document.forma.YoyakuNum.length;
		for (i = 0; i < n; ++i)
		{
			if (document.forma.YoyakuNum[i].checked) {
				selectFee = document.forma.elements['ShisetsuFee['+i+']'].value - 0;
				break;
			}
		}
	} else {
		if (document.forma.YoyakuNum.checked) {
			selectFee = document.forma.elements['ShisetsuFee[0]'].value - 0;
		}
	}
	if (selectFee == 0) {
		alert('充当先の予約を選択してください。');
		return false;
	}
	var jyutouFee = document.forma.KanpuFee.value - 0;
	if (jyutouFee > selectFee) {
		msg = '充当額 '+addKeta(jyutouFee)+' 円に対して、充当先の未収納額分の '+addKeta(selectFee)+' 円のみ充当します。\nよろしいですか？';
		document.forma.KanpuFee.value = selectFee;
	}
	if (confirm(msg)) {
		return true;
	} else {
		if (jyutouFee > selectFee) {
			document.forma.KanpuFee.value = jyutouFee;
		}
		return false;
	}
}
// -->
</script>
{/literal}

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt; <a href="index.php?op=rsv_01_01_search&back=1">予約状況</a> &gt; <a href="index.php?op=rsv_02_06_exemption&YoyakuNum={$req.srcYoyaku}{if $req.delFlg}&delFlg=1{/if}">還付・充当</a> &gt; <strong><u>充当受付</u></strong>
</div>

<h2 class="subtitle01">充当受付</h2>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<div class="margin-box">
<table class="itemtable03">
<tr>
	<th nowrap>利用者ID</th>
	<td><input type="text" name="user_id" value="{$user.userid}" size="12" maxlength="16" class="textBox_r" readonly="true"></td>
</tr>
<tr>
	<th nowrap>利用者名</th>
	<td>
	<input type="text" name="name" value="{$user.namesei}" size="30" maxlength="20" class="textBox_r" readonly="true">
	<input type="text" name="name_kana" value="{$user.nameseikana}" size="30" maxlength="20" class="textBox_r" readonly="true">
	</td>
</tr>
<tr>
	<th nowrap>充当額</th>
	<td><input type="text" name="KanpuFee" value="{$req.KanpuFee}" size="12" maxlength="16" style="text-align:right;ime-mode:disabled;">&nbsp;円</td>
</tr>
</table>
<br>
{if $message}<div id="errorbox">{$message}</div>{/if}
{if $success == 1}
<strong>以下の予約に充当しました。</strong>
<input type="submit" name="receiptBtn" value="受付処理へ">
{else}
<strong>充当先の予約を選択して「充当」ボタンを押してください。</strong>
<input type="submit" name="commitBtn" value="充当" onclick="return checkFee();">
<input type="hidden" name="srcYoyaku" value="{$req.srcYoyaku}">
{if $req.delFlg}
<input type="hidden" name="delFlg" value="{$req.delFlg}">
{/if}
{/if}
<input type="hidden" name="UserID" value="{$req.UserID}">
<input type="button" name="backBtn" value="戻る" onClick="location.href='index.php?op=rsv_02_06_exemption&YoyakuNum={$req.srcYoyaku}{if $req.delFlg}&delFlg=1{/if}';">
</div>
<div align="center">
<table class="itemtable02">
<tr>
	<th width="60">予約番号</th>
	<th width="110">利用日</th>
	<th width="90">利用時間</th>
	<th width="180">利用施設</th>
	<th width="80">未収納額</th>
	<th width="60">収納状態</th>
	<th width="56">選択</th>
</tr>
{foreach $recs as $val}
<tr>
	<td align='center'>{$val.yoyakunum}</td>
	<td align='center'>{$val.UseDateView}</td>
	<td align='center'>{$val.UseTimeFromView}〜{$val.UseTimeToView}</td>
	<td align='left'>
	{$val.ShisetsuName}<br>{$val.shitsujyoname}
	</td>
	<td align='right'>
	{$val.useShowFee}円
	<input type="hidden" name="ShisetsuFee[{$val.key}]" value="{$val.unpaidFee|string_format:"%d"}">
	</td>
	<td align='center'>{$val.PayKbnName}</td>
	<td align='center'>
	<input type="radio" name="YoyakuNum" value="{$val.yoyakunum}" {if $val.yoyakunum == $req.YoyakuNum}checked{/if}>
	</td>
</tr>
{foreachelse}
<tr>
    	<td colspan="7" align="center">完納していない予約がありません。</td>
</tr>
{/foreach}
</table>
</div>
</form>

{include file='footer.tpl'}
