{include file='header.tpl'}
<!-- templates rsv_03_01.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function clickKoma(key)
{
	var Obj = document.forma;

	var click = Obj.elements['clickKoma['+key+']'].value;
	if (click == '0') {
		Obj.elements['clickKoma['+key+']'].value = '1';
		document.getElementById('Koma['+key+']').innerHTML = '申し込む';
	} else {
		Obj.elements['clickKoma['+key+']'].value = '0';
		document.getElementById('Koma['+key+']').innerHTML = '○';
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

<div id=searchbox>
予約管理 &gt; 
<a href="index.php?op=rsv_01_02_search&back=1">空き状況照会/予約申込</a> &gt; 
<a href="index.php?op=rsv_02_02_status">空き状況表示</a> &gt; 
<u><strong>空き状況詳細表示</strong></u>
</div>

<h2 class="subtitle01">空き状況詳細表示</h2>
◇利用する時間帯を選択し、「予約」ボタンを押してください。<br>
<h3 class="subtitle02">{$aMain.ShisetsuName}</h3>
<div id="itm1">
<table width="567" height="15">
<tr align="left">
	<td>&nbsp;</td>
	<td colspan="2">
	{strip}
	<strong>{$aMain.ShitsujyoName}{if $aMain.MenName != ''}&nbsp;{$aMain.MenName}{/if}</strong>
	&nbsp;&nbsp;&nbsp;&nbsp;定員&nbsp;:&nbsp;
	{if $aMain.Teiin == 0}-
	{else}{$aMain.Teiin|default:'&nbsp;'}
	{/if}
	人<br><br>
	{/strip}
	</td>
</tr>
<tr align="left">
	<td width="14">&nbsp;</td>
	<td width="200" bordercolor="#666666"><strong>{$aMain.DateView}</strong></td>
	<td width="353">
		<a href="index.php?op=rsv_03_01_status&date={$aMain.PreWeek}&replayFlg=1"><strong>&lt;&lt;&nbsp;前の週</strong></a> &nbsp;&nbsp;<a href="index.php?op=rsv_03_01_status&date={$aMain.Yesterday}&replayFlg=1"><strong>&lt;&nbsp;前日</strong></a> &nbsp;&nbsp;<a href="index.php?op=rsv_03_01_status&date={$aMain.Tomorrow}&replayFlg=1"><strong>翌日&nbsp;&gt;</strong></a> &nbsp;&nbsp;<a href="index.php?op=rsv_03_01_status&date={$aMain.NextWeek}&replayFlg=1"><strong>次の週&nbsp;&gt;&gt;</strong></a>
	</td>
</tr>
</table>
</div>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="rsv_03_01_status">
<div id="itm1">
<table class="itemtable02">
{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
<tr>
	{****  午前・午後・夜間  ****}
{if $aMain.aAPN.am}
	{if $reserve_band_active[0]}
	<th height="25" colspan="{$aMain.aAPN.am}">午前</td>
	{/if}
{/if}
{if $aMain.aAPN.pm}
	{if $reserve_band_active[1]}
	<th height="25" colspan="{$aMain.aAPN.pm}">午後</td>
	{/if}
{/if}
{if $aMain.aAPN.nt}
	{if $reserve_band_active[2]}
	<th height="25" colspan="{$aMain.aAPN.nt}">夜間</td>
	{/if}
{/if}
</tr>
{/if}
<tr height="25" align="center">
{foreach $aMain.aTimeKoma as $value}
	{if $value.apnFlg}
	{if $aMain.komaCount > 16}
	<td class="gray">{$value.FromView}-</td>
	{elseif $aMain.komaCount > 12}
	<td class="gray" nowrap>{$value.FromView}〜</td>
	{else}
	<td width="56" class="gray" nowrap>{$value.FromView}〜</td>
	{/if}
	{/if}
{/foreach}
</tr>
<tr align="center">
{foreach $aMain.aTimeKoma as $key => $value}
	{if $value.apnFlg}
	<td>
		{if $value.reserved}<span class="y-blu">{if $value.YoyakuNum == ''}{$value.Mark|default:'&nbsp;'}{else}<a href="#" onclick="openReserveInfo('{$value.YoyakuNum}');">{$value.Mark|default:'&nbsp;'}</a>{/if}</span>
		{elseif $value.set}<a href="#" onclick="clickKoma('{$key}');"><span id="Koma[{$key}]" class="y-blu">申し込む</span></a>
		{else}<a href="#" onclick="clickKoma('{$key}');"><span id="Koma[{$key}]" class="y-blu">○</span></a>
		{/if}
	</td>
	{/if}
{/foreach}
</tr>
{if !$smarty.const._ROOM_STATUS_ALL_DAY_}
<tr>
	<td height="15" colspan="{$aMain.komaCount}">
	<span class="fsize_10">※&nbsp;午前&nbsp;{$aMain.AMFromView}-{$aMain.AMToView}&nbsp;&nbsp;午後&nbsp;{$aMain.PMFromView}-{$aMain.PMToView}&nbsp;&nbsp;夜間&nbsp;{$aMain.NTFromView}-{$aMain.NTToView}</span>
	</td>
</tr>
{/if}
</table>
{foreach $aMain.aTimeKoma as $key => $value}
<input type="hidden" name="clickKoma[{$key}]" value="{$value.set}">
{/foreach}
</div>

<div align="left">
<input type="submit" name="commitBtn" value="予約" >&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" name="backBtn" onclick="location.href='index.php?op=rsv_02_02_status';" value="戻る" />
</div>
</form>

{include file='footer.tpl'}
