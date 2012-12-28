{include file='header.tpl'}
<!-- templates mst_01_03_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
発番管理 &gt; <strong><u>発番情報</u></strong>
</div>

<h2 class="subtitle01">発番情報</h2>

<div class="margin-box">
{foreach $rec as $key => $value}
{if $value.enabledSaibanFlg}
	{assign var = "rowSpan" value = "5"}
{else}
	{assign var = "rowSpan" value = "4"}
{/if}
<table class="ri-table">
<tr>
	<td rowspan="{$rowSpan}" nowrap width="100" align="center"><strong>{$value.saibanName}</strong></td>
	<th width="100">番号</th>
	<td width="100">{$value.saibanno}</td>
	<td rowspan="{$rowSpan}" width="60" align="center" class="no-border">
	  <input type="button" name="editBtn" value=" 変更" onClick="window.location.href='index.php?op=mst_01_03_number&SaibanCode={$value.saibancode}';">
	</td>
</tr>
<tr>
	<th>ケタ数</th>
	<td>{$value.saibannolng}</td>
</tr>
<tr>
	<th>プレフィックス</th>
	<td>{$value.prefix|default:'なし'}</td>
</tr>
<tr>
	<th>サフィックス</th>
	<td>{$value.suffix|default:'なし'}</td>
</tr>
{if $value.enabledSaibanFlg}
<tr>
	<th>自動発番</th>
	<td>{$value.showSaibanFlg}</td>
</tr>
{/if}
</table>
<br/>現在の出力値&nbsp;：&nbsp;{$value.outputValue}<br/>
<br/>
{/foreach}
</div>
{include file='footer.tpl'}
