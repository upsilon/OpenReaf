{include file='header.tpl'}
<!-- templates fcl_05_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_01_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">予約時間割一覧</a> &gt;
<strong><u>予約時間割{if $mode=='ref'}照会{elseif $mode=='mod'}変更{elseif $mode=='abo'}{if $aboSuccess}廃止取消{else}廃止{/if}{elseif $mode=='del'}
削除{else}登録{/if}</u></strong>
</div>

<h2 class="subtitle01">予約時間割{if $mode=='ref'}照会{elseif $mode=='mod'}変更{elseif $mode=='abo'}{if $aboSuccess}廃止取消{else}廃止{/if}{elseif $mode=='del'}削除{else}登録{/if}</h2>

<input type="button" value="予約時間割一覧へ戻る" class="btn-01" onclick="location.href='index.php?op=fcl_04_01_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}';">
<br>

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
{if $message}<div id="errorbox">{$message}</div>{/if}

<form name="forma" method="post" action="index.php">
<table>
<tr>
	<td width="70">適用開始日</td>
	<td nowrap>
	<input type="text" name="appdatefrom" value="{$para.appdatefrom}" size="10" maxlength="8" {if $err.AppDateFrom}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}readonly{else}style="ime-mode:disabled;"{/if}>
	{if $mode == 'abo' || $mode == 'del'} 
		&nbsp;&nbsp;&nbsp;廃止日&nbsp;
		<input type="text" name="HaishiDate" value="{$para.haishidate}" size="10" maxlength="8" {if $err.HaishiDate}class="error"{/if} {if $mode=='del' || $aboSuccess=='1'}readonly{else}style="ime-mode:disabled;"{/if}>
	{/if}
	</td>
</tr>
<tr>
	<td>設定期間</td>
	<td nowrap>
	<input type="text" name="monthdayfrom" value="{$para.monthdayfrom}" size="5" maxlength="4" {if $err.MonthDayFrom}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}disabled{else}style="ime-mode:disabled;"{/if}>
	&nbsp;〜&nbsp;
	<input type="text" name="monthdayto" value="{$para.monthdayto}" size="5" maxlength="4" {if $err.MonthDayTo}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}disabled{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
</table>
指定方法のいずれかを選択し、入力してください。<BR>

<table>
<tr>
	<td width="30">&nbsp;</td>
	<td colspan="8">
	<input type="radio" name="komaclass" value="1" id="btn_01" {if $para.komaclass!='2' && $para.komaclass!='3'}checked{/if}{if $mode == 'ref' || $mode == 'abo' || $mode == 'del'} disabled{/if}>
	<label for="btn_01">コマ割を一定にする</label>
	</td>
</tr>
<tr>
	<td width="30">&nbsp;</td>
	<td width="30">&nbsp;</td>
	<td nowrap>開場時刻</td>
	<td><input name="kaijoutime" type="text" size="4" maxlength="4" value="{$para.kaijoutime}" {if $err.KaijouTime}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}></td>
	<td nowrap>閉場時刻</td>
	<td><input name="heijoutime" type="text" size="4" maxlength="4" value="{$para.heijoutime}" {if $err.HeijouTime}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}></td>
	<td nowrap>コマ単位</td>
	<td><input name="komatanitime" type="text" size="4" maxlength="4" value="{$para.komatanitime}" {if $err.KomaTaniTime}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}></td>
	<td nowrap>
	{if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}
		{html_radios name=komatanitimekbn options=$komatanitimekbn_arr checked=$para.komatanitimekbn|default:1 separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=komatanitimekbn options=$komatanitimekbn_arr checked=$para.komatanitimekbn|default:1 separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="8">
	<input type="radio" name="komaclass" value="2" id="btn_02" {if $para.komaclass=='2'}checked{/if}{if $mode == 'ref' || $mode == 'abo' || $mode == 'del'} disabled{/if}>
	<label for="btn_02">コマ毎に時間割を指定する</label>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="7">
	<table class="itemtable02">
	<tr>
		<th>&nbsp;</th>
		<th colspan="2" height="20">予約制時間割表</th>
	</tr>
	<tr align="center">
		<th width="150" height="20" nowrap>番号</th>
		<th width="100" height="20" nowrap>開始時間</th>
		<th width="100" height="20" nowrap>終了時間</th>
	</tr>
	{foreach $para.komakbn as $key => $value}
	<tr align="center">
		<td nowrap>
		{if $key == 0}終日{else}{$value}{/if}
		<input type="hidden" name="komakbn[{$key}]" value="{$value}">
		</td>
		<td nowrap>
		<input type="text" name="komakbntimefrom[{$key}]" size="4" maxlength="4" value="{$para.komakbntimefrom[$key]|truncate:4:"":true}" {if $err.KomaKbnTimeFrom[$key]}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}>
		</td>
		<td nowrap>
		<input type="text" name="komakbntimeto[{$key}]" size="4" maxlength="4" value="{$para.komakbntimeto[$key]|truncate:4:"":true}" {if $err.KomaKbnTimeTo[$key]}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}>
		</td>
	</tr>
	{/foreach}
	</table>
	</td>
</tr>
<tr>
	<td width="30">&nbsp;</td>
	<td colspan="8">
	<input type="radio" name="komaclass" value="3" id="btn_03" {if $para.komaclass=='3'}checked{/if}{if $mode == 'ref' || $mode == 'abo' || $mode == 'del'} disabled{/if}>
	<label for="btn_03">区分を指定する</label>
	</td>
</tr>
<tr>
	<td width="30">&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="7">
	<table class="itemtable02">
	<tr align="center">
		<th width="150" height="20" nowrap>区分名　{if $mode == 'reg' || $mode == 'mod'}<input type="button" name="komaname_add_btn" value="区分追加" onclick="set_val(document.forma.komaname_add_btn_hd, 1);submitTo(this.form, '{$op}')">{/if}</th>
		<th width="100" nowrap>開始時間</th>
		<th width="100" nowrap>終了時間</th>
	</tr>
	{foreach $para.komaname as $key => $value}
	<tr align="center">
		<td nowrap>&nbsp;&nbsp;
	 	<input type="text" name=komaname[{$key}] size="20" maxlength="20" value="{$value}" {if $err.KomaName[$key]}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:active"{/if}>&nbsp;&nbsp;</td>
		<td nowrap>
		<input type="text" name=komanametimefrom[{$key}] size="4" maxlength="4" value="{$para.komanametimefrom[$key]|truncate:4:"":true}" {if $err.KomaNameTimeFrom[$key]}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}></td>
		<td nowrap>
		<input type="text" name=komanametimeto[{$key}] size="4" maxlength="4" value="{$para.komanametimeto[$key]|truncate:4:"":true}" {if $err.KomaNameTimeTo[$key]}class="error"{/if} {if $mode == 'ref' || $mode == 'abo' || $mode == 'del'}class="textBox_r" readonly{else}style="ime-mode:disabled;"{/if}>
		</td>
	</tr>
	{/foreach}
	</table>
	</td>
</tr>
<tr><td colspan="9">&nbsp;</td></tr>
<tr>
	<td colspan="9" align="center">
	{if $mode == 'reg'}
	<input type="submit" name="insertBtn" value="登録" onclick="return confirm('登録しますか？');" {if $success == 1}disabled{/if}>
	{elseif $mode == 'mod'}
	<input type="submit" name="updateBtn" value="変更" onclick="returnn confirm('変更しますか？');">
	{elseif $mode == 'abo'}
	<input type="submit" name="{if $aboSuccess}resumeBtn{else}expireBtn{/if}" value="{if $aboSuccess}廃止取消{else}廃止{/if}">
	{elseif $mode == 'del'}
	<input type="submit" name="deleteBtn" value="削除" onclick="returnn confirm('削除しますか？');" {if $success == 1}disabled{/if}>
	{/if}
	&nbsp;&nbsp;
	<input type="button" name="backBtn" onclick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="apd" value="{$req.apd}">
<input type="hidden" name="prfr" value="{$req.prfr}">
<input type="hidden" name="prto" value="{$req.prto}">
<input type="hidden" name="komaname_add_btn_hd" value="">
</form>

{include file='footer.tpl'}
