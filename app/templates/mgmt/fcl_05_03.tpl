{include file='header.tpl'}
<!-- templates fcl_05_03.tpl -->

{if $message}
<body onLoad="alert('{$message}');">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_03_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">申込不可日設定</a> &gt;
<strong><u>定期休館日{if $mode=='mod'}設定{else}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">定期休館日{if $mode=='mod'}設定{else}照会{/if}</h2>

<form name="forma" method="post" action="index.php">

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
<br />

<table class="itemtable03">
<tr>
	<th rowspan="2">休館日1</th>
	<td width="60">週指定</td>
	<td>
	<input name="maishu1" type="checkbox" id="chk1-1" value="1" {if $para.maishu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-1">毎週</label>&nbsp;
	<input name="dai1shu1" type="checkbox" id="chk1-2" value="1" {if $para.dai1shu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-2">第1</label>&nbsp;
	<input name="dai2shu1" type="checkbox" id="chk1-3" value="1" {if $para.dai2shu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-3">第2</label>&nbsp;
	<input name="dai3shu1" type="checkbox" id="chk1-4" value="1" {if $para.dai3shu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-4">第3</label>&nbsp;
	<input name="dai4shu1" type="checkbox" id="chk1-5" value="1" {if $para.dai4shu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-5">第4</label>&nbsp;
	<input name="dai5shu1" type="checkbox" id="chk1-6" value="1" {if $para.dai5shu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk1-6">第5</label>
	</td>
</tr>
<tr>
	<td>曜日指定</td>
	<td>
	<input name="sun1" type="checkbox" id="chk2-1" value="1" {if $para.sun1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-1">日</label>&nbsp;
	<input name="mon1" type="checkbox" id="chk2-2" value="1" {if $para.mon1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-2">月</label>&nbsp;
	<input name="tue1" type="checkbox" id="chk2-3" value="1" {if $para.tue1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-3">火</label>&nbsp;
	<input name="wed1" type="checkbox" id="chk2-4" value="1" {if $para.wed1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-4">水</label>&nbsp;
	<input name="thu1" type="checkbox" id="chk2-5" value="1" {if $para.thu1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-5">木</label>&nbsp;
	<input name="fri1" type="checkbox" id="chk2-6" value="1" {if $para.fri1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-6">金</label>&nbsp;
	<input name="sat1" type="checkbox" id="chk2-7" value="1" {if $para.sat1}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk2-7">土</label>&nbsp;
	</td>
</tr>
<tr>
	<th rowspan="2">休館日2</th>
	<td>週指定</td>
	<td>
	<input name="maishu2" type="checkbox" id="chk3-1" value="1" {if $para.maishu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-1">毎週</label>&nbsp;
	<input name="dai1shu2" type="checkbox" id="chk3-2" value="1" {if $para.dai1shu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-2">第1</label>&nbsp;
	<input name="dai2shu2" type="checkbox" id="chk3-3" value="1" {if $para.dai2shu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-3">第2</label>&nbsp;
	<input name="dai3shu2" type="checkbox" id="chk3-4" value="1" {if $para.dai3shu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-4">第3</label>&nbsp;
	<input name="dai4shu2" type="checkbox" id="chk3-5" value="1" {if $para.dai4shu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-5">第4</label>&nbsp;
	<input name="dai5shu2" type="checkbox" id="chk3-6" value="1" {if $para.dai5shu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk3-6">第5</label>
	</td>
</tr>
<tr>
	<td>曜日指定</td>
	<td>
	<input name="sun2" type="checkbox" id="chk4-1" value="1" {if $para.sun2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-1">日</label>&nbsp;
	<input name="mon2" type="checkbox" id="chk4-2" value="1" {if $para.mon2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-2">月</label>&nbsp;
	<input name="tue2" type="checkbox" id="chk4-3" value="1" {if $para.tue2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-3">火</label>&nbsp;
	<input name="wed2" type="checkbox" id="chk4-4" value="1" {if $para.wed2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-4">水</label>&nbsp;
	<input name="thu2" type="checkbox" id="chk4-5" value="1" {if $para.thu2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-5">木</label>&nbsp;
	<input name="fri2" type="checkbox" id="chk4-6" value="1" {if $para.fri2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-6">金</label>&nbsp;
	<input name="sat2" type="checkbox" id="chk4-7" value="1" {if $para.sat2}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk4-7">土</label>&nbsp;
	</td>
</tr>
<tr>
	<th rowspan="2">休館日3</th>
	<td>週指定</td>
	<td>
	<input name="monthfirst3" type="checkbox" id="chk5-1" value="1" {if $para.monthfirst3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk5-1">毎月最初の</label>&nbsp;
	<input name="monthfainal3" type="checkbox" id="chk5-2" value="1" {if $para.monthfainal3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk5-2">毎月最終の</label>
	</td>
</tr>
<tr>
	<td>曜日指定</td>
	<td>
	<input name="sun3" type="checkbox" id="chk6-1" value="1" {if $para.sun3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-1">日</label>&nbsp;
	<input name="mon3" type="checkbox" id="chk6-2" value="1" {if $para.mon3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-2">月</label>&nbsp;
	<input name="tue3" type="checkbox" id="chk6-3" value="1" {if $para.tue3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-3">火</label>&nbsp;
	<input name="wed3" type="checkbox" id="chk6-4" value="1" {if $para.wed3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-4">水</label>&nbsp;
	<input name="thu3" type="checkbox" id="chk6-5" value="1" {if $para.thu3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-5">木</label>&nbsp;
	<input name="fri3" type="checkbox" id="chk6-6" value="1" {if $para.fri3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-6">金</label>&nbsp;
	<input name="sat3" type="checkbox" id="chk6-7" value="1" {if $para.sat3}checked{/if} {if $mode=='ref'}disabled{/if}><label for="chk6-7">土</label>&nbsp;
	</td>
</tr>
<tr>
	<th>祝祭日</th>
	<td colspan="2">
	{if $mode == 'ref'}
	{html_radios name=holiclosedflg options=$holiclosedflg_arr checked=$para.holiclosedflg separator="&nbsp;" disabled=true}
	{else}
	{html_radios name=holiclosedflg options=$holiclosedflg_arr checked=$para.holiclosedflg separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>祝祭日振替<br>（休館日1〜3と祝祭日が重なる場合）</th>
	<td colspan="2">
	{if $mode == 'ref'}
	{html_radios name=closeddaychgflg options=$closeddaychgflg_arr checked=$para.closeddaychgflg separator="&nbsp;" disabled=true}
	{else}
	{html_radios name=closeddaychgflg options=$closeddaychgflg_arr checked=$para.closeddaychgflg separator="&nbsp;"}
	{/if}
	
    <p>
    ※振替例）<br>
    1．祝祭日を休館とする場合&nbsp;&nbsp; <strong class="f-red">翌平日も休館</strong><br>
    2．祝祭日を休館としない場合&nbsp;&nbsp; <strong class="f-red">祝祭日当日を開館、翌平日を休館</strong><br>
	</p>
	</td>
</tr>
<tr>
	<th>月内定期休館日</th>
	<td colspan="2">
	毎月&nbsp;&nbsp;<input name="koteiclosedday1" type="text" size="1" maxlength="2" value="{$para.koteiclosedday1}" {if $mode=="ref"}class="textBox_r" disabled{else}style="ime-mode:disabled;"{/if}>日&nbsp;&nbsp;
	<input name="koteiclosedday2" type="text" size="1" maxlength="2" value="{$para.koteiclosedday2}" {if $mode=="ref"}class="textBox_r" disabled{else}style="ime-mode:disabled;"{/if}>日&nbsp;&nbsp;
	<input name="koteiclosedday3" type="text" size="1" maxlength="2" value="{$para.koteiclosedday3}" {if $mode=="ref"}class="textBox_r" disabled{else}style="ime-mode:disabled;"{/if}>日&nbsp;&nbsp;<span class="f-red">(数字2桁)</span>
	</td>
</tr>
	<th>休館除外日</th>
	<td colspan="2">
	{foreach $para.exception as $key => $value}
	<input name="exception[{$key}]" type="text" size="5" maxlength="4" value="{$value}" {if $mode=="ref"}class="textBox_r" disabled{else}style="ime-mode:disabled;"{/if}>&nbsp;&nbsp;
	{/foreach}
<span class="f-red">(MMDD形式)</span>
	</td>
<tr>
</tr>
</table>
<br>

<table width="500" cellspacing="0" cellpadding="0">
<tr>
	<td align="center">
	{if $mode=='mod'}
	<input type="submit" name="updateBtn" value="更新">&nbsp;&nbsp;
	{/if}
	<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
</form>

{include file='footer.tpl'}
