{include file='header.tpl'}

    <div id="navi">
    <a href="#" onclick="gotoPage('{$ShisetsuSentaku}', '', '');"><img src="image/aki.gif" alt="空き状況の確認" width="170" height="170" border="0"></a>
    <a href="#" onclick="gotoPage('login', '', '{$NEXT_URL}?return=user_menu');"><img src="image/yoyaku.gif" alt="マイページ" width="170" height="170" border="0" /></a>
    {if $smarty.const._USE_FACILITY_GUIDE_}
    <a href="#" onclick="gotoPage('guide', '_blank', '');"><img src="image/shisetsu.gif" alt="施設のご案内" width="170" height="170" border="0"></a>
    {/if}
    </div>

    {if $smarty.const._SHOW_GUIDANCE_BUTTON_}
    <div class="main-area">
    <a href="#" onclick="gotoPage('tebiki', '_blank', '');"><img src="image/button-2.gif" alt="ご利用の手引き"></a>
    </div>
    {/if}

    <div id="new">
    <h3><a href="#" onclick="gotoPage('info', '_blank', '');">{$smarty.const.OR_NEWS}</a><a href="#" class="new-bt" onclick="gotoPage('info', '_blank', '');">{$smarty.const.OR_NEWS_LIST}</a><a href="index.php?op=rss_info" target="_blank"><img src="image/rss.gif" alt="RSS"></a></h3>
    <ul style="margin-left:20px;">
    {foreach $recs as $val}
    <li><strong>{$val.title}</strong>{if $val.shisetsucode != '000'}&nbsp({$val.shisetsuname}){/if}</li>
    {foreachelse}
    <li>{$smarty.const.OR_NO_NEWS}</li>
    {/foreach}
    </ul>
    </div>

    <div class="top-message">
    <strong class="f-red">{$smarty.const.OR_NOTICE}</strong><br />{$smarty.const.OR_ATTENTION}
    </div>

<!--/conts-->
</div>
</div>
<hr />

<div id="footer">
{$smarty.const._FOOTER_MESSAGE_}
<a href="http://openreaf.jp/" target="_blank"><img src="image/openreaf.gif" alt="オープンリーフ公式サイトへ" width="100" height="27" class="banner"></a>
<!--/footer-->
</div>

<form name="formx" method="post" action="index.php">
<input type="hidden" name="op" value="">
</form>
</body>
</html>
