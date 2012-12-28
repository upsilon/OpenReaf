{include file='header.tpl'}

<section>
<div id="top">
	<header>
	<h1><img src="image/smart/logo.gif" width="100%" alt="公共施設予約システム　OPENREAF" /></h1>
	</header>
</div>
	<section>
		<div id="conts">
		<div class="close">
		{$SiteCloseMessage}
		</div>
		{if $smarty.const._FOOTER_MESSAGE_}
		<div class="top-tel">	
		□{$smarty.const._FOOTER_MESSAGE_}
		</div>
		{/if}
	</div>
	<div id="footer">
        <address>&copy;2011-2012 OpenReaf&reg;</address>
        </div>
<!--/wrap top-->
</div>
</section>
</body>
</html>
