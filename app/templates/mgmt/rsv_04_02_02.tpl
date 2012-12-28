{include file='header.tpl'}
<!-- templates rsv_04_02_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
var change_flag = 0;

function checkConfirm()
{
	if (change_flag == 1) {
		return confirm("再計算ボタンを押していませんが、申し込みをしてもよろしいですか？");
	} else {
		return confirm("申し込みを行ないます。よろしいですか？");
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
予約管理 &gt;
<a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt;
<a href="index.php?op=rsv_02_02_status">空き状況表示</a> &gt;
<a href="index.php?op=rsv_03_01_status&back=1">空き状況詳細表示</a> &gt;
<u><strong>予約情報入力</strong></u>
</div>

<h2 class="subtitle01">予約情報入力</h2>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_04_02_input">
<input type="hidden" name="noCheckUserValiDate" value="">
<input type="hidden" name="useCheckFlag" value="{$info.useCheckFlag}">

<h4 class="subtitle02">予約申込内容</h4>

<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="100">日時</th>
	<td width="200">{$aMain.DateView}&nbsp;&nbsp;{$aMain.UseTimeFromView}〜{$aMain.UseTimeToView}</td>
	<th width="100">施設(月計申込)</th>
	<td width="320" nowrap>{$aMain.ShisetsuName}&nbsp;({$info.ShisetsuUserCount}&nbsp;回)&nbsp;{$aMain.ShitsujyoName}&nbsp;({$info.ShitsujyoUserCount}&nbsp;回)&nbsp;{$aMain.MenName}</td>
</tr>
<tr>
	<th>付属室場</th>
	<td colspan="3">
	{foreach $aFuzoku as $key => $value}
		{if $value.fuzokucode == $info.FuzokuCode[$key]}{$value.shitsujyoname}&nbsp;<input type="hidden" name="FuzokuCode[{$key}]" value="{$info.FuzokuCode[$key]}">{/if}
	{/foreach}
	</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>
	{$aPurpose[$info.MokutekiCode]}
	<input type="hidden" name="MokutekiCode" value="{$info.MokutekiCode}">
	</td>
	<th>利用人数</th>
	<td>
	{$info.useninzu}人
	{if $aMain.ShowDanjyoNinzuFlg}&nbsp;({$aNinzu.ninzu1[0]}&nbsp;{$info.ninzu1}人&nbsp;{$aNinzu.ninzu2[0]}&nbsp;{$info.ninzu2}人){/if}
	<input type="hidden" name="useninzu" value="{$info.useninzu}">
	{foreach $aNinzu as $ninzuKey => $ninzuLabel}
	<input type="hidden" name="{$ninzuKey}" value="{$info[$ninzuKey]}">
	{/foreach}
	</td>
</tr>
<tr>
	<th>予約区分</th>
	<td colspan="3">
	{$aYoyakuKbn[$info.YoyakuKbn]}
	<input type="hidden" name="YoyakuKbn" value="{$info.YoyakuKbn}">
	<input type="hidden" name="ShinsaFlg" value="{$info.ShinsaFlg}">
	</td>
</tr>
<tr>
	<th>催事名</th>
	<td colspan="3">
	{$info.YoyakuName}
	<input type="hidden" name="YoyakuName" value="{$info.YoyakuName}">
	</td>
</tr>
<tr>
	<th>備考</th>
	<td colspan="3">
	{$info.Bikou}
	<input type="hidden" name="Bikou" value="{$info.Bikou}">
	</td>
</tr>
</table>
</div>

<h4 class="subtitle02">利用者情報</h4>

<div class="margin-box">
<table width="720" class="itemtable02">
<tr>
	<th width="100">利用者ID</th>
	<td>
	{$info.UserID}
	<input type="hidden" name="UserID" value="{$info.UserID}">
	</td>
</tr>
<tr>
	<th>利用者名</th>
	<td>{$info.NameSei}<br />{$info.NameSeiKana}
		<input type="hidden" name="NameSei" value="{$info.NameSei}">
		<input type="hidden" name="NameSeiKana" value="{$info.NameSeiKana}">
{if $info.UserID === $smarty.const._UNREGISTED_USER_ID_}
		<br>
		<table class="none-table">
		<tr>
			<th>氏名</th><td>{$info.UnregUserName}<input type="hidden" name="UnregUserName" value="{$info.UnregUserName}"></td>
		</tr>
		<tr>
			<th>住所</th><td>{$info.UnregAddress}<input type="hidden" name="UnregAddress" value="{$info.UnregAddress}"></td>
		</tr>
		<tr>
			<th>電話番号</th><td>{$info.UnregTel}<input type="hidden" name="UnregTel" value="{$info.UnregTel}"></td>
		</tr>
		<tr>
			<th>連絡先</th><td>{$info.UnregContact}<input type="hidden" name="UnregContact" value="{$info.UnregContact}"></td>
		</tr>
		</table>
{/if}
	</td>
</tr>
</table>
</div>

<h4 class="subtitle02">料金情報</h4>

<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="120" rowspan="2">料金区分</th>
	<td width="120" rowspan="2">
		<select name="FeeKbn" onchange="change_flag=1;">
		{if $aFeeKbn}
			{html_options options=$aFeeKbn selected=$info.FeeKbn}
		{else}
			<option value="">--</option>
		{/if}
		</select>
	</td>
	<th width="120">割増</th>
	<td>
		<select name="Extracharge" onchange="change_flag=1;">
		<option value="">割増なし</option>
		{html_options options=$aExtra selected=$info.Extracharge}
		</select>
	</td>
	<td rowspan="2" class="no-border">
	<input type="submit" name="calcBtn" value="再計算" onclick="change_flag=0;">
	</td>
</tr>
<tr>
	<th>減免</th>
	<td>
		<select name="Genmen" onchange="change_flag=1;">
		<option value="">減免なし【0%】</option>
		{html_options options=$aGenmen selected=$info.Genmen}
		</select>
	</td>
</tr>
</table>
<br>
<table class="itemtable02">
<tr>
	<th width="120">基本施設使用料</th>
	<th width="120">施設使用料</th>
	<th width="120">消費税額</th>
</tr>
<tr align="right">
	<td>
	<input type="text" name="BaseFee" value="{$info.BaseFee|default:''}" maxlength="10" style="width:72px;text-align:right;ime-mode:disabled;"/>&nbsp;円&nbsp;
	<input type="hidden" name="OriginalFee" value="{$info.OriginalFee}">
	</td>
	<td>
	<input type="text" name="TotalFee" value="{$info.TotalFee|default:''}" style="width:72px;text-align:right;" readonly/>&nbsp;円&nbsp;
	<input type="hidden" name="ShisetsuFee" value="{$info.ShisetsuFee}">
	</td>
	<td>{$info.Tax|default:''}&nbsp;円&nbsp;<input type="hidden" name="Tax" value="{$info.Tax}"></td>
</tr>
</table>
<br>
<table width="226" height="25">
<tr align="center">
	<td>
	<input type="submit" name="applyBtn" value="予約申込" onclick="return checkConfirm();">
	</td>
	<td>
	<input type="submit" name="backBtn" value="戻る">
	</td>
</tr>
</table>
</div>

</form>

{include file='footer.tpl'}
