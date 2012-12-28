{include file='header.tpl'}
<!-- templates rsv_03_11.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
var change_flag = 0;

function checkConfirm(calc_flag)
{
	if (change_flag == 1 && calc_flag == 1) {
		return confirm("計算ボタンを押していませんが、登録してもよろしいですか？");
	} else {
		return confirm("登録処理を行ないます。よろしいですか？");
	}
}
//-->
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
収納管理 &gt;
{if $back_op == 'rsv_02_05_receipt'}
<a href="index.php?op=rsv_01_04_search&back=1">使用料等受付/使用許可</a> &gt;
<a href="#" onclick="submitTo(document.forma, '{$back_op}');">受付</a> &gt;
{else}
<a href="index.php?op=rsv_01_07_search&back=1">使用料等一括受付/使用許可</a> &gt;
<a href="index.php?op=rsv_02_08_search&back=1">一括処理</a> &gt;
<a href="#" onclick="submitTo(document.forma, '{$back_op}');">一括収納</a> &gt;
{/if}
<strong><u>料金加算・調整額</u></strong>
</div>

<h2 class="subtitle01">料金加算・調整額</h2>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_03_11_option">
<input type="hidden" name="back_op" value="{$back_op}">
<input type="hidden" name="yoyakunum" value="{$rec.yoyakunum}">
{if $back_op == 'rsv_02_05_receipt'}
<input type="hidden" name="YoyakuNum" value="{$req.YoyakuNum}">
{else}
{foreach $req.YoyakuNum as $val}
<input type="hidden" name="YoyakuNum[]" value="{$val}">
{/foreach}
{/if}

<table class="itemtable02">
<tr>
	<th width="100">予約番号</th>
	<td width="320">{$rec.yoyakunum}</td>
</tr>
<tr>
	<th>利用者ID</th>
	<td>{$rec.userid}</td>
</tr>
<tr>
	<th>利用日時</th>
	<td>{$rec.UseDateView}&nbsp;{$rec.UseTime}</td>
</tr>
<tr>
	<th>利用施設</th>
	<td nowrap>{$rec.ShisetsuName}&nbsp;{$rec.shitsujyoname}</td>
</tr>
</table>
<br>
<table class="itemtable02">
<tr>
	<th width="120">{$aOptionFee[4]}</th>
	<th width="120">{$aOptionFee[1]}</th>
	<th width="120">{$aOptionFee[2]}</th>
	<th width="120">{$aOptionFee[3]}</th>
	<th width="120">{$aOptionFee[5]}</th>
</tr>
<tr>
	<td align="right">
	<input type="text" name="optionfee4" value="{$req.optionfee4|string_format:'%d'}" onchange="change_flag=1;" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="optionfee1" value="{$req.optionfee1|string_format:'%d'}" onchange="change_flag=1;" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="optionfee2" value="{$req.optionfee2|string_format:'%d'}" onchange="change_flag=1;" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="optionfee3" value="{$req.optionfee3|string_format:'%d'}" onchange="change_flag=1;" maxlength="9" style="width:72px;text-align:right;ime-mode:disabled;">&nbsp;円&nbsp;
	</td>
	<td align="right">
	<input type="text" name="optionfee5" value="{$req.optionfee5|string_format:'%d'}" class="textBox_r" style="width:72px;text-align:right;" readonly="true">&nbsp;円&nbsp;
	</td>
</tr>
<tr>
	<th>調整理由</th>
	<td colspan="4">
	<input type="text" name="chousei_reason" value="{$req.chousei_reason}" maxlength="128" style="width:476px;ime-mode:active;">
	</td>
</tr>
</table>
<br>
{if $res}
<table class="itemtable02">
<tr>
	<th rowspan="2" width="160">加算料金名</th>
	<th rowspan="2">使用時間</th>
	<th rowspan="2">数量</th>
	<th rowspan="2" width="60">料金区分</th>
	<th>割増</th>
	<th rowspan="2">基本料金</th>
	<th rowspan="2">使用料</th>
</tr>
<tr>
	<th>減免</th>
</tr>
{foreach $res as $val}
<tr>
	<td rowspan="2">{$val.shitsujyoname}</td>
	<td rowspan="2">
	<select name="FromHour[{$val.shitsujyocode}]" onchange="change_flag=1;">
	{html_options options=$aHours selected=$req.FromHour[$val.shitsujyocode]}
	</select>&nbsp;:
	<select name="FromMinute[{$val.shitsujyocode}]" onchange="change_flag=1;">
	{html_options options=$aMinutes selected=$req.FromMinute[$val.shitsujyocode]}
	</select>&nbsp;～
	<select name="ToHour[{$val.shitsujyocode}]" onchange="change_flag=1;">
	{html_options options=$aHours selected=$req.ToHour[$val.shitsujyocode]}
	</select>&nbsp;:
	<select name="ToMinute[{$val.shitsujyocode}]" onchange="change_flag=1;">
	{html_options options=$aMinutes selected=$req.ToMinute[$val.shitsujyocode]}
	</select>
	</td>
	<td rowspan="2">
	<input type="text" name="amount[{$val.shitsujyocode}]" maxlength="4" value="{$req.amount[$val.shitsujyocode]|default:0}" style="width:40px;text-align:right;ime-mode:disabled;" onchange="change_flag=1;" />
	</td>
	<td rowspan="2">
		<select name="feekbn[{$val.shitsujyocode}]" onchange="change_flag=1;">
		{if $val.aFeeKbn}
			{html_options options=$val.aFeeKbn selected=$req.feekbn[$val.shitsujyocode]}
		{else}
			<option value="">--</option>
		{/if}
		</select>
	</td>
	<td>
		<select name="extra[{$val.shitsujyocode}]" onchange="change_flag=1;">
		<option value="">割増なし</option>
		{html_options options=$val.aExtra selected=$req.extra[$val.shitsujyocode]}
		</select>
	</td>
	<td rowspan="2">
	&nbsp;<input type="text" name="basefee[{$val.shitsujyocode}]" value="{$req.basefee[$val.shitsujyocode]|default:'0'}" class="textBox_r" style="width:56px;text-align:right;" readonly/>&nbsp;円
	</td>
	<td rowspan="2">
	&nbsp;<input type="text" name="billingfee[{$val.shitsujyocode}]" value="{$req.billingfee[$val.shitsujyocode]|default:'0'}" class="textBox_r" style="width:56px;text-align:right;" readonly/>&nbsp;円
	<input type="hidden" name="tax[{$val.shitsujyocode}]" value="{$req.tax[$val.shitsujyocode]|default:'0'}">
	</td>
</tr>
<tr>
	<td>
		<select name="genmen[{$val.shitsujyocode}]" onchange="change_flag=1;">
		<option value="">減免なし【0%】</option>
		{html_options options=$val.aGenmen selected=$req.genmen[$val.shitsujyocode]}
		</select>
	</td>
</tr>
{/foreach}
</table>
{else}
加算料金項目は設定されていません。<br>
{/if}
<table width="520" height="25">
<tr align="center">
	<td>
	<input type="submit" name="applyBtn" value="登録" class="btn-01" onclick="return checkConfirm({if $res}'1'{else}'0'{/if});">
{if $res}
	<input type="submit" name="calcBtn" value="計算" class="btn-01" onclick="change_flag=0;">
{/if}
	<input type="button" name="backBtn" value="戻る" class="btn-01" onclick="submitTo(this.form, '{$back_op}');">
	</td>
</tr>
</table>
</form>

{include file='footer.tpl'}
