{include file='header.tpl'}
<!-- templates fcl_04_06.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<strong><u>一般予約設定{if $mode=='ref'}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">一般予約設定{if $mode=='ref'}照会{/if}</h2>

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
{if $message}<div id="errorbox">{$message}</div>{/if}
<br>

<form name="forma" method="post" action="index.php">

<table class="itemtable03">
<tr>
	<th width="200">予約時受付方法</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=ippanyoyakukbn options=$ippanyoyakukbn_arr checked=$para.ippanyoyakukbn separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippanyoyakukbn options=$ippanyoyakukbn_arr checked=$para.ippanyoyakukbn separator="&nbsp;"}
	{/if}
	</td>
</tr>
</table>
<br>

<h2 class="subtitle02">一般予約受付スケジュール</h2>
<table class="itemtable03">
<tr height="24">
	<th width="200">&nbsp;</th>
	<th>受付開始日</th>
	<th>受付締切日</th>
</tr>
<tr>
	<th rowspan="4">{$smarty.const._INSIDE_}</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=ippanresstartflg options=$ippanresstartflg_arr checked=$para.ippanresstartflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippanresstartflg options=$ippanresstartflg_arr checked=$para.ippanresstartflg separator="&nbsp;"}
	{/if}
	</td>
	<td>
	{if $mode == 'ref'}
		{html_radios name=ippanreslimitflg options=$ippanreslimitflg_arr checked=$para.ippanreslimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippanreslimitflg options=$ippanreslimitflg_arr checked=$para.ippanreslimitflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanresstartmon" value="{$para.ippanresstartmon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResStartMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月と&nbsp;
	<input type="text" name="ippanresstartday" value="{$para.ippanresstartday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResStartDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日前
	</td>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanreslimitmon" value="{$para.ippanreslimitmon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResLimitMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月と&nbsp;
	<input type="text" name="ippanreslimitday" value="{$para.ippanreslimitday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResLimitDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日前
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanresfrommon" value="{$para.ippanresfrommon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResFromMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月前の&nbsp;
	<input type="text" name="ippanresfromday" value="{$para.ippanresfromday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResFromDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日
	</td>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanrestomon" value="{$para.ippanrestomon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResToMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月前の&nbsp;
	<input type="text" name="ippanrestoday" value="{$para.ippanrestoday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanResToDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日
	</td>
</tr>
<tr>
	<td>
	時刻&nbsp;
	<input type="text" name="ippanresfromtime" value="{$para.ippanresfromtime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanResFromTime}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
	<td>
	時刻&nbsp;
	<input type="text" name="ippanrestotime" value="{$para.ippanrestotime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanResToTime}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
</tr>
<tr>
	<th rowspan="4">{$smarty.const._OUTSIDE_}</th>
	<td>
	{if $mode == 'ref'}
		{html_radios name=ippanshigairesstartflg options=$ippanresstartflg_arr checked=$para.ippanshigairesstartflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippanshigairesstartflg options=$ippanresstartflg_arr checked=$para.ippanshigairesstartflg separator="&nbsp;"}
	{/if}
	</td>
	<td>
	{if $mode == 'ref'}
		{html_radios name=ippanshigaireslimitflg options=$ippanreslimitflg_arr checked=$para.ippanshigaireslimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippanshigaireslimitflg options=$ippanreslimitflg_arr checked=$para.ippanshigaireslimitflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanshigairesstartmon" value="{$para.ippanshigairesstartmon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResStartMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月と&nbsp;
	<input type="text" name="ippanshigairesstartday" value="{$para.ippanshigairesstartday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResStartDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日前
	</td>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanshigaireslimitmon" value="{$para.ippanshigaireslimitmon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResLimitMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月と&nbsp;
	<input type="text" name="ippanshigaireslimitday" value="{$para.ippanshigaireslimitday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResLimitDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日前
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanshigairesfrommon" value="{$para.ippanshigairesfrommon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResFromMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月前の&nbsp;
	<input type="text" name="ippanshigairesfromday" value="{$para.ippanshigairesfromday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResFromDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日
	</td>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanshigairestomon" value="{$para.ippanshigairestomon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResToMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月前の&nbsp;
	<input type="text" name="ippanshigairestoday" value="{$para.ippanshigairestoday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanShigaiResToDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日
	</td>
</tr>
<tr>
	<td>
	時刻&nbsp;
	<input type="text" name="ippanshigairesfromtime" value="{$para.ippanshigairesfromtime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanShigaiResFromTime}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
	<td>
	時刻&nbsp;
	<input type="text" name="ippanshigairestotime" value="{$para.ippanshigairestotime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanShigaiResToTime}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
</tr>
<tr>
	<th>予約受付開始日が閉庁日の場合</th>
	<td colspan="2">
	{if $mode == 'ref'}
		{html_radios name=ipnchgflg1 options=$ipnchgflg1_arr checked=$para.ipnchgflg1 separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ipnchgflg1 options=$ipnchgflg1_arr checked=$para.ipnchgflg1 separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>予約受付開始日が休館日の場合</th>
	<td colspan="2">
	{if $mode == 'ref'}
		{html_radios name=ipnchgflg2 options=$ipnchgflg2_arr checked=$para.ipnchgflg2 separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ipnchgflg2 options=$ipnchgflg2_arr checked=$para.ipnchgflg2 separator="&nbsp;"}
	{/if}
	</td>
</tr>
</table>
<br>
{****
予約情報公開終了日<br>
<table class="itemtable03">
<tr>
	<td width="130">&nbsp;</td>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippanopnlimtday" value="{$para.ippanopnlimtday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanOpnLimtDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日後
	&nbsp;&nbsp; 時刻&nbsp;
	<input type="text" name="ippanopnlimittime" value="{$para.ippanopnlimittime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanOpnLimitTime}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
</tr>
</table>
<br>
****}

<h3 class="subtitle02">取消受付締切日</h3>

<table class="itemtable03">
<tr>
	<td colspan="2">
	{if $mode == 'ref'}
		{html_radios name=ippancanlimitflg options=$ippancanlimitflg_arr checked=$para.ippancanlimitflg separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=ippancanlimitflg options=$ippancanlimitflg_arr checked=$para.ippancanlimitflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippancanlimitday" value="{$para.ippancanlimitday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanCanLimitDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日前
	</td>
	<td rowspan="2">
	時刻&nbsp;
	<input type="text" name="ippancanlimittime" value="{$para.ippancanlimittime}" size="4" maxlength="4" style="ime-mode:disabled;" {if $err.IppanCanLimitTime} class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;(hhmm)
	</td>
</tr>
<tr>
	<td>
	利用日の&nbsp;
	<input type="text" name="ippancanclosemon" value="{$para.ippancanclosemon}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanCanCloseMon}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;ヶ月前の&nbsp;
	<input type="text" name="ippancancloseday" value="{$para.ippancancloseday}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.IppanCanCloseDay}class='error'{/if} {if $mode=='ref'}disabled{/if}>&nbsp;日
	</td>
</tr>
</table>
<br>

<table width="530">
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
