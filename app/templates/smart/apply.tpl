{include file="header.tpl"}

{include file="page-header.tpl"}
<!--apply.tpl-->

<div class="conf">
<h3><span>{$smarty.const.OR_USE_INFORMATION}</span></h3>

■{$smarty.const.OR_RECEIPT_NUMBER}&nbsp;{$YoyakuNum}<br>

<table>
<tr>
	<th>{$smarty.const.OR_DATE_AND_TIME}</th>
	<td>{$info.UseDateDisp}&nbsp;{$info.UseTime}</td>
</tr>
<tr>
	<th>{$smarty.const.OR_REQUESTED_FACILITY}</th>	
	<td>{$info.ShisetsuName}<br />
	{$info.ShitsujyoName}
	{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
	{if $info.Fuzoku}
		{foreach $info.Fuzoku as $value}
		<br />&nbsp;{$value}
		{/foreach}
	{/if}
	</td>
</tr>
<tr>
	<th>{$smarty.const.OR_USAGE_FEE}</th>
	<td>{$Fee}円</td>
</tr>
<tr>
	<th>{$smarty.const.OR_STATUS}</th>
	<td>{$YoyakuCondition}</td>
</tr>
</table>

</div>

{if $showFeePayLimit}
<p align="left" class="f-red">
{$smarty.const.OR_ATTENTION}<br />
{$showFeePayLimit}に、ご利用料金をお支払いください。
</p>
{/if}

<div align="center">
<a href="{$REPEAT_LINK}"><img src="image/smart/repet.png" alt="繰り返し予約"></a>
<a href='index.php?op=apply&preset=1'><img src="image/smart/favorite.png" alt="よく使う施設への登録"></a>
</div>

{include file="footer.tpl"}
