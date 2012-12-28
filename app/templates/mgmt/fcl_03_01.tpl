{include file='header.tpl'}
<!-- templates fcl_03_01.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt;
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
{if $mode != 'reg'}
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
{/if}
<strong><u>基本情報</u></strong>
</div>

<h2 class="subtitle01">基本情報</h2>

<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</td>
	{if $rec.shitsujyoname}
	<th width="50">室場名</th>
	<td width="160">{$rec.shitsujyoname}</td>
	<th width="70">適用開始日</th>
	<td width="70">{$rec.appdatefrom}</td>
	{/if}
</tr>
</table>
<br />
<p>
<strong>{if $mode=='ref'}基本情報の照会{else}基本情報を入力してください。{/if}</strong>
</p>

{if $message}<div id="errorbox">{$message}</div>{/if}
<div>
<form name="forma" method="post" action="index.php">

<table class="itemtable03">
<tr>
	<th width="120">適用開始日</th>
	<td>
	<input type="text" name="appdatefrom" value="{$para.appdatefrom}" size="12" maxlength="8" style="ime-mode:disabled;" {if $err.AppDateFrom}class="error"{/if} {if $mode=='ref' || $mode=='mod'}class="textBox_r" readonly{/if}>
	</td>
{if $mode=='ref'}
	<th align="right">廃止日</th>
	<td>
	<input type="text" name="haishidate" value="{$para.haishidate}" size="12" maxlength="8" class="textBox_r" disabled="true">
	</td>
{else}
	<td colspan="2">&nbsp;</td>
{/if}
</tr>
<tr>
	<th>表示順</th>
	<td colspan="3">
	<input type="text" name="shitsujyoskbcode" value="{$para.shitsujyoskbcode}" size="6" maxlength="4" style="text-align:right;ime-mode:disabled;" {if $err.ShitsujyoSkbCode}class="error"{/if} {if $mode=='ref'} class="textBox_r"  readonly{/if}>
	</td>
</tr>
<tr>
	<th>室場名称</th>
	<td colspan="3">
	<input type="text" name="shitsujyoname" value="{$para.shitsujyoname}" size="30" maxlength="30" style="ime-mode:active;" {if $err.ShitsujyoName}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>
	</td>
</tr>
{*<tr>
	<th>{$smarty.const._KANA_}</th>
	<td colspan="3">
	<input type="text" name="shitsujyoname2" value="{$para.shitsujyoname2}" size="30" maxlength="60" style="ime-mode:active;" {if $mode=='ref'}class="textBox_r" readonly{/if}>
	</td>
</tr>*}
<tr>
	<th>室場区分</th>
	<td colspan="3">
	{if $mod == 'ref'}
		{html_radios name=shitsujyokbn options=$shitsujyokbn_arr checked=$para.shitsujyokbn|default:'1' separator="&nbsp;&nbsp;&nbsp;&nbsp;" diabled=true}
	{else}
		{html_radios name=shitsujyokbn options=$shitsujyokbn_arr checked=$para.shitsujyokbn|default:'1' separator="&nbsp;&nbsp;&nbsp;&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>定員</th>
	<td>
	<input type="text" name="teiin" value="{$para.teiin|default:0}" size="12" maxlength="8" style="text-align:right;ime-mode:disabled;" {if $err.Teiin}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>人
	</td>
	<th width="120">最小利用人数</th>
	<td>
	<input type="text" name="teiin_min" value="{$para.teiin_min|default:0}" size="12" maxlength="8" style="text-align:right;ime-mode:disabled;" {if $err.Teiin_min}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>人
	</td>
</tr>
</table>
<br>

<table class="itemtable03">
<tr>
	<th width="120">適用減免</th>
	<td>
	{if $mode=="ref"}
		{html_checkboxes name=genapplyflg_chk options=$genmentype_arr checked=$para.genapplyflg_chk separator="&nbsp;" disabled=true}
	{else}
		{html_checkboxes name=genapplyflg_chk options=$genmentype_arr checked=$para.genapplyflg_chk separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>室場減免</th>
	<td>
	{if $mode=="ref"}
		{html_checkboxes name=genmen_chk options=$aGenmen checked=$para.genmen_chk separator="&nbsp;" disabled=true}
	{else}
		{html_checkboxes name=genmen_chk options=$aGenmen checked=$para.genmen_chk separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>割増</th>
	<td>
	{if $mode=="ref"}
		{html_checkboxes name=extracharge_chk options=$aExtra checked=$para.extracharge_chk separator="&nbsp;" disabled=true}
	{else}
		{html_checkboxes name=extracharge_chk options=$aExtra checked=$para.extracharge_chk separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>使用料支払期限<br>（一般予約）</th>
	<td>
	{if $mode=="ref"}
		{foreach $aPayLimitKbn as $value}
		{html_radios name=feepaylimtkbn options=$value checked=$para.feepaylimtkbn|default:'1' separator="&nbsp;" disabled=true}
		<br>
		{/foreach}
	{else}
		{foreach $aPayLimitKbn as $value}
		{html_radios name=feepaylimtkbn options=$value checked=$para.feepaylimtkbn|default:'1' separator="&nbsp;"}
		<br>
		{/foreach}
	{/if}
	<input type="text" name="feepaylimtday" value="{$para.feepaylimtday|default:0}" size="2" maxlength="3" style="text-align:right;ime-mode:disabled;" {if $err.FeePayLimtDay}class="error"{/if} {if $mode=="ref"} class="textBox_r" readonly{/if}>
	<label for="btn_01">日前迄&nbsp;(後払いの場合は○日後迄、翌月払いの場合は○日)</label>
	</td>
</tr>
<tr>
	<th>使用料支払期限<br>（抽選）</th>
	<td>
	{if $mode=="ref"}
		{foreach $aPayLimitKbn as $value}
		{html_radios name=pulloutfeepaylimtkbn options=$value checked=$para.pulloutfeepaylimtkbn|default:'1' separator="&nbsp;" disabled=true}
		<br>
		{/foreach}
	{else}
		{foreach $aPayLimitKbn as $value}
		{html_radios name=pulloutfeepaylimtkbn options=$value checked=$para.pulloutfeepaylimtkbn|default:'1' separator="&nbsp;"}
		<br>
		{/foreach}
	{/if}
	<input type="text" name="pulloutfeepaylimtday" value="{$para.pulloutfeepaylimtday|default:0}" size="2" maxlength="3" style="text-align:right;ime-mode:disabled;" {if $err.PullOutFeePayLimtDay}class="error"{/if} {if $mode=="ref"} class="textBox_r" readonly{/if}>
	<label for="btn_01">日前迄&nbsp;(後払いの場合は○日後迄、翌月払いの場合は○日)</label>
	</td>
</tr>
<tr>
	<th>支払期限切れ</th>
	<td>
	{if $mode=="ref"}
		{html_radios name=feelimitautocanflg options=$feeLimitautocanflg_arr checked=$para.feelimitautocanflg|default:'0' separator="&nbsp;" disabled=true}
	{else}
		{html_radios name=feelimitautocanflg options=$feeLimitautocanflg_arr checked=$para.feelimitautocanflg|default:'0' separator="&nbsp;"}
	{/if}
	</td>
</tr>
<tr>
	<th>利用者画面<br>表示コマ単位</th>
	<td>
	一般予約&nbsp;<input type="text" name="yoyakudispkoma" value="{$para.yoyakudispkoma|default:1}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.YoyakuDispKoma}class="error"{/if} {if $mode=="ref"} class="textBox_r" readonly{/if}>&nbsp;コマ
	&nbsp;&nbsp;抽選&nbsp;<input type="text" name="pulloutdispkoma" value="{$para.pulloutdispkoma|default:1}" size="2" maxlength="2" style="text-align:right;ime-mode:disabled;" {if $err.PullOutDispKoma}class="error"{/if} {if $mode=="ref"} class="textBox_r" readonly{/if}>&nbsp;コマ
	</td>
</tr>
</table>
<h4>画面出力メッセージ</h4>
<table class="itemtable03">
<tr>
	<th width="120">利用時間選択画面</th>
	<td><input type="text" name="msg1" value="{$para.msg1}" size="60" maxlength="64" style="ime-mode:active;" {if $mode=='ref'}class="textBox_r" readonly{/if}></td>
</tr>
<tr>
	<th>申込内容確認画面</th>
	<td><input type="text" name="msg2" value="{$para.msg2}" size="60" maxlength="64" style="ime-mode:active;" {if $mode=='ref'}class="textBox_r" readonly{/if}></td>
</tr>
</table>


<table width="700">
<tr>
	<td>&nbsp;</td>
	<td width="258" align="center">
	{if $mode == 'reg'}
	<input type="submit" name="insertBtn" value="登録" {if $success == 1}disabled{/if}>&nbsp;&nbsp;
	{elseif $mode=='mod'}
	<input type="submit" name="updateBtn" value="更新">&nbsp;&nbsp;
	{/if}
	<input type="button" name="backBtn" onClick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
	<td width="246">&nbsp;</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
</form>
</div>

{include file='footer.tpl'}
