{include file='header.tpl'}
<!--info_top.tpl-->
<br />
{foreach $recs as $val}
<img src="image/mobile/i-text.gif" width="16" height="16"><a href="index.php?op=info&sCode={$val.shisetsucode}&tDate={$val.tourokudate}&tTime={$val.tourokutime}&seq={$val.seqno}">{$val.title}</a>{if $val.shisetsucode != '000'}<br>　({$val.shisetsuname}){/if}
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#bbbbbb;background:#bbbbbb;border:1px solid #bbbbbb;" />
{foreachelse}
{$smarty.const.OR_NO_NEWS}
{/foreach}
<br />

{include file='footer.tpl'}
