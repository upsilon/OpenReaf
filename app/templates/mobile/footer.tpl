<a name="footer" id="footer"></a>

<div style="background:#eeeeee;border-top:1px solid #bbbbbb;border-bottom:1px solid #bbbbbb;padding:3px 0;margin-top:5px;">
{if $BACK_LINK != ''}
<img src="image/mobile/i-left.gif" alt="戻る" width="16" height="16"><a href="{$BACK_LINK}" accesskey="*">戻る[*]</a><br>
{/if}
{if $MODE == 1}
	{if $hiddenUserButton != 1}
	<img src="image/mobile/i-user.gif" alt="利用者メニューへ" width="16" height="16"><a href="index.php?op=user_menu" accesskey="0">利用者メニューへ[0]</a><br>
	{/if}
	<img src="image/mobile/i-logout.gif" alt="ログアウト" width="16" height="16"><a href="{$TOP_LINK|default:'index.php'}" accesskey="#">ログアウト[#]</a><br>
{else}
	<img src="image/mobile/i-home.gif" alt="トップへ" width="16" height="16"><a href="{$TOP_LINK|default:'index.php'}" accesskey="#">トップへ[#]</a><br>
{/if}
</div>
<div style="padding:5px 0;text-align:center;">
<img src="image/openreaf.gif" alt="オープンリーフ公式サイトへ" width="100" height="27">
</div>

</div>
</body>
</html>
