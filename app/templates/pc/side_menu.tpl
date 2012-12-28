<!--side_menu.tpl-->
<div id="sentaku-area">
<!--sentaku-area-->

<div id="left">
<!--left-->
<h3 class="menu-title">{$smarty.const.OR_APPLICATION_ITEMS}</h3>
<ul id="menu">
    {if $screenflg==1}
    	<li>
        {if $ShisetsuClassName == ''}
            <span class="on">{$smarty.const.OR_CATEGORY}
        {else}
            <span class="edit"><a href="#" onclick="gotoPage('shisetsuclass', '', '');">{$smarty.const.OR_CATEGORY}</a>
                <ul><li><strong>{$ShisetsuClassName}</strong></li></ul>
        {/if}
        </span>
        </li>
        <li>        
        {if $ShisetsuName == '' && $ShisetsuClassName != ''}
            <span class="on">{$smarty.const.OR_FACILITY}
        {elseif $ShisetsuName != '' && $ShisetsuClassName != ''}
            <span class="edit"><a href="#" onclick="gotoPage('shisetsu', '', '');">{$smarty.const.OR_FACILITY}</a>
        {else}
            <span class="off">{$smarty.const.OR_FACILITY}
        {/if}
        {if $ShisetsuName != ''}
            <ul><li><strong>{$ShisetsuName}</strong></li></ul>
        {/if}
        </span>
        </li>
    {else}
        <li>
        {if $ShisetsuName == ''}
            <span class="on">{$smarty.const.OR_FACILITY}
        {else}
            <span class="edit"><a href="#" onclick="gotoPage('shisetsu', '', '');">{$smarty.const.OR_FACILITY}</a>
            <ul><li><strong>{$ShisetsuName}</strong></li></ul>
        {/if}
        </span>
        </li>
    {/if}
    <li>
    {if $ShitsujyoName == '' && $ShisetsuName != ''}
        <span class="on">{$smarty.const.OR_PLACE}
    {elseif $ShitsujyoName != '' && $ShisetsuName != ''}
        <span class="edit"><a href="#" onclick="gotoPage('shitsujyo', '', '');">{$smarty.const.OR_PLACE}</a>
    {else}
        <span class="off">{$smarty.const.OR_PLACE}
    {/if}
    {if $ShitsujyoName != ''}
        <ul><li><strong>{$ShitsujyoName}</strong></li></ul>
    {/if}
    </span>
    </li>
    <li>
    {if $CombiName == '' && $ShitsujyoName != ''}
        <span class="on">{$smarty.const.OR_PIECE}
    {elseif $CombiName != '' && $ShitsujyoName != ''}
        {if $CombiNo == 0}
            <span class="edit">{$smarty.const.OR_PIECE}
        {else}
            <span class="edit"><a href="#" onclick="gotoPage('men', '', '');">{$smarty.const.OR_PIECE}</a>
        {/if}
    {else}
        <span class="off">{$smarty.const.OR_PIECE}
    {/if}
    {if $CombiName != ''}
        <ul><li><strong>{$CombiName}</strong></li></ul>
    {/if}
    </span>
    </li>
    <li>
    {if $UseDateDisp == '' && $CombiName != ''}
        <span class="on">{$smarty.const.OR_DATES}
    {elseif $UseDateDisp != '' && $CombiName != ''}
        <span class="edit"><a href="#" onclick="gotoPage('monthly', '', '');">{$smarty.const.OR_DATES}</a>
    {else}
        <span class="off">{$smarty.const.OR_DATES}
    {/if}
    {if $UseDateDisp != ''}
        <ul><li><strong>{$UseDateDisp}</strong></li></ul>
    {/if}
    </span>
    </li>
    <li>
    {if $UseTime == '' && $UseDateDisp != ''}
        <span class="on">{$smarty.const.OR_HOURS}
    {elseif $UseTime != '' && $UseDateDisp != ''}
        <span class="edit"><a href="#" onclick="gotoPage('daily', '', '');">{$smarty.const.OR_HOURS}</a>
    {else}
        <span class="off">{$smarty.const.OR_HOURS}
    {/if}
    {if $UseTime != ''}
        <ul><li><strong>{$UseTime}</strong></li></ul>
    {/if}
    </span>
    </li>
    <li>
    {if !is_array($FuzokuName) && $UseTime != ''}
        <span class="on">{$smarty.const.OR_OPTIONS}
    {elseif is_array($FuzokuName) && $UseTime != ''}
        <span class="edit"><a href="#" onclick="gotoPage('fuzoku', '', '');">{$smarty.const.OR_OPTIONS}</a>
    {else}
        <span class="off">{$smarty.const.OR_OPTIONS}
    {/if}
    {if count($FuzokuName) != 0}
        {foreach $FuzokuName as $value}
            <ul><li><strong>{$value}</strong></li></ul>
        {/foreach}
    {/if}
    </span>
    </li>
    <li>
    {if $MokutekiName == '' && is_array($FuzokuName)}
        <span class="on">{$smarty.const.OR_PURPOSE}
    {elseif $MokutekiName != '' && is_array($FuzokuName)}
        <span class="edit"><a href="#" onclick="gotoPage('mokuteki', '', '');">{$smarty.const.OR_PURPOSE}</a>
    {else}
        <span class="off">{$smarty.const.OR_PURPOSE}
    {/if}
    {if $MokutekiName != ''}
        <ul><li><strong>{$MokutekiName}</strong></li></ul>
    {/if}
    </span>
    </li>
    {if $MokutekiName != '' && is_array($FuzokuName)}
        <li><span class="on">{$smarty.const.OR_CONFIRMATION}</span></li>
    {/if}
</ul>

<!--/left-->
</div>
