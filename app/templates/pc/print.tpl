<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<html>
<head><title>{$smarty.const._SYSTEM_NAME_}</title>
<meta http-equiv=CONTENT-SCRIPT-TYPE content=text/javascript>
</head>
<link href="css/style.css" rel="stylesheet" type="text/css">
<body>
<center>
<table width=615 border=0>
<tbody>
<tr><td>&nbsp;</td></tr>
<tr>
  <td>
  <table width="100%" border=0>
  <tbody>
  <tr>
    <td width="100%" height="40" align="center" bordercolor="#000099" bgcolor="#CCCCCC">　-　{$smarty.const._SYSTEM_NAME_}　-　　<font size="4"><strong>{$ConditionName}内容確認票（印刷用）</strong></font></td>
  </tr>
  <tr>
    <td align="right"><br><a href="#" onclick="window.print();"><img src="image/print.gif" alt="この画面を印刷する" width="48" height="15" border="0"></a>｜<a href="#" onclick="window.close();"><img src="image/close.gif" alt="この画面を閉じる" width="58" height="15" border="0"></a></td>
  </tr>
  <tr>
    <td align="center"><hr width="600" size="1" noshade></td>
  </tr>
  <tr>
    <td>■申込内容</td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#fcbbb6">
      <table cellspacing="1" cellpadding="3" width="100%">
      <tbody>
      <tr>
        <td noWrap align="center" width="20%" bgcolor="#f9edca">利用者ID</td>
        <td width="80%" bgcolor="#fff2ee">{$UID}</td>
      </tr>
      <tr>
        <td align="center" width="20%" bgcolor="#f9edca">お名前</td>
        <td width="80%" bgcolor="#fff2ee">{$Name}&nbsp;様</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#f9edca">受付番号</td>
        <td bgcolor="#fff2ee">{$YoyakuNum}</td>
      </tr>
      <tr>
        <td align="center" width="20%" bgcolor="#f9edca">受付日時</td>
        <td width="80%" bgcolor="#fff2ee">{$AppDate}&nbsp;{$AppTime}</td>
      </tr>
      <tr>
        <td align="center" width="20%" bgcolor="#f9edca">受付内容</td>
        <td width="80%" bgcolor="#fff2ee">
          <table width="477" border=0 cellPadding=1 cellSpacing=1>
          <tbody>
          <tr>
            <td width="110"><font size="3">利用日</font></td>
            <td width="347"><font size="3">{$info.UseDateDisp}</font></td>
          </tr>
          <tr>
            <td><font size="3">利用時間</font></td>
            <td><font size="3">{$info.UseTime}</font></td>
          </tr>
          <tr>
            <td valign="top"><font size="3">利用施設</font></td>
            {strip}
            <td><font size="3">
              {$info.ShisetsuName}<br>{$info.ShitsujyoName}{if $CombiNo != 0}&nbsp;{$info.CombiName}{/if}
              {if $info.Fuzoku}
              {foreach $info.Fuzoku as $value}
                <br>{$value}
              {/foreach}
              {/if}
              </font></td>
            {/strip}
          </tr>
          <tr>
            <td><font size="3">利用目的</font></td>
            <td><font size="3">{$info.MokutekiName}</font></td>
          </tr>
          <tr>
            <td><font size="3">施設使用料</font></td>
            <td><font size="3">{$Fee}円</font></td>
          </tr>
          <tr>
            <td><font size="3">{$YoyakuCondition}</font></td>
           </tr>
           </tbody>
           </table>
        </td>
      </tr>
    </tbody>
    </table>
  </td>
</tr>
{if $showFeePayLimit}
<tr>
  <td>
    <table width="100%" height="72" cellpadding="3" cellspacing="1" bgcolor="#8992fa">
    <tbody>
    <tr>
      <td align="center" nowrap bgcolor="#eef2ff">【注意事項】<br>
        ・{$showFeePayLimit}に、ご利用料金をお支払いください。
      </td>
    </tr>
    </tbody>
    </table>
  </td>
</tr>
{/if}
{if $smarty.const._FOOTER_MESSAGE_}
<tr>
  <td><span class=size12><br>■お問合せ先</span></td>
</tr>
<tr>
  <td>{$smarty.const._FOOTER_MESSAGE_}</td>
</tr>
{/if}
<tr>
  <td align="center"><hr width="600" size="1" noshade></td>
</tr>
<tr>
  <td align="center">
    <a href="#" onclick="window.close();"><img src="image/close.gif" alt="この画面を閉じる" width="58" height="15" border="0"></a>
  </td>
</tr>
</tbody>
</table>
</center></body></html>
