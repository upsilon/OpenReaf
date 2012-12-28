<section>
<div id="wrap">
<header>
<h1><img src="image/smart/openreaf_logo.png" alt="公共施設予約システム OPENREAF" /></h1>
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
</header>

<section id="next">
{if $condition}<h2 class="title01">{$condition}</h2>{/if}
<div id="conts">
	{if $message}<p class="message">{$message}</p>{/if}
