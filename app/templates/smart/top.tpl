{include file='header.tpl'}

<section>
<div id="top">
<header><h1><img src="image/smart/logo.gif" width="100%" alt="公共施設予約システム OPENREAF" /></h1></header>

	<section>
	<div id="conts">
		<noscript>
		<p style="font-size:120%;" class="f-ore">{$smarty.const.OR_NOSCRIPT}</p>
		</noscript>	
		<nav class="topnav">
		<ul>
		<li><a href="index.php?op={$ShisetsuSentaku}"><img src="image/smart/shisetsu-bt.png" alt="利用施設から検索" width="100" height="100" /></a></li>
		<li><a href="{$NEXT_URL}?op=user_menu"><img src="image/smart/user-bt.png" alt="マイページ" width="100" height="100" /></a></li>
		<li><a href="index.php?op=info_top"><img src="image/smart/news-bt.png" alt="お知らせ一覧へ" width="100" height="100" /></a></li>
		</ul>
		</nav>
		{if $smarty.const._FOOTER_MESSAGE_}
		<div class="conts-botm">
		{$smarty.const.OR_SHORT_NOTICE}
		</div>
		<div class="top-tel">	
		{$smarty.const._FOOTER_MESSAGE_}
		</div>		
		{/if}
	<!--/conts-->
	</div>
	</section>

<div id="footer">
<address>&copy;2011-2012 OpenReaf&reg;</address>
</div>

<!--/wrap top-->
</div>
</section>
</body>
</html>
