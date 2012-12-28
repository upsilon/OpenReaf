{include file='header.tpl'}
<!-- templates fcl_05_06.tpl -->

{if $success == 1}
<body onload="alert('{$message}');">
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
<a href="index.php?op=fcl_04_08_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">利用単位情報（利用単位一覧）</a> &gt; 
<strong><u>利用単位情報{if $mode=='reg'}登録{elseif $mode=='mod'}変更{else}照会{/if}</u></strong>
</div>

<h2 class="subtitle01">利用単位情報{if $mode=='reg'}登録{elseif $mode=='mod'}変更{else}照会{/if}</h2>

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
<p>
{if $mode=='reg'}利用単位情報を入力してください。{elseif $mode=='mod'}利用単位情報を変更してください。{else}利用単位情報照会{/if}
</p>

{if $message && $success < 1}<div id="errorbox">{$message}</div>{/if}
<form name="forma" method="post" action="index.php">
<table class="itemtable03">
<tr>
	<th width="100">適用開始日</th>
	<td>
	<input name="appdatefrom" type="text" size="12" maxlength="8"  value="{$para.appdatefrom}" style="ime-mode:disabled;" {if $err.AppDateFrom}class="error"{/if} {if $mode=='ref' || $mode=='mod'}class="textBox_r" readonly{/if}>
	</td>
{if $mode == 'ref'}
	<th align="right">廃止日</th>
	<td>
	<input name="menhaishidate" type="text" size="12" maxlength="8" value="{$para.menhaishidate}" class="textBox_r" disabled="true">
	</td>
{else}
	<td colspan="2">&nbsp;</td>
{/if}
</tr>
<tr>
	<th>表示順</th>
	<td colspan="3">
	<input name="menskbcode" type="text" size="6" maxlength="4" value="{$para.menskbcode}" style="text-align:right;ime-mode:disabled;" {if $err.MenSkbCode}class="error"{/if} {if $mode=='ref'} class="textBox_r"  readonly{/if}>
	</td>
</tr>
<tr>
	<th>利用単位名称</th>
	<td colspan="3">
	<input name="menname" type="text" size="30" maxlength="30" value="{$para.menname}" style="ime-mode:active;" {if $err.MenName}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>
	</td>
</tr>
{*<tr>
	<th>{$smarty.const._KANA_}</th>
	<td colspan="3">
	<input name="menname2" type="text" size="30" maxlength="60" value="{$para.menname2}" style="ime-mode:active;" {if $mode=='ref'}class="textBox_r" readonly{/if}>
	</td>
</tr>*}
<tr>
	<th>抽選受付</th>
	<td colspan="3">
	{html_radios name=pulloutukemnflg options=$pulloutukemnflg_arr checked=$para.pulloutukemnflg|default:'1' separator="&nbsp"}
	</td>
</tr>
<tr>
	<th>定員</th>
	<td colspan="3">
	<input name="teiin" type="text" value="{$para.teiin|default:0}" size="12
" maxlength="8" style="text-align:right;ime-mode:disabled;" {if $err.Teiin}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>人
	</td>
</tr>
<tr>
	<th width="100">最小利用人数</th>
	<td colspan="3">
	<input name="teiin_min" type="text" value="{$para.teiin_min|default:0}" size="12" maxlength="8" style="text-align:right;ime-mode:disabled;" {if $err.Teiin_min}class="error"{/if} {if $mode=='ref'}class="textBox_r" readonly{/if}>人
	</td>
</tr>
<tr>
	<td colspan="4" class="no-border" align="center">
{if $baseMode ne 'ref'}
	{if $mode == 'reg'}
	<input type="submit" name="commitBtn" value="登録" {if $success == 1}disabled{/if}>&nbsp;&nbsp;
	{elseif $mode=='mod'}
	<input type="submit" name="commitBtn" value="更新">&nbsp;&nbsp;
	{/if}
{/if}
	<input type="button" name="backBtn" onclick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="mcd" value="{$req.mcd}">
</form>

{include file='footer.tpl'}
