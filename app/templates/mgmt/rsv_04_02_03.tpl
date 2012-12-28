{include file='header.tpl'}
<!-- templates rsv_04_02_03.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
予約管理 &gt;
<a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt;
<a href="index.php?op=rsv_02_02_status">空き状況表示</a> &gt;
<a href="index.php?op=rsv_03_01_status&back=1">空き状況詳細表示</a> &gt;
予約情報入力 &gt; 
<u><strong>申込内容表示</strong></u>
</div>

<h2 class="subtitle01">申込内容表示</h2>

{if $message}<div id="errorbox">{$message}</div>{/if}
<h4 class="subtitle02">予約申込内容</h4>

<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="100">予約番号</th>
	<td colspan="3"><strong>{$aMain.yoyakunum}</strong></td>
</tr>
<tr>
	<th>日時</th>
	<td width="200">{$aMain.DateView}&nbsp;&nbsp;{$aMain.UseTimeFromView}〜{$aMain.UseTimeToView}</td>
	<th width="100">施設(月計申込)</th>
	<td width="320" nowrap>{$aMain.ShisetsuName}&nbsp;({$info.ShisetsuUserCount}&nbsp;回)&nbsp;{$aMain.ShitsujyoName}&nbsp;({$info.ShitsujyoUserCount}&nbsp;回)&nbsp;{$aMain.MenName}</td>
</tr>
<tr>
	<th>付属室場</th>
	<td colspan="3">
	{foreach $aFuzoku as $key => $value}
		{if $value.fuzokucode == $info.FuzokuCode[$key]}{$value.shitsujyoname}&nbsp;{/if}
	{/foreach}
	</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>{$aPurpose[$info.MokutekiCode]}</td>
	<th>利用人数</th>
	<td>
	{$info.useninzu}人
	{if $aMain.ShowDanjyoNinzuFlg}&nbsp;({$aNinzu.ninzu1[0]}&nbsp;{$info.ninzu1}人&nbsp;{$aNinzu.ninzu2[0]}&nbsp;{$info.ninzu2}人){/if}
	</td>
</tr>
<tr>
	<th>予約区分</th>
	<td colspan="3">{$aYoyakuKbn[$info.YoyakuKbn]}
	</td>
</tr>
<tr>
	<th>催事名</th>
	<td colspan="3">{$info.YoyakuName}</td>
</tr>
<tr>
	<th>備考</th>
	<td colspan="3">{$info.Bikou}</td>
</tr>
</table>
</div>

<h4 class="subtitle02">利用者情報</h4>

<div class="margin-box">
<table width="720" class="itemtable02">
<tr>
	<th width="100">利用者ID</th>
	<td>{$info.UserID}</td>
</tr>
<tr>
	<th>利用者名</th>
	<td>{$info.NameSei}<br />{$info.NameSeiKana}
{if $info.UserID === $smarty.const._UNREGISTED_USER_ID_}
	<br>
	<table class="none-table">
	<tr>
		<th>氏名</th><td>{$info.UnregUserName}</td>
	</tr>
	<tr>
		<th>住所</th><td>{$info.UnregAddress}</td>
	</tr>
	<tr>
		<th>電話番号</th><td>{$info.UnregTel}</td>
	</tr>
	<tr>
		<th>連絡先</th><td>{$info.UnregContact}</td>
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
	<td width="120" rowspan="2">{$aFeeKbn[$info.FeeKbn]|default:'--'}
	<th width="120">割増</th>
	<td width="120" nowrap>{if $info.Extracharge == ''}なし{else}{$aExtra[$info.Extracharge]}{/if}</td>
</tr>
<tr>
	<th>減免</th>
        <td>{if $info.Genmen == ''}なし{else}{$aGenmen[$info.Genmen]}{/if}</td>
</tr>
</table>
<br>
<table class="itemtable02">
<tr>
	<th width="120">基本施設使用料</th>
	<th width="120">施設使用料</th>
	<th width="120">消費税額</th>
	<th width="120">受付者</th>
</tr>
<tr>
	<td align="right">{$info.BaseFee}&nbsp;円&nbsp;</td>
	<td align="right">{$info.TotalFee}&nbsp;円&nbsp;</td>
	<td align="right">{$info.Tax}&nbsp;円&nbsp;</td>
	<td align="center">{$user_name}</td>
</tr>
</table>
</div>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_04_02_input">
<div align="center">
<table height="25">
<tr>
	<td width="103" align="center">
	<input type="submit" name="repeatBtn" value="繰り返し予約">
	</td>
	<td width="103" align="center">
	<input type="submit" name="againBtn" value="空き状況へ">
	</td>
{if $info.YoyakuKbn == '02' && $info.ShinsaFlg == '0'}
	<td width="103" align="center">
	<input type="submit" name="receiptBtn" value="受付処理へ" onclick="return confirm('申し込みは完了しました。\n受付画面へ移りますか？');">
	</td>
{/if}
</tr>
</table>
</div>
</form>

{include file='footer.tpl'}
