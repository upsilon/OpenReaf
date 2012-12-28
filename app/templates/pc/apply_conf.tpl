{include file="header.tpl"}

{include file="side_menu.tpl"}

<!--apply_conf.tpl-->
<div id="right">
<!--right-->

<h3 class="time-title">{$smarty.const.OR_USE_INFORMATION}</h3>

<table width="570" class="table-style">
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_DATE_AND_TIME}</th>
	<td>{$UseDateDisp}&nbsp;{$UseTime}</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_REQUESTED_FACILITY}</th>
	{strip}
	<td>
	{$ShisetsuName}<br>{$ShitsujyoName}{if $CombiNo != 0}&nbsp;{$CombiName}{/if}
        {if $FuzokuName}
        {foreach $FuzokuName as $value}
        <br>{$value}
        {/foreach}
        {/if}
	</td>
	{/strip}
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_SPECIFIED_PURPOSE}</th>
	<td>{$MokutekiName}</td>
</tr>
<tr>
	<th class="bg-gray-2">{$smarty.const.OR_USAGE_FEE}</th>
	<td>{$Fee}円</td>
</tr>
</table>
{if $notice}{$notice}<br>{/if}
<form name="dataForm" method="post" action="index.php">
{if $smarty.const._USE_NUMERICKEY_}
<table id="apply-left">
<tr>
	<td width="90"><a href="#" class="num-bt" onclick="addField_Ninzu(7);"><span>７</span></a></td>
	<td width="90"><a href="#" class="num-bt" onclick="addField_Ninzu(8);"><span>8</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(9);"><span>9</span></a></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(4);"><span>4</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(5);"><span>5</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(6);"><span>6</span></a></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(1);"><span>1</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(2);"><span>2</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(3);"><span>3</span></a></td>
</tr>
<tr>
	<td><a href="#" class="num-bt" onclick="backspaceField_Ninzu();"><span>{$smarty.const.OR_BACK}</span></a></td>
	<td><a href="#" class="num-bt" onclick="addField_Ninzu(0);"><span>0</span></a></td>
	<td><a href="#" class="num-bt" onclick="clearField_Ninzu();"><span>{$smarty.const.OR_CLEAR}</span></a></td>
</tr>
</table>
<div id="apply-right">
{else}
<div id="apply-right" style="margin-left:150px;">
{/if}

{if $showDanjyoNinzuFlg}
<input type="hidden" name="useninzu" value="0">
<div onclick="setObjColor('Ninzu1Title');setObj('Ninzu1');">
<span id="Ninzu1Title"><strong>{$smarty.const.OR_NUMBER_OF_PEOPLE}&nbsp;{$aNinzu.ninzu1[0]}</strong></span>
</div>
<input type="text" name="ninzu1" id="Ninzu1" size="8" maxlength="6" style="font-size:large;text-align:right;ime-mode:disabled;" onclick="setObjColor('Ninzu1Title');setObj('Ninzu1');" value=""><br />
<div onclick="setObjColor('Ninzu2Title');setObj('Ninzu2');">
<span id="Ninzu2Title"><strong>{$smarty.const.OR_NUMBER_OF_PEOPLE}&nbsp;{$aNinzu.ninzu2[0]}</strong></span>
</div>
<input type="text" name="ninzu2" id="Ninzu2" size="8" maxlength="6" style="font-size:large;text-align:right;ime-mode:disabled;" onclick="setObjColor('Ninzu2Title');setObj('Ninzu2');" value="">
{else}
<div onclick="setObjColor('UseNinzuTitle');setObj('UseNinzu');">
<span id="UseNinzuTitle"><strong>{$smarty.const.OR_NUMBER_OF_PEOPLE}</strong></span>
</div>
<input type="text" name="useninzu" id="UseNinzu" size="14" maxlength="11" style="font-size:large;text-align:right;ime-mode:disabled;" onclick="setObjColor('UseNinzuTitle');setObj('UseNinzu');" value="">
<input type="hidden" name="ninzu1" value="0">
<input type="hidden" name="ninzu2" value="0">
{/if}
<br /><br />
<div align="center" style="margin-top:20px;">
<input type="image" src="image/ri-16-button1.gif" alt="{$smarty.const.OR_APPLY}">
</div>
<br clear="left" />

<!-- apply-right end -->
</div>
<input type="hidden" name="op" value="apply">
<input type="hidden" name="apply" value="1">
</form>

<!-- /right -->
</div>
<br clear="left" />

<!-- sentaku-area end -->
</div>

{include file="footer.tpl"}
