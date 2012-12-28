{include file='header.tpl'}
<!-- templates stf_02_02.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
職員管理&nbsp;&gt;&nbsp;
<a href="index.php?op=stf_01_01_top">職員一覧</a>&nbsp;&gt;&nbsp;
<strong><u>職員{if $mode == 'del'}情報削除{elseif $mode == 'abo'}廃止{/if}</u></strong>
</div>

<h2 class="subtitle01">職員{if $mode == 'del'}情報削除{elseif $mode == 'abo'}廃止{/if}</h2>

<div class="margin-box">
<input type="button" name="backBtn" value="一覧へ戻る" onclick="location.href='index.php?op=stf_01_01_top';">
<br />
<br />
{if $message}<div id ="errorbox">{$message}</div>{/if}

<form name="formx" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">

<table width="500">
<tr>
	<td>・職員ID</td>
	<td><input type="text" name="staffid" value="{$para.staffid}" size="12" maxlength="10" class="textBox_r" readonly="true"></td>
	<td nowrap>・職員名</td>
	<td><input type="text" name="StaffName" value="{$para.staffname}" size="20" maxlength="20" class="textBox_r" readonly="true"></td>
</tr>
<tr>
	<td width="80">・所属部署</td>
	<td width="170">
	  {foreach $aBusho as $key => $value}
	  {if $key==$para.bushocode}<input type="text" value="{$value}" readonly="true" class="textBox_r">{/if}
	  {/foreach}

	</td>
	<td nowrap>・職員番号</td>
	<td><input type="text" name="StaffNum" value="{$para.staffnum}" size="20" maxlength="20" class="textBox_r" readonly="true"></td>
</tr>
<tr>
	<td nowrap>・適用開始日</td>
	<td colspan="3"><input type="text" name="AppDateFrom" value="{$para.appdatefrom}" size="20" maxlength="8" class="textBox_r" readonly="true">&nbsp;(入力例：{$smarty.const._EXAMPLE_DATE_})</td>
</tr>
{if $mode == 'abo'}
<tr>
	<td>・廃止日</td>
	<td colspan="3"><input type="text" name="HaishiDate" value="{$para.haishidate}" size="20" maxlength="8" {if $err.HaishiDate}class="error"{/if} {if $aboSuccess}class="textBox_r" readonly="true"{else}style="ime-mode:disabled;"{/if}>&nbsp;(入力例：{$smarty.const._EXAMPLE_DATE_})</td>
</tr>
{/if}
</table>
<br />

<table width="500">
<tr>
	<td width="188">&nbsp;</td>
	<td>
{if $mode == 'abo'}
	  {if $aboSuccess}
	  <input type="submit" name="resumeBtn" value="廃止取消" onClick="return confirm('廃止を取り消しますか？');">
	  {else}
	  <input type="submit" name="expireBtn" value="廃止" onClick="return confirm('廃止しますか？');">
	  {/if}
{else}
	<input type="submit" name="deleteBtn" value="削除" onclick="return confirm('削除しますか？');" {if $success == 1}disabled{/if}>
{/if}
	</td>
</tr>
</table>
</form>
</div>

{include file='footer.tpl'}
