<!--/conts-->
</div>
</section>

<div id="footer">
<nav class="gnav">
<ul>
{if $BACK_LINK != ''}<li><a href="{$BACK_LINK}" class="back">戻る</a></li>{/if}
{if $MODE == 1}
	{if $hiddenUserButton != 1}
	<li><a href="index.php?op=user_menu" class="user">ユーザーメニュー</a></li>
	{/if}
	<li><a href="{$TOP_LINK|default:'index.php'}" class="logout">ログアウト</a></li>
{else}		
	<li><a href="{$TOP_LINK|default:'index.php'}" class="top">トップへ</a></li>
{/if}
</ul>
</nav>
<address>&copy;2011-2012 OpenReaf&reg;</address>
</div>

<!--/wrap top-->
</div>
</section>
</body>
</html>
