<div class="button-area">
<!--button-area-->

{if $MODE == 3}
	<a href="#" onclick="window.close();"><img src="image/button_close.gif" alt="閉じる" width="200" height="40" border="0"></a>
{else}
    {if $BACK_LINK != ''}
	<a href="{$BACK_LINK}"><img src="image/button_back.gif" alt="戻る" width="200" height="40" border="0"></a>
    {elseif $REPEAT_LINK}
	<a href="{$REPEAT_LINK}"><img src="image/button_kurikaeshi.gif" alt="繰り返し予約" width="200" height="40" border="0"></a>
    {/if}
    {if $MODE == 1}
	{if $hiddenUserButton != 1}
	<a href="#" onclick="gotoPage('user_menu', '', '');"><img src="image/button_menu.gif" alt="利用者メニューへ" width="200" height="40" border="0"></a>
	{/if}
	<div align="right" style="margin-right:20px;">
	<a href="{$TOP_LINK|default:'index.php'}"><img src="image/logout.gif" alt="ログアウト" border="0"></a>
	</div>
    {else}
	<a href="{$TOP_LINK|default:'index.php'}"><img src="image/button_top.gif" alt="トップへ" width="200" height="40" border="0"></a>
    {/if}
{/if}
<!--/button-area-->
</div>

<!--/conts-->
</div>
</div>
<hr />

<div id="footer">
<!--footer-->
{$smarty.const._FOOTER_MESSAGE_}
<a href="http://openreaf.jp/" target="_blank"><img src="image/openreaf.gif" alt="オープンリーフ公式サイトへ" width="100" height="27" class="banner"></a>
<!--/footer-->
</div>

<form name="formx" method="post" action="index.php">
<input type="hidden" name="op" value="">
</form>
</body>
</html>
