{include file="header.tpl"}

{include file="page-header.tpl"}
<!--user_menu.tpl-->

{if $notice}
<div class="oshirase">
	<h3>{$smarty.const.OR_INFORMATION_FROM_SYSTEM}</h3>
	<p class="f-red">{$notice}</p>
</div>
{/if}

<div class="usermenu2">
	<ul>
	{strip}
	<li><a href="index.php?op={$ShisetsuSentaku}"><img src="image/smart/shisetsu-bt.png" alt="利用施設から検索" width="95" height="100" /></a></li>
	<li><a href="index.php?op=preset"><img src="image/smart/yokutsukau.png" alt="よく使う施設" width="95" height="100" /></a></li>
	<li><a href="index.php?op=list"><img src="image/smart/yoyaku-kakunin.png" alt="申込み状況の確認、取消" width="95" height="100" /></a></li>
	<li><a href="index.php?op=history"><img src="image/smart/rireki.png" alt="履歴の確認" width="95" height="100" /></a></li>
	{/strip}
	</ul>
</div>

{include file="footer.tpl"}
