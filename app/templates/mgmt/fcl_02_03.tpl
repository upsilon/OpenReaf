{include file='header.tpl'}
<!-- templates fcl_02_03.tpl -->

{if $success == 1}
<body onload="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
施設管理 &gt; <a href="index.php?op=fcl_01_03_list">施設分類登録</a> &gt; <strong><u>施設分類情報{if $mode == 'reg'}登録{elseif $mode == 'mod'}変更{elseif $mode == 'del'}削除{else}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">施設分類情報{if $mode == 'reg'}登録{elseif $mode == 'mod'}変更{elseif $mode == 'del'}削除{else}照会{/if}</h2>

<form name="forma" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="ccd" value="{$ccd}">

<div class="margin-box">
{if $message && $success < 1}<div id="errorbox">{$message}</div>{/if}
<table class="itemtable02">
<tr>
	<th nowrap width="120">施設分類コード</th>
	<td><input type="text" size="3" maxlength="2" name="shisetsuclasscode" value="{$req.shisetsuclasscode}" style="ime-mode:disabled" {if $err.ShisetsuClassCode}class="error"{/if} {if $mode !='reg'}class="textBox_r" readonly{/if}/></td>
</tr>
<tr>	
	<th>表示順</th>
	<td><input type="text" size="3" maxlength="2" name="shisetsuclassskbcode" value="{$req.shisetsuclassskbcode|default:0}" style="text-align:right;ime-mode:disabled" {if $err.ShisetsuClassSkbCode}class="error"{/if} {$input_control}/></td>	
</tr>
<tr>
	<th>インターネット公開</th>
	<td>
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="delflg" options=$delflg_arr selected=$req.delflg|default:0 disabled=true}
	{else}
	{html_radios name="delflg" options=$delflg_arr selected=$req.delflg|default:0}
	{/if}
	</td> 
</tr>
<tr>	
	<th>施設分類名称</th>
	<td><input type="text" size="30" maxlength="30" name="shisetsuclassname" value="{$req.shisetsuclassname}" style="ime-mode:active" {if $err.ShisetsuClassName}class="error"{/if} {$input_control}/></td>	
</tr>
</table>
</div>

<h4 class="subtitle02">申込制限</h4>
<div class="margin-box">
<h4>予約</h4>
<table width="530" class="itemtable02">
<tr>
	<th width="120">月間制限</th>
	<td width="165">
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="limitflg" options=$limitflg_arr selected=$req.limitflg|default:0 disabled=true}
	{else}
	{html_radios name="limitflg" options=$limitflg_arr selected=$req.limitflg|default:0}
	{/if}
	</td>
	<td align="right">
{strip}
	団体&nbsp;<input type="text" name="groupippanmonlimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.groupippanmonlimit|default:0}" {if $err.GroupIppanMonLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippanmonlimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalippanmonlimit|default:0}" {if $err.PersonalIppanMonLimit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="weklimitflg" options=$limitflg_arr selected=$req.weklimitflg|default:0 disabled=true}
	{else}
	{html_radios name="weklimitflg" options=$limitflg_arr selected=$req.weklimitflg|default:0}
	{/if}
	</td>
	<td align="right">
{strip}
	団体&nbsp;<input type="text" name="groupippanweklimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.groupippanweklimit|default:0}" {if $err.GroupIppanWekLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippanweklimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalippanweklimit|default:0}" {if $err.PersonalIppanWekLimit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td> 
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="daylimitflg" options=$limitflg_arr selected=$req.daylimitflg|default:0 disabled=true}
	{else}
	{html_radios name="daylimitflg" options=$limitflg_arr selected=$req.daylimitflg|default:0}
	{/if}
	</td>
	<td align="right">
{strip}
	団体&nbsp;<input type="text" name="groupippandaylimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.groupippandaylimit|default:0}" {if $err.GroupIppanDayLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalippandaylimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalippandaylimit|default:0}" {if $err.PersonalIppanDayLimit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
</table>
<h4>抽選</h4>
<table width="530" class="itemtable02">
<tr>
	<th width="120">月間制限</th>
	<td width="165">
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="pulloutlimitflg" options=$limitflg_arr selected=$req.pulloutlimitflg|default:0 disabled=true}
	{else}
	{html_radios name="pulloutlimitflg" options=$limitflg_arr selected=$req.pulloutlimitflg|default:0}
	{/if}
	<br>抽選制限方法<br>
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$req.pulloutmonlimitkbn|default:0 separator="<br>" disabled=true}
	{else}
	{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$req.pulloutmonlimitkbn|default:0 separator="<br>"}
	{/if}
	</td>
	<td align="right">
{strip}
	月間全体&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmonlimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.grouppulloutmonlimit|default:0}" {if $err.GroupPullOutMonLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmonlimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalpulloutmonlimit|default:0}" {if $err.PersonalPullOutMonLimit}class="error"{/if} {$input_control}>&nbsp;
	<br>平日&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmon1limit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.grouppulloutmon1limit|default:0}" {if $err.GroupPullOutMon1Limit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmon1limit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalpulloutmon1limit|default:0}" {if $err.PersonalPullOutMon1Limit}class="error"{/if} {$input_control}>&nbsp;
	<br>土日祝日&nbsp;&nbsp;団体&nbsp;<input type="text" name="grouppulloutmon2limit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.grouppulloutmon2limit|default:0}" {if $err.GroupPullOutMon2Limit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutmon2limit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalpulloutmon2limit|default:0}" {if $err.PersonalPullOutMon2Limit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="pulloutweklimitflg" options=$limitflg_arr selected=$req.pulloutweklimitflg|default:0 disabled=true}
	{else}
	{html_radios name="pulloutweklimitflg" options=$limitflg_arr selected=$req.pulloutweklimitflg|default:0}
	{/if}
	</td>
	<td align="right">
{strip}
	団体&nbsp;<input type="text" name="grouppulloutweklimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.grouppulloutweklimit|default:0}" {if $err.GroupPullOutWekLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutweklimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalpulloutweklimit|default:0}" {if $err.PersonalPullOutWekLimit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td> 
	{if $mode == 'ref' || $mode == 'del'}
	{html_radios name="pulloutdaylimitflg" options=$limitflg_arr selected=$req.pulloutdaylimitflg|default:0 disabled=true}
	{else}
	{html_radios name="pulloutdaylimitflg" options=$limitflg_arr selected=$req.pulloutdaylimitflg|default:0}
	{/if}
	</td>
	<td align="right">
{strip}
	団体&nbsp;<input type="text" name="grouppulloutdaylimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.grouppulloutdaylimit|default:0}" {if $err.GroupPullOutDayLimit}class="error"{/if} {$input_control}>&nbsp;
	&nbsp;&nbsp;個人&nbsp;<input type="text" name="personalpulloutdaylimit" maxlength="2" size="2" style="text-align:right;ime-mode:disabled;" value="{$req.personalpulloutdaylimit|default:0}" {if $err.PersonalPullOutDayLimit}class="error"{/if} {$input_control}>&nbsp;
{/strip}
	</td>
</tr>
</table>
{if $mode == 'reg'}
<input type="submit" name="commitBtn" class="btn-01" value="追加" onclick="return confirm('登録しますか？');" {if $success == 1}disabled{/if}>
{elseif $mode == 'mod'}
<input type="submit" name="commitBtn" class="btn-01" value="変更">
{elseif $mode == 'del'}
<input type="submit" name="deleteBtn" class="btn-01" value="削除" onclick="return confirm('削除しますか？')" {if $message}disabled{/if}>
{/if}
<input type="button" class="btn-01" id="btn_rtn" value="戻る" onclick="location.href='index.php?op=fcl_01_03_list';">
</div>
</form>
{include file='footer.tpl'}
