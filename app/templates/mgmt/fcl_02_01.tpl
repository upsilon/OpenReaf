{include file='header.tpl'}
<!-- templates fcl_02_01.tpl -->

{if $success == 1}
<body onload="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; <a href="index.php?op=fcl_01_01_list">施設登録</a> &gt; <strong><u>施設{if $mode == 'reg'}情報登録{elseif $mode == 'mod'}情報変更{elseif $mode == 'del'}情報削除{elseif $mode == 'abo'}廃止{else}情報照会{/if}</u></strong>
</div>

<h2 class="subtitle01">施設{if $mode == 'reg'}情報登録{elseif $mode == 'mod'}情報変更{elseif $mode == 'del'}情報削除{elseif $mode == 'abo'}廃止{else}情報照会{/if}</h2>

<form name="formx" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">

<div class="margin-box">
{if $message && $success < 1}<div id="errorbox">{$message}</div>{/if}
<table width="600" class="itemtable02">
<tr>
	<th width="160">適用開始日</th>
	<td><input name="appdatefrom" type="text" value="{$para.appdatefrom}" size="20" maxlength="8" style="ime-mode:disabled" {if $err.AppDateFrom}class="error"{/if} {if $mode != 'reg'}class="textBox_r" readonly{/if}></td>
	<th width="57">廃止日</th>
	<td>
	<input name="HaishiDate"  type="text" value="{$para.haishidate}" size="20" maxlength="8" style="ime-mode:disabled" {if $err.HaishiDate}class="error"{/if} OnKeyPress="{literal}if (event.keyCode == 13) {event.returnValue = false;}{/literal}" {if $mode != 'abo' || $aboSuccess}class="textBox_r" readonly{/if}>
	</td>
</tr>
{if $mode != 'reg'}
<tr>
	<th>施設コード</th>
	<td colspan="3">
	<input name="scd"  type="text" value="{$scd}" size="10" maxlength="10" class="textBox_r" readonly>
	</td>
</tr>
{/if}
<tr>
	<th>表示順</th>
	<td colspan="3">
	<input name="shisetsuskbcode"  type="text" value="{$para.shisetsuskbcode|default:0}" size="5" maxlength="7" style="text-align:right;ime-mode:disabled;" {if $err.ShisetsuSkbCode}class="error"{/if} {$input_control}>
	</td>
</tr>
<tr>
	<th>施設名称</th>
	<td colspan="3">
	<input name="shisetsuname"  type="text" value="{$para.shisetsuname}" size="76" maxlength="30" style="ime-mode:active" {if $err.ShisetsuName}class="error"{/if} {$input_control}>
	</td>
</tr>
{*<tr>
	<th>{$smarty.const._KANA_}</th>
	<td colspan="3">
	<input name="shisetsuname2"  type="text" value="{$para.shisetsuname2}" size="76" maxlength="60" style="ime-mode:active" {$input_control}>
	</td>
</tr>*}
<tr>
	<th>住所</th>
	<td colspan="3">
	<input name="adr"  type="text" value="{$para.adr}" size="76" maxlength="60" style="ime-mode:active" {$input_control}>
	</td>
</tr>
<tr>
	<th>電話番号</th>
	<td colspan="3">
	<input name="tel1" type="text" value="{$para.tel1}" size="7" maxlength="10" style="ime-mode:disabled" {if $err.Tel1}class="error"{/if} {$input_control}> −
	<input name="tel2" type="text" value="{$para.tel2}" size="7" maxlength="10" style="ime-mode:disabled" {if $err.Tel2}class="error"{/if} {$input_control}> −
	<input name="tel3" type="text" value="{$para.tel3}" size="7" maxlength="10" style="ime-mode:disabled" {if $err.Tel3}class="error"{/if} {$input_control}>
	</td>
</tr>
<tr>
	<th>問い合せ先電話番号</th>
	<td colspan="3">
	<input name="telno21" value="{$para.telno21}" type="text" size="7" maxlength="5" style="ime-mode:disabled" {if $err.TelNo21}class="error"{/if} {$input_control}> −
	<input name="telno22" value="{$para.telno22}" type="text" size="7" maxlength="4" style="ime-mode:disabled" {if $err.TelNo22}class="error"{/if} {$input_control}> −
	<input name="telno23" value="{$para.telno23}" type="text" size="7" maxlength="4" style="ime-mode:disabled" {if $err.TelNo23}class="error"{/if} {$input_control}>
	</td>
</tr>
<tr>
	<th>施設案内URL</th>
	<td colspan="3">
	<input name="guideurl"  type="text" value="{$para.guideurl}" size="76" maxlength="160" style="ime-mode:disabled" {$input_control}>
	</td>
</tr>
<tr>
	<th>施設管理者</th>
	<td colspan="3">
	<input type="text" name="shisetsumaster" maxlength="64" size="40" value="{$para.shisetsumaster}" style="ime-mode:active" {$input_control}>
	</td>
</tr>
<tr>
	<th>インターネット公開</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=openflg options=$openflg_arr selected=$para.openflg|default:1}
	{else}
	{html_radios name=openflg options=$openflg_arr selected=$para.openflg|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>施設のご案内への表示</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=showguideflg options=$dispflg_arr selected=$para.showguideflg|default:1}
	{else}
	{html_radios name=showguideflg options=$dispflg_arr selected=$para.showguideflg|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>催事予定の表示</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=showeventflg options=$dispflg_arr selected=$para.showeventflg|default:1}
	{else}
	{html_radios name=showeventflg options=$dispflg_arr selected=$para.showeventflg|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>人数詳細入力</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=showdanjyoninzuflg options=$useflg_arr selected=$para.showdanjyoninzuflg|default:1}
	{else}
	{html_radios name=showdanjyoninzuflg options=$useflg_arr selected=$para.showdanjyoninzuflg|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>期間外の表示</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=showoutofserviceflg options=$dispflg_arr selected=$para.showoutofserviceflg|default:1}
	{else}
	{html_radios name=showoutofserviceflg options=$dispflg_arr selected=$para.showoutofserviceflg|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>施設区分</th>
	<td colspan="3">
	<label for="btn_02">
	<select name="shisetsuclassdaicode" {$button_control}>
	{html_options options=$shisetsukbn_arr selected=$para.shisetsuclassdaicode}
	</select>
	</label>
	</td>
</tr>
<tr>
	<th>施設分類</th>
	<td colspan="3">
	<select name="shisetsuclasscode" {$button_control}>
	{html_options options=$aShisetsuClass selected=$para.shisetsuclasscode}
	</select>
	</td>
</tr>
<tr>
	<th>管轄部署</th>
	<td colspan="3">
	<select name="rangebusyocode" {$button_control}>
	{html_options options=$aBusho selected=$para.rangebusyocode}
	</select>
	</td>
</tr>
<tr>
	<th>キャンセル料</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=cancelfeeflg options=$useflg_arr selected=$para.cancelfeeflg|default:0}
	{else}
	{html_radios name=cancelfeeflg options=$useflg_arr selected=$para.cancelfeeflg|default:0 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<th>料金端数処理</th>
	<td colspan="3">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name=fractionflg options=$fractionflg_arr checked=$para.fractionflg|default:'0'}
	{else}
	{html_radios name=fractionflg options=$fractionflg_arr checked=$para.fractionflg|default:'0' disabled=true}
	{/if}
	</td>
</tr>
</table>
</div>
<h4 class="subtitle02">申込制限</h4>
<div class="margin-box">
<h4>予約</h4>
<table width="600" class="itemtable02">
<tr>
	<th width="160">月間制限</th>
	<td width="180">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="limitflg" options=$limitflg_arr selected=$para.limitflg|default:0}
	{else}
	{html_radios name="limitflg" options=$limitflg_arr selected=$para.limitflg|default:0 disabled=true}
	{/if}
	</td>
	<td align="right" nowrap>
	{strip}
	団体&nbsp;<input type="text" name="groupippanmonlimit" value="{$para.groupippanmonlimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupIppanMonLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippanmonlimit" value="{$para.personalippanmonlimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalIppanMonLimit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="weklimitflg" options=$limitflg_arr selected=$para.weklimitflg|default:0}
	{else}
	{html_radios name="weklimitflg" options=$limitflg_arr selected=$para.weklimitflg|default:0 disabled=true}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="groupippanweklimit" value="{$para.groupippanweklimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupIppanWekLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippanweklimit" value="{$para.personalippanweklimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalIppanWekLimit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td>
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="daylimitflg" options=$limitflg_arr selected=$para.daylimitflg|default:0}
	{else}
	{html_radios name="daylimitflg" options=$limitflg_arr selected=$para.daylimitflg|default:0 disabled=true}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="groupippandaylimit" value="{$para.groupippandaylimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupIppanDayLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippandaylimit" value="{$para.personalippandaylimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalIppanDayLimit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
</table>
<h4>抽選</h4>
<table width="600" class="itemtable02">
<tr>
	<th width="160">月間制限</th>
	<td width="180">
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="pulloutlimitflg" options=$limitflg_arr selected=$para.pulloutlimitflg|default:0}
	{else}
	{html_radios name="pulloutlimitflg" options=$limitflg_arr selected=$para.pulloutlimitflg|default:0 disabled=true}
	{/if}
	<br>抽選制限方法<br>
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$para.pulloutmonlimitkbn|default:0 separator="<br>"}
	{else}
	{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$para.pulloutmonlimitkbn|default:0 separator="<br>" disabled=true}
	{/if}
	</td>
	<td align="right" nowrap>
	{strip}
	月間全体&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmonlimit" value="{$para.grouppulloutmonlimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupPullOutMonLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmonlimit" value="{$para.personalpulloutmonlimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalPullOutMonLimit}class="error"{/if} {$input_control}>&nbsp;
	<br>平日&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmon1limit" value="{$para.grouppulloutmon1limit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupPullOutMon1Limit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmon1limit" value="{$para.personalpulloutmon1limit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalPullOutMon1Limit}class="error"{/if} {$input_control}>&nbsp;
	<br>土日祝日&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmon2limit" value="{$para.grouppulloutmon2limit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupPullOutMon2Limit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmon2limit" value="{$para.personalpulloutmon2limit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalPullOutMon2Limit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="pulloutweklimitflg" options=$limitflg_arr selected=$para.pulloutweklimitflg|default:0}
	{else}
	{html_radios name="pulloutweklimitflg" options=$limitflg_arr selected=$para.pulloutweklimitflg|default:0 disabled=true}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="grouppulloutweklimit" value="{$para.grouppulloutweklimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupPullOutWekLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutweklimit" value="{$para.personalpulloutweklimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalPullOutWekLimit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td>
	{if $mode == 'reg' || $mode == 'mod'}
	{html_radios name="pulloutdaylimitflg" options=$limitflg_arr selected=$para.pulloutdaylimitflg|default:0}
	{else}
	{html_radios name="pulloutdaylimitflg" options=$limitflg_arr selected=$para.pulloutdaylimitflg|default:0 disabled=true}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="grouppulloutdaylimit" value="{$para.grouppulloutdaylimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.GroupPullOutDayLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutdaylimit" value="{$para.personalpulloutdaylimit|default:0}" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" {if $err.PersonalPullOutDayLimit}class="error"{/if} {$input_control}>&nbsp;
	{/strip}
	</td>
</tr>
</table>

<table width="650" cellspacing="0" cellpadding="0">
<tr align="right">
	<td colspan="2">&nbsp;</td>
</tr>
<tr align="right">
	<td width="353" align="center">
	{if $mode == 'reg'}
	<input type="submit" name="commitBtn" value="登録" {if $success == 1}disabled{/if} onclick="return confirm('登録しますか？');">
	{elseif $mode == 'mod'}
	<input type="submit" name="commitBtn" value="変更">
	{elseif $mode == 'del'}
	<input type="submit" name="deleteBtn" value="削除" {if $message}disabled{/if} onclick="return confirm('削除しますか？');">
	{elseif $mode == 'abo'}
		{if $aboSuccess}
		<input type="submit" name="HaishiCancelBtn" value="廃止取消">
		{else}
		<input type="submit" name="HaishiBtn" value="廃止">
		{/if}
	{/if}
	<input name="btn_return" type="button" onclick="location.href='index.php?op=fcl_01_01_list';" value="戻る">
	</td>
	<td>&nbsp;</td>
</tr>
</table>
</div>
</form>

{include file='footer.tpl'}
