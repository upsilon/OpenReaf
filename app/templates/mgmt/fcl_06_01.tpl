{include file='header.tpl'}
<!-- templates fcl_06_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_07_list&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">料金情報</a> &gt;
<a href="index.php?op=fcl_05_05_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}&cno={$req.cno}">料金設定期間一覧</a> &gt;
{if $mode != 'reg'}<a href="#" onclick="submitTo(document.forma, '{$back_url}');">曜日指定一覧</a> &gt; {/if}<strong><u>料金表</u></strong>
</div>

<h2 class="subtitle01">料金表</h2>

<input type="button" name="backBtn" class="btn-01" onclick="submitTo(document.forma, '{$back_url}');" value="{if $mode == 'reg'}料金設定期間一覧{else}曜日指定一覧{/if}へ戻る">
<br />

<table width="380" class="itemtable02">
<tr>
	<th width="100">施設名</th>
	<td>{$rec.shisetsuname}</td>
</tr>
<tr>
	<th>室場名</th>
	<td>{$rec.shitsujyoname}</td>
</tr>
{if $rec.MenName}
<tr>
	<th>組合せ名称</th>
	<td>{$rec.MenName}</td>
</tr>
{/if}
</table>
{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
<table>
<tr>
	<td width="100">適用開始日</td>
	<td colspan="2">
	<input type="text" name="appdatefrom" value="{$para.appdatefrom}" size="10" maxlength="8" {if $err.AppDateFrom}class="error"{/if} {if $mode=='ref'}disabled{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
<tr>
	<td width="100">料金設定期間</td>
	<td>
	<input type="text" name="monthdayfrom" value="{$para.monthdayfrom}" size="4" maxsize="4" {if $err.MonthDayFrom}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}>&nbsp;〜&nbsp;<input type="text" name="monthdayto" value="{$para.monthdayto}" size="4" maxsize="4" {if $err.MonthDayTo}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}>
	</td>
	<td {if $err.SunFlg}class="error"{/if}>
	<input id=timeSelected0 enable type=checkbox value="1" name="sunflg"{if $para.sunflg=='1'} checked{/if}><label for="timeSelected0">日</label>
	<input id=timeSelected1 enable type=checkbox value="1" name="monflg"{if $para.monflg=='1'} checked{/if}><label for="timeSelected1">月</label>
	<input id=timeSelected2 enable type=checkbox value="1" name="tueflg"{if $para.tueflg=='1'} checked{/if}><label for="timeSelected2">火</label>
	<input id=timeSelected3 enable type=checkbox value="1" name="wedflg"{if $para.wedflg=='1'} checked{/if}><label for="timeSelected3">水</label>
	<input id=timeSelected4 enable type=checkbox value="1" name="thuflg"{if $para.thuflg=='1'} checked{/if}><label for="timeSelected4">木</label>
	<input id=timeSelected5 enable type=checkbox value="1" name="friflg"{if $para.friflg=='1'} checked{/if}><label for="timeSelected5">金</label>
	<input id=timeSelected6 enable type=checkbox value="1" name="satflg"{if $para.satflg=='1'} checked{/if}><label for="timeSelected6">土</label>
	<input id=timeSelected7 enable type=checkbox value="1" name="holiflg"{if $para.holiflg=='1'} checked{/if}><label for="timeSelected7">祝祭日</label>
	</td>
</tr>
<tr>
	<td width="100">最低利用時間</td>
	<td>
	{if $mode=='ref'}
	  {html_radios name="minimumusetimeflg" options=$setflg_arr selected=$para.minimumusetimeflg|default:'0' separator='&nbsp;' disabled=true}
	{else}
	  {html_radios name="minimumusetimeflg" options=$setflg_arr selected=$para.minimumusetimeflg|default:'0' separator='&nbsp;'}
	{/if}
	</td>
	<td>
	&nbsp;&nbsp;最低利用時間&nbsp;<input type="text" name="minimumusetime" value="{$para.minimumusetime|default:0}" size="4" maxlength="6" {if $err.MinimumUseTime}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}style="text-align:right;ime-mode:disabled;"{/if}>&nbsp;分
	</td>
</tr>
<tr>
	<td>利用コマ単位</td>
	<td>
	{if $mode=='ref'}
	  {html_radios name="komaunitflg" options=$setflg_arr selected=$para.komaunitflg|default:'0' separator='&nbsp;' disabled=true}
	{else}
	  {html_radios name="komaunitflg" options=$setflg_arr selected=$para.komaunitflg|default:'0' separator='&nbsp;'}
	{/if}
	</td>
	<td>
	&nbsp;&nbsp;単位&nbsp;<input type="text" name="komaunit" value="{$para.komaunit|default:1}"size="4" maxlength="6" {if $err.KomaUnit}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}style="text-align:right;ime-mode:disabled;"{/if}>&nbsp;コマ
	</td>
</tr>
<tr>
	<td>最低利用料金</td>
	<td colspan="2">
	{if $mode=='ref'}
	  {html_radios name="minimumusefeeflg" options=$setflg_arr selected=$para.minimumusefeeflg|default:'0' separator='&nbsp;' disabled=true}
	{else}
	  {html_radios name="minimumusefeeflg" options=$setflg_arr selected=$para.minimumusefeeflg|default:'0' separator='&nbsp;'}
	{/if}
	</td>
</tr>
<tr>
	<td>単位コマ料金</td>
	<td>
	{if $mode=='ref'}
	  {html_radios name="feeunitflg" options=$setflg_arr selected=$para.feeunitflg|default:'0' separator='&nbsp;' disabled=true}
	{else}
	  {html_radios name="feeunitflg" options=$setflg_arr selected=$para.feeunitflg|default:'0' separator='&nbsp;'}
	{/if}
	</td>
	<td>
	&nbsp;&nbsp;単位&nbsp;<input type="text" name="feeunit" value="{$para.feeunit|default:1}"size="4" maxlength="6" {if $err.FeeUnit}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}style="text-align:right;ime-mode:disabled;"{/if}>&nbsp;コマ
	</td>
</tr>
<tr>
	<td>料金設定</td>
	<td colspan="2">
	{if $mode=='ref'}
	  {html_radios name="feetourokukbn" options=$feetourokukbn_arr selected=$para.feetourokukbn|default:'2' separator='&nbsp;' disabled=true}
	{else}
	  {html_radios name="feetourokukbn" options=$feetourokukbn_arr selected=$para.feetourokukbn|default:'2' separator='&nbsp;'}
	{/if}
	</td>
</tr>
</table>
<br />
<table class="itemtable02">
<tr>
	<th>&nbsp;</th>
	<th colspan="2">区分番号</th>
	<th>01</th><th>02</th><th>03</th><th>04</th><th>05</th>
	<th>06</th><th>07</th><th>08</th><th>09</th><th>10</th>
</tr>
	<tbody>
<tr>
	<th rowspan="4">番号</th>
	<th colspan="2">料金区分</th>
	{section name=feekbn loop=$para.feekbn}
	{if $mode!='ref'}
	<td nowrap>
	<select name="feekbn[{$smarty.section.feekbn.index}]" tabindex="{$para.feekbnindex[feekbn]}">
	<option value="00"></option>
	{html_options options=$aFeeKbn selected=$para.feekbn[feekbn]}
	</select>
	</td>
	{else}	 
	<td class="textBox_r">
		{foreach key=key item=item from=$aFeeKbn}{if $key==$para.feekbn[feekbn]}{$item}{/if}{/foreach}
	</td>		
	{/if}		
	{/section}
	</tr>
	<tr align="center">
	{* -----  最低料金設定 ------ *}
	<th colspan="2">最低利用料金</th>
	{section name=minFee loop=$para.minfee}
	  <td nowrap>
		<input type="text" size="7" maxlength="9" name="minfee[{$smarty.section.minFee.index}]" value="{$para.minfee[minFee]}" {if $err.MinFee[minFee]}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}class="area-size" style="text-align:right;ime-mode:disabled;"{/if} tabindex="{$para.minfeeindex[minFee]}">円
	  </td>
	{/section}
</tr>
<tr align="center">
	{* -----  固定料金設定 ------ *}
	<th colspan="2">固定料金</th>
	{section name=flatFee loop=$para.flatfee}
	  <td nowrap>
		 <input type="text" size="7" maxlength="9" name="flatfee[{$smarty.section.flatFee.index}]" value="{$para.flatfee[flatFee]}" {if $err.FlatFee[flatFee]}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}class="area-size" style="text-align:right;ime-mode:disabled;"{/if} tabindex="{$para.flatfeeindex[flatFee]}">円
	  </td>
	{/section}
</tr>
<tr align="center">
	<th nowrap>開始時間</th>
	<th nowrap>終了時間</th>
	<td colspan="10">コマ時間毎料金</td>
</tr>
{section name=fee loop=$para.fee}
<tr align="center">
	<td nowrap>
	  {$smarty.section.fee.index+1|string_format:"%02d"}
	</td>
	<td nowrap>
		 <input type="text" name="timefrom[{$smarty.section.fee.index}]" value="{$para.timefrom[fee]}" size="4" maxlength="4" {if $err.TimeFrom[fee]}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if} tabindex="{$para.timefromindex[fee]}">
	</td>
	<td nowrap>
		 <input type="text" name="timeto[{$smarty.section.fee.index}]" value="{$para.timeto[fee]}" size="4" maxlength="4" {if $err.TimeTo[fee]}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if} tabindex="{$para.timetoindex[fee]}">
	</td>
	{section name=fee2 loop=$para.fee[fee]}
	  <td>
		<input type="text" name="fee[{$smarty.section.fee.index}][{$smarty.section.fee2.index}]" value="{$para.fee[fee][fee2]}" size="7" maxlength="9" {if $err.Fee[fee][fee2]}class="error"{/if}{if $mode=='ref'}class="textBox_r" readonly style="text-align:right;"{else}class="area-size" style="text-align:right;ime-mode:disabled;"{/if} tabindex="{$para.feeindex[fee][fee2]}">円
	  </td>
	{/section}
</tr>
{/section}
</table>

<div id="div">
<table width="500">
<tr>
	<td width="188"></td>
	<td width="126">
	{if $mode == 'reg'}
	<input type="submit" name="insertBtn" value="登録" onclick="return confirm('登録しますか？');" {if $success == 1}disabled{/if}>&nbsp;&nbsp;
	{elseif $mode == 'mod'}
	<input type="submit" name="updateBtn" value="変更" onclick="returnn confirm('変更しますか？');">&nbsp;&nbsp;
	{/if}
	<input type="button" name="backBtn" onclick="submitTo(this.form, '{$back_url}');" value="戻る">
	<td width="172"></td>
</tr>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="cno" value="{$req.cno}">
<input type="hidden" name="tcd" value="{$req.tcd}">
<input type="hidden" name="apd" value="{$req.apd}">
<input type="hidden" name="prfr" value="{$req.prfr}">
<input type="hidden" name="prto" value="{$req.prto}">
{if $req.new}
<input type="hidden" name="new" value="1">
{/if}
</form>
</table>
<br />
  
{* disabled 設定 *}
{if $mode=='ref'}
{literal}
<script type="text/javascript" language="javascript">
<!--
document.forma.timeSelected0.disabled=true;
document.forma.timeSelected1.disabled=true;
document.forma.timeSelected2.disabled=true;
document.forma.timeSelected3.disabled=true;
document.forma.timeSelected4.disabled=true;
document.forma.timeSelected5.disabled=true;
document.forma.timeSelected6.disabled=true;
document.forma.timeSelected7.disabled=true;
for(i=0;i<document.forma.FeeTourokuKbn.length;i++) {
	document.forma.FeeTourokuKbn[i].disabled=true;
}
//-->
</script>
{/literal}
{/if}

{include file='footer.tpl'}
