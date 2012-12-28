{include file='header.tpl'}
<!-- templates rsv_02_04_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function confirmCancel()
{
	var CancelReason = document.forma.elements['CancelReason'].value;
	if (CancelReason == '') {
		alert('取消事由を選択してください。');
		return false;
	}
	return confirm('予約を取消します。よろしいですか？');
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
<h4>■予約内容を取り消します。</h4>
<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_02_04_cancel">
<input type="hidden" name="YoyakuNum" value="{$rec.yoyakunum}">
<input type="hidden" name="CancelFee" value="0">
<table class="itemtable02">
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
	<td colspan="3">{$rec.DaikouStaffName|default:'&nbsp;'}</td>
</tr>
</table>
<br />
<table class="itemtable02">
<tr>
	<th width="100">状態</th>
	<th width="100">収納</th>
</tr>
<tr align="center">
	<td>{$rec.HonYoyakuKbnName}</td>
	<td>{$rec.PayKbnName}</td>
</tr>
</table>
<div class="bt-area">
<p>
※予約を取り消す理由を指定して「予約取消」ボタンを押してください。
</p>
<label for="select">取消事由</label>
<select name="CancelReason" id="select">
<option value="" selected>-------------</option>
{html_options options=$aCancel selected=$req.CancelReason}
</select>
<input type="submit" name="cancelBtn" value="予約取消" onClick="return confirmCancel();">
<input type="button" name="backBtn" onClick="location.href='{$returnUrl}';" value="戻る" />
</form>
</div>
</div>

{include file='footer.tpl'}
