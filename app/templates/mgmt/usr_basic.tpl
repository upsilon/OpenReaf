<div class="margin-box">
{if $aSystem.userlimitdispflg != '1'}
<input type="hidden" name="userlimityear" value="2100">
<input type="hidden" name="userlimitmonth" value="03">
<input type="hidden" name="userlimitday" value="31">
{/if}
<table class="ri-table">
<tr>
	<th width="80">{$col.userid[0]}</th>
	<td><input type="text" name="userid" value="{$para.userid}" {$err.userid} size="20" maxlength="128" {if $autoAssign}style='background-color:#ffffcc;font-color:#000000;' readonly{else}style="ime-mode:disabled;"{/if}></td>
</tr>
<tr>
	<th>{$col.firstapplydate[0]}</th>
	<td>
	西暦&nbsp;<input type="text" name="firstapplydateyear" value="{$para.firstapplydateyear}" {$err.firstapplydate} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;年&nbsp;<input type="text" name="firstapplydatemonth" value="{$para.firstapplydatemonth}" {$err.firstapplydate} size="2" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;月&nbsp;<input type="text" name="firstapplydateday" value="{$para.firstapplydateday}" {$err.firstapplydate} size="2" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;日&nbsp;{if $mode == 'reg' || $mode == 'mod'}（半角数字）{if $col.firstapplydate[2]}<span class="f-red">&nbsp;(必須)</span>{/if}{/if}
	</td>
</tr>
{if $mode != 'reg' && $mode != 'mod'}
<tr>
	<th>{$col.stoperasedate[0]}</th>
	<td><input type="text" name="stoperasedate" value="{$para.stoperasedate}" {$err.stoperasedate} size="10" maxlength="8" {$input_control}>{if $para.userjyoutaikbn != '1'}&nbsp;状態:&nbsp;{$col.userjyoutaikbn[4][$para.userjyoutaikbn]}{/if}</td>
</tr>
{/if}
<tr>
	<th>{$col.userareakbn[0]}</th>
	<td {$err.userareakbn}>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="userareakbn" options=$col.userareakbn[4] selected=$para.userareakbn disabled=true}
	{else}
	{html_radios name="userareakbn" options=$col.userareakbn[4] selected=$para.userareakbn}{if $col.userareakbn[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.kojindankbn[0]}</th>
	<td {$err.kojindankbn}>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="kojindankbn" options=$col.kojindankbn[4] selected=$para.kojindankbn disabled=true}
	{else}
	{html_radios name="kojindankbn" options=$col.kojindankbn[4] selected=$para.kojindankbn}{if $col.kojindankbn[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.usekbn[0]}</th>
	<td>
	<select name="usekbn" {$button_control}>
	<option label="" value="">&nbsp;</option>
	{html_options options=$aFeeKbn selected=$para.usekbn}
	</select>{if $col.usekbn[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	</td>
</tr>
</table>
</div>

<h4 class="subtitle02">利用者</h4>
<div class="margin-box">
<table class="ri-table">
<tr>
	<td>&nbsp;</td>
	<td>氏　名（団体の場合は団体名）</td>
</tr>
<tr>
	<th width="80">漢字</th>
	<td><input type="text" name="namesei" value="{$para.namesei}" {$err.namesei} size="60" maxlength="128" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}>{if ($mode == 'reg' || $mode == 'mod') && $col.namesei[2]}<span class="f-red">(必須)</span>{/if}</td>
</tr>
<tr>
	<th>{$smarty.const._KANA_}</th>
	<td><input type="text" name="nameseikana" value="{$para.nameseikana}" {$err.nameseikana} size="60" maxlength="128" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}>{if ($mode == 'reg' || $mode == 'mod') && $col.nameseikana[2]}<span class="f-red">(必須)</span>{/if}</td>
</tr>
<tr>
	<th>{$col.hyoujimei[0]}</th>
	<td><input type="text" name="hyoujimei" value="{$para.hyoujimei}" {$err.hyoujimei} size="20" maxlength="20" {$input_control} ></td>
</tr>
</table>

<br />

<table class="ri-table">
<tr>
	<td colspan="2" class="bg-black" style="padding:3px; text-align:center">
	<strong>代表者（団体のみ）</strong>
	</td>
	<td colspan="2" class="bg-black" style="padding:3px; text-align:center">
	<strong>担当者（団体のみ）</strong>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>氏　名</td>
	<td>&nbsp;</td>
	<td>氏　名</td>
</tr>
<tr>
	<th width="80">漢字</th>
	<td><input type="text" name="headnamesei" value="{$para.headnamesei}" {$err.headnamesei} size="24" maxlength="32" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}></td>
	<th width="80">漢字</th>
	<td><input type="text" name="contactname" value="{$para.contactname}" {$err.contactname} size="24" maxlength="32" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}></td>
</tr>
<tr>
	<th width="80">{$smarty.const._KANA_}</th>
	<td><input type="text" name="headnameseikana" value="{$para.headnameseikana}" {$err.headnameseikana} size="24" maxlength="64" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}></td>
	<th width="80">{$smarty.const._KANA_}</th>
	<td><input type="text" name="contactnamekana" value="{$para.contactnamekana}" {$err.contactnamekana} size="24" maxlength="64" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<th>連絡先</th>
	<td>
	<input type="text" name="telno31" value="{$para.telno31}" {$err.telno31} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno32" value="{$para.telno32}" {$err.telno32} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno33" value="{$para.telno33}" {$err.telno33} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（半角数字）{/if}
	</td>
</tr>
</table>
</div>

<h4 class="subtitle02">連絡先等（団体の場合は代表者）</h4>
<div class="margin-box">
<table class="ri-table">
<tr>
	<th width="150">郵便番号</th>
	<td>
	<input type="text" name="postno1" value="{$para.postno1}" id="PostNo1" {$err.postno1} size="3" maxlength="3" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="postno2" value="{$para.postno2}" id="PostNo2" {$err.postno2} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（半角数字）{if $col.postno1[2]}<span class="f-red">(必須)</span>{/if}{if $smarty.const._postaddress_flg_}<input type="button" name="btn" value="住所検索" size="8" onclick="submitZipCode()">{/if}{/if}&nbsp;&nbsp;<span id="posterror" style="color:red"></span>
	</td>
</tr>
<tr>
	<th>{$col.adr1[0]}</th>
	<td>
	<input type="text" name="adr1" value="{$para.adr1}" id="Adr1" {$err.adr1} size="60" maxlength="128" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}>{if ($mode == 'reg' || $mode == 'mod') && $col.adr1[2]}<span class="f-red">(必須)</span>{/if}
	</td>
</tr>
<tr>
	<th>{$col.adr2[0]}</th>
	<td><input type="text" name="adr2" value="{$para.adr2}" {$err.adr2} size="60" maxlength="128" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}></td>
</tr>
<tr>
	<th>連絡先</th>
	<td>
	<input type="text" name="telno11" value="{$para.telno11}" {$err.telno11} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno12" value="{$para.telno12}" {$err.telno12} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno13" value="{$para.telno13}" {$err.telno13} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（半角数字）{if $col.telno11[2]}<span class="f-red">(必須)</span>{/if}{/if} 緊急連絡先
	<input type="text" name="telno21" value="{$para.telno21}" {$err.telno21} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno22" value="{$para.telno22}" {$err.telno22} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="telno23" value="{$para.telno23}" {$err.telno23} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（半角数字）{/if}
	</td>
</tr>
<tr>
	<th>FAX</th>
	<td>
	<input type="text" name="faxno1" value="{$para.faxno1}" {$err.faxno1} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="faxno2" value="{$para.faxno2}" {$err.faxno2} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}> -
	<input type="text" name="faxno3" value="{$para.faxno3}" {$err.faxno3} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（半角数字）{/if}
	</td>
</tr>
<tr>
	<th>{$col.mailadr[0]}</th>
	<td>
	<input type="text" name="mailadr" value="{$para.mailadr}" {$err.mailadr} size="40" maxlength="255" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>
	</td>
</tr>
<tr>
	<th>{$col.mailsendflg[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="mailsendflg" options=$col.mailsendflg[4] selected=$para.mailsendflg|default:0 disabled=true}
	{else}
	{html_radios name="mailsendflg" options=$col.mailsendflg[4] selected=$para.mailsendflg|default:0}
	{/if}
	</td>
</tr>
<tr>
	<th>生年月日</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="nengoukbn" options=$col.nengoukbn[4] selected=$para.nengoukbn|default:0 disabled=true}
	{else}
	{html_radios name="nengoukbn" options=$col.nengoukbn[4] selected=$para.nengoukbn|default:0}
	{/if}
	<input type="text" name="bdayyear" value="{$para.bdayyear}" {$err.bdayyear} size="1" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>年
	<input type="text" name="bdaymonth" value="{$para.bdaymonth}" {$err.bdaymonth} size="1" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>月
	<input type="text" name="bdayday" value="{$para.bdayday}" {$err.bdayday} size="1" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>日{if $mode == 'reg' || $mode == 'mod'}（半角数字）{if $col.bdayyear[2] || $col.bdaymonth[2] || $col.bdayday[2]}<span class="f-red">(必須)</span>{/if}{/if}
	</td>
</tr>
<tr>
	<th>{$col.seibetsukbn[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="seibetsukbn" options=$col.seibetsukbn[4] selected=$para.seibetsukbn|default:0 disabled=true}
	{else}
	{html_radios name="seibetsukbn" options=$col.seibetsukbn[4] selected=$para.seibetsukbn|default:0}
	{/if}
	</td>
</tr>
{****
<tr>
	<th>{$col.mainusepropose[0]</th>
	<td><input type="text" name="mainusepropose" value="{$para.mainusepropose}" {$err.mainusepropose} size="30" maxlength="30" {$input_control} >
	</td>
</tr>
****}
{***
<tr>
	<td width="90px" colspan="2" align="right">現在パスワード&nbsp;&nbsp;</td>
	<td width="560px"><input type="text" name="nowpwd" value="{$para.pwd}" {$err.pwd} size="12" maxlength="{$aSystem.pwdlng}" style='background-color:#ffffcc;font-color:#000000;' readonly></td>
</tr>
***}
<tr>
	<th>{$col.pwd[0]}</th>
	<td><input type="text" name="pwd" value="{$para.pwd}" {$err.pwd} size="12" maxlength="{$aSystem.pwdlng}" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>{if $mode == 'reg' || $mode == 'mod'}（{$aInputType[$aSystem.pwdtype]}）{if $col.pwd[2]}<span class="f-red">&nbsp;(必須)</span>{/if}{/if}
	{if $aSystem.userpassautoflg==1 && ($mode == 'reg' || $mode == 'mod')}
	<input type="button" value="パスワード発行" onclick="setPassWord({$aSystem.pwdlngmin}, {$aSystem.pwdtype});">
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.kouseijinnin[0]}</th>
	<td>
	<input type="text" name="kouseijinnin" value="{$para.kouseijinnin}" {$err.kouseijinnin} size="1" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>　人{if ($mode == 'reg' || $mode == 'mod') && $col.kouseijinnin[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	&nbsp;{$col.kouseijinmeisai1[0]}
	<input type="text" name="kouseijinmeisai1" value="{$para.kouseijinmeisai1}" {$err.kouseijinmeisai1} size="1" maxlength="5" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>　人
	　{$col.kouseijinmeisai2[0]}
	<input type="text" name="kouseijinmeisai2" value="{$para.kouseijinmeisai2}"{$err.kouseijinmeisai2} size="1" maxlength="5" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>　人
	　{$col.kouseijinmeisai3[0]}
	<input type="text" name="kouseijinmeisai3" value="{$para.kouseijinmeisai3}"{$err.kouseijinmeisai3} size="1" maxlength="5" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>　人
	　{$col.kouseijinmeisai4[0]}
	<input type="text" name="kouseijinmeisai4" value="{$para.kouseijinmeisai4}" {$err.kouseijinmeisai4} size="1" maxlength="5" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>　人 （半角数字）
	</td>
</tr>
{*<tr>
	<th>{$col.koukaikbn[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="koukaikbn" options=$col.koukaikbn[4] selected=$para.koukaikbn disabled=true}
	{else}
	{html_radios name="koukaikbn" options=$col.koukaikbn[4] selected=$para.koukaikbn|default:0}
	{/if}
	</td>
</tr>*}
<tr>
	<th>{$col.userkubun[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="userkubun" options=$col.userkubun[4] selected=$para.userkubun disabled=true}
	{else}
	{html_radios name="userkubun" options=$col.userkubun[4] selected=$para.userkubun}
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.honninkakuninkubun[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="honninkakuninkubun" options=$col.honninkakuninkubun[4] selected=$para.honninkakuninkubun disabled=true}
	{else}
	{html_radios name="honninkakuninkubun" options=$col.honninkakuninkubun[4] selected=$para.honninkakuninkubun}
	{/if}
	</td>
</tr>
{*<tr>
	<th>{$col.carduse[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="carduse" options=$col.carduse[4] selected=$para.carduse disabled=true}
	{else}
	{html_radios name="carduse" options=$col.carduse[4] selected=$para.carduse}
	{/if}
	</td>
</tr>*}
{if $aSystem.userlimitdispflg == '1'}
<tr>
	<th>{$col.userlimit[0]}</th>
	<td>
	西暦&nbsp;<input type="text" name="userlimityear" value="{$para.userlimityear}" {$err.userlimit} size="4" maxlength="4" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;年&nbsp;<input type="text" name="userlimitmonth" value="{$para.userlimitmonth}" {$err.userlimit} size="2" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;月&nbsp;<input type="text" name="userlimitday" value="{$para.userlimitday}" {$err.userlimit} size="2" maxlength="2" {if $input_control}{$input_control}{else}style="ime-mode:disabled;"{/if}>&nbsp;日&nbsp;{if $mode == 'reg' || $mode == 'mod'}（半角数字）{if $col.userlimit[2]}<span class="f-red">&nbsp;(必須)</span>{/if}{/if}
	</td>
</tr>
{/if}
<tr>
	<th>{$col.yoyakukyokaflg[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="yoyakukyokaflg" options=$col.yoyakukyokaflg[4] selected=$para.yoyakukyokaflg disabled=true}（職員による予約も不可）
	{else}
	{html_radios name="yoyakukyokaflg" options=$col.yoyakukyokaflg[4] selected=$para.yoyakukyokaflg|default:1}（職員による予約も不可）{if $col.yoyakukyokaflg[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.yoyakukyokaflgweb[0]}</th>
	<td>
	{if $mode != 'reg' && $mode != 'mod'}
	{html_radios name="yoyakukyokaflgweb" options=$col.yoyakukyokaflgweb[4] selected=$para.yoyakukyokaflgweb disabled=true}
	{else}
	{html_radios name="yoyakukyokaflgweb" options=$col.yoyakukyokaflgweb[4] selected=$para.yoyakukyokaflgweb|default:1}{if $col.yoyakukyokaflgweb[2]}<span class="f-red">&nbsp;(必須)</span>{/if}
	{/if}
	</td>
</tr>
<tr>
	<th>{$col.nojiyu[0]}</th>
	<td>
	<textarea name="nojiyu" cols="45" rows="3" {if $input_control}{$input_control}{else}style="ime-mode:active;"{/if}>{$para.nojiyu}</textarea>
	</td>
</tr>
{if $mode != 'reg'}
<tr>
	<th>更新日時・更新者</th>
	<td>{$para.UpdDateView}&nbsp;{$para.UpdTimeView}&nbsp;{$para.UpdStaffName}</td>
</tr>
{/if}
</table>
</div>
