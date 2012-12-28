{include file="header.tpl"}

{if $notice}
<div class="oshirase">
<strong>{$smarty.const.OR_INFORMATION_FROM_SYSTEM}</strong><br />
{$notice}
</div>
{/if}

<div align="center">
    <a href="#" onclick="gotoPage('{$ShisetsuSentaku}', '', '');"><img src="image/yoyaku-2.gif" alt="予約・抽選の申込み" /></a>
    <a href="#" onclick="gotoPage('preset', '', '');"><img src="image/yokutsukau.gif" alt="よく使う施設" /></a>
    <a href="#" onclick="gotoPage('list', '', '');"><img src="image/yoyaku-3.gif" alt="予約・抽選申込み状況の確認、取消" /></a>
    <a href="#" onclick="gotoPage('history', '', '');"><img src="image/rireki.gif" alt="履歴の確認" /></a>
</div>

{include file="footer.tpl"}
