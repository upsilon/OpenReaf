{include file='header.tpl'}
<!-- templates fcl_04_04.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>制限設定{if $mode=='ref'}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">制限設定{if $mode=='ref'}照会{/if}</h2>

<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</td>
	<th width="50">室場名</th>
	<td width="160">{$rec.shitsujyoname}</td>
	<th width="70">適用開始日</th>
	<td width="70">{$rec.appdatefrom}</td>
</tr>
</table>
<br>
{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">

<table class="itemtable03">
<tr>
	<th rowspan="2">インターネット利用設定</th>
	<td align="left">
	{if $mode == 'ref'}
		{html_radios name=openflg options=$openflg_arr selected=$para.openflg|default:1 disabled=true}
	{else}
		{html_radios name=openflg options=$openflg_arr selected=$para.openflg|default:1}
	{/if}
	</td>
</tr>
<tr>
	<td>
	<table>
	<tr><th align="center">公開区分</th>
	{foreach $month_arr as $month}
	<td>{$month+1}月</td>
	{/foreach}
	</tr>
	<tr><td>
	<input type="button" name="openkbn1" value="予約" class="btn-02" onclick="checkRadio('1');" {if $mode == 'ref'}disabled{/if}><br>
	<input type="button" name="openkbn2" value="空き状況のみ" class="btn-02" onclick="checkRadio('2');" {if $mode == 'ref'}disabled{/if}><br>
	<input type="button" name="openkbn0" value="非表示" class="btn-02" onclick="checkRadio('0');" {if $mode == 'ref'}disabled{/if}>
	</td>
	{foreach $month_arr as $month}
	<td>
	{if $mode == 'ref'}
		{html_radios name="openkbnval[{$month}]" values=$openkbn_arr selected=$para.openkbnval[$month]|default:1 separator="<br>" disabled=true}
	{else}
		{html_radios name="openkbnval[{$month}]" values=$openkbn_arr selected=$para.openkbnval[$month]|default:1 separator="<br>"}
	{/if}
	</td>
	{/foreach}
	</tr>
	</table>
	<label>閉庁日は空き状況のみ<input type="checkbox" name="openkbnval[12]" value="1" {if $para.openkbnval[12] == '1'}checked{/if} {if $mode == 'ref'}disabled{/if}></label>
	&nbsp;<label>休館日は空き状況のみ<input type="checkbox" name="openkbnval[13]" value="1" {if $para.openkbnval[13] == '1'}checked{/if} {if $mode == 'ref'}disabled{/if}></label>
	</td>
</tr>
<tr>
	<th rowspan="2" width="150">インターネット受付時間帯</th>
	<td align="left" class="no-border">
	{if $mode == 'ref'}
		{html_radios name=webuketimekbn options=$webuketimekbn_arr checked=$para.webuketimekbn separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=webuketimekbn options=$webuketimekbn_arr checked=$para.webuketimekbn separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<td align="center">
		受付時間帯&nbsp;
		<input type="text" name="webuketimefrom" value="{$para.webuketimefrom}" maxlength="4" size="4" {if $err.WebUkeTimeFrom}class="error"{/if} {if $mode=='ref'}disabled{else}style="ime-mode:disabled;"{/if}>
		&nbsp;〜&nbsp;
		<input type="text" name="webuketimeto" value="{$para.webuketimeto}" maxlength="4" size="4" {if $err.WebUkeTimeTo}class="error"{/if} {if $mode=='ref'}disabled{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
</table>
<br>

<h3 class="subtitle02">申込制限</h3>
<p>
	申込回数での制限の場合は回数を<br>
	コマ数での制限の場合はコマ数を設定してください。
</p>
<h4>予約</h4>
<table width="600" class="itemtable03">
<tr>
	<th width="150">月間制限</th>
	<td width="170">
	{if $mode == 'ref'}
		{html_radios name=limitflg options=$limitflg_arr checked=$para.limitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=limitflg options=$limitflg_arr checked=$para.limitflg separator="&nbsp;"}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="yoyakumonlimitdantai" value="{$para.yoyakumonlimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuMonLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="yoyakumonlimitkojin" value="{$para.yoyakumonlimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuMonLimitKojin}class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=weklimitflg options=$limitflg_arr checked=$para.weklimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=weklimitflg options=$limitflg_arr checked=$para.weklimitflg separator="&nbsp;"}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="yoyakuweklimitdantai" value="{$para.yoyakuweklimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuWekLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="yoyakuweklimitkojin" value="{$para.yoyakuweklimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuWekLimitKojin}class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=daylimitflg options=$limitflg_arr checked=$para.daylimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=daylimitflg options=$limitflg_arr checked=$para.daylimitflg separator="&nbsp;"}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="yoyakudaylimitdantai" value="{$para.yoyakudaylimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuDayLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="yoyakudaylimitkojin" value="{$para.yoyakudaylimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.YoyakuDayLimitKojin}class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
</table>
<h4>抽選</h4>
<table width="600" class="itemtable03">
<tr>
	<th width="150">月間制限</th>
	<td width="170">
	{if $mode == 'ref'}
		{html_radios name=pulloutlimitflg options=$limitflg_arr checked=$para.pulloutlimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=pulloutlimitflg options=$limitflg_arr checked=$para.pulloutlimitflg separator="&nbsp;"}
	{/if}
	<br>抽選制限方法<br>
	{if $mode == 'ref'}
		{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$para.pulloutmonlimitkbn|default:0 separator="<br>" disabled=true}
	{else}
		{html_radios name="pulloutmonlimitkbn" options=$pulloutmonlimitkbn_arr selected=$para.pulloutmonlimitkbn|default:0 separator="<br>"}
	{/if}
	</td>
	<td align="right">
	{strip}
	月間全体&nbsp;&nbsp;団体&nbsp;<input type="text" name="pulloutmonlimitdantai" value="{$para.pulloutmonlimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMonLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="pulloutmonlimitkojin" value="{$para.pulloutmonlimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMonLimitKojin}class='error'{/if}>&nbsp;
	<br>平日&nbsp;&nbsp;団体&nbsp;<input type="text" name="pulloutmon1limitdantai" value="{$para.pulloutmon1limitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMon1LimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="pulloutmon1limitkojin" value="{$para.pulloutmon1limitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMon1LimitKojin}class='error'{/if}>&nbsp;
	<br>土日祝日&nbsp;&nbsp;団体&nbsp;<input type="text" name="pulloutmon2limitdantai" value="{$para.pulloutmon2limitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMon2LimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="pulloutmon2limitkojin" value="{$para.pulloutmon2limitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutMon2LimitKojin}class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>週間制限</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=pulloutweklimitflg options=$limitflg_arr checked=$para.pulloutweklimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=pulloutweklimitflg options=$limitflg_arr checked=$para.pulloutweklimitflg separator="&nbsp;"}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="pulloutweklimitdantai" value="{$para.pulloutweklimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutWekLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="pulloutweklimitkojin" value="{$para.pulloutweklimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutWekLimitKojin}class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
<tr>
	<th>日間制限</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=pulloutdaylimitflg options=$limitflg_arr checked=$para.pulloutdaylimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=pulloutdaylimitflg options=$limitflg_arr checked=$para.pulloutdaylimitflg separator="&nbsp;"}
	{/if}
	</td>
	<td align="right">
	{strip}
	団体&nbsp;<input type="text" name="pulloutdaylimitdantai" value="{$para.pulloutdaylimitdantai}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutDayLimitDantai}class='error'{/if}>&nbsp;
	個人&nbsp;<input type="text" name="pulloutdaylimitkojin" value="{$para.pulloutdaylimitkojin}" maxlength="3" size="4" style="text-align:right;ime-mode:disabled;" {if $mode=='ref'}disabled{/if} {if $err.PullOutDayLimitKojin} class='error'{/if}>&nbsp;
	{/strip}
	</td>
</tr>
</table>
<br>

<h4 class="subtitle02">予約申込の制限</h4>
<table class="itemtable03">
<tr>
	<th width="150" align="center">{$smarty.const._INSIDE_}／{$smarty.const._OUTSIDE_}</th>
	<td {if $err.YoyakuAreaPriorityFlg} class='error'{/if}>
	{if $mode == 'ref'}
		{html_radios name=yoyakuareapriorityflg options=$areapriorityflg_arr checked=$para.yoyakuareapriorityflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=yoyakuareapriorityflg options=$areapriorityflg_arr checked=$para.yoyakuareapriorityflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th align="center">個人／団体</th>
	<td {if $err.YoyakuKojinDanFlg} class='error'{/if}>
	{if $mode == 'ref'}
		{html_radios name=yoyakukojindanflg options=$grouporpersonallimit_arr checked=$para.yoyakukojindanflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=yoyakukojindanflg options=$grouporpersonallimit_arr checked=$para.yoyakukojindanflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
</table>
<br>

<h4 class="subtitle02">抽選申込の制限</h4>
<table class="itemtable03">
<tr>
	<th width="150" align="center">{$smarty.const._INSIDE_}／{$smarty.const._OUTSIDE_}</th>
	<td {if $err.PullOutAreaPriorityFlg} class='error'{/if}>
	{if $mode == 'ref'}
		{html_radios name=pulloutareapriorityflg options=$areapriorityflg_arr checked=$para.pulloutareapriorityflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=pulloutareapriorityflg options=$areapriorityflg_arr checked=$para.pulloutareapriorityflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th align="center">個人／団体</th>
	<td {if $err.PullOutKojinDanFlg} class='error'{/if}>
	{if $mode == 'ref'}
		{html_radios name=pulloutkojindanflg options=$grouporpersonallimit_arr checked=$para.pulloutkojindanflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=pulloutkojindanflg options=$grouporpersonallimit_arr checked=$para.pulloutkojindanflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
</table>
<br>

<table width="600">
<tr>
	<td align="center">
	{if $mode == 'mod'}
	<input type="submit" name="updateBtn" value="更新">&nbsp;&nbsp;
	{/if}
	<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
</form>

{include file='footer.tpl'}
