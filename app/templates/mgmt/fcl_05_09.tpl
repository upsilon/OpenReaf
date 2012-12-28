{include file='header.tpl'}
<!-- templates fcl_05_09.tpl -->

<body >
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; 
<a href="index.php?op=fcl_01_01_list">施設選択</a> &gt;
<a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt;
<a href="index.php?op=fcl_03_02_menu&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">処理選択</a> &gt;
<a href="index.php?op=fcl_04_09_summary&type={$req.type}&scd={$req.scd}&rcd={$req.rcd}">利用単位組合せ</a> &gt;
<strong><u>組合せ{if $mode == 'del'}削除{elseif $mode == 'ref'}情報参照{else}情報変更{/if}</u></strong></p>
</div>

<h2 class="subtitle01">利用単位組合せ</h2>

<table class="itemtable02">
<tr>
	<th width="50">施設名</th>
	<td width="200">{$rec.shisetsuname}</td>
	<th width="50">室場名</th>
	<td width="160">{$rec.shitsujyoname}</td>
	<th width="70">適用開始日</th>
	<td width="70">{$rec.appdatefrom}</td>
</tr>
</table>
<br>
<h4 class="subtitle02">組合せ{if $mode == 'del'}削除{elseif $mode == 'ref'}情報参照{else}情報変更{/if}</h4>
{if $message}<div id="errorbox">{$message}</div>{else}
<br>
{/if}
<form name="forma" method="post" action="index.php">
<table class="itemtable03">	
<tr>
	<th width="160">組合せ利用単位</th>
	<td>{$para.menname}</td>		
</tr>
<tr>
	<th>組合せ番号</th>
	<td>{$para.combino}</td>		
</tr>
<tr>
	<th>組合せ名</th>
	<td><input type="text" name="combiname" value="{$para.combiname}" size="40" style="ime-mode:active;" {$input_control}></td>		
</tr>
<tr>
	<th>表示順</th>
	<td><input type="text" name="combiskbno" value="{$para.combiskbno}" size="4" maxlength="2" style="text-align:right;ime-mode:disabled;" {$input_control}></td>		
</tr>
<tr>
	<th rowspan="3">インターネット利用設定</th>
	<td>
	{if $mode == 'mod'}
		{html_radios name="openflg" options=$openflg_arr selected=$para.openflg}
	{else}
		{html_radios name="openflg" options=$openflg_arr selected=$para.openflg disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<td>
	室場の公開区分の使用&nbsp;
	{if $mode == 'mod'}
		{html_radios name=openkbn_disable options=$useflg_arr selected=$para.openkbn_disable|default:1}
	{else}
		{html_radios name=openkbn_disable options=$useflg_arr selected=$para.openkbn_disable|default:1 disabled=true}
	{/if}
	</td>
</tr>
<tr>
	<td>
	<table>
	<tr><th align="center">公開区分</th>
	{foreach $month_arr as $month}
	<td>{$month+1}月</td>
	{/foreach}
	</tr>
	<tr><td>
	<input type="button" name="openkbn1" value="予約" class="btn-02" onclick="checkRadio('1');" {if $mode != 'mod'}disabled{/if}><br>
	<input type="button" name="openkbn2" value="空き状況のみ" class="btn-02" onclick="checkRadio('2');" {if $mode != 'mod'}disabled{/if}><br>
	<input type="button" name="openkbn0" value="非表示" class="btn-02" onclick="checkRadio('0');" {if $mode != 'mod'}disabled{/if}>
	</td>
	{foreach $month_arr as $month}
	<td>
	{if $mode == 'mod'}
		{html_radios name="openkbnval[{$month}]" values=$openkbn_arr selected=$para.openkbnval[$month]|default:1 separator="<br>"}
	{else}
		{html_radios name="openkbnval[{$month}]" values=$openkbn_arr selected=$para.openkbnval[$month]|default:1 separator="<br>" disabled=true}
	{/if}
	</td>
	{/foreach}
	</tr>
	</table>
	<label>閉庁日は空き状況のみ<input type="checkbox" name="openkbnval[12]" value="1" {if $para.openkbnval[12] == '1'}checked{/if} {if $mode != 'mod'}disabled{/if}></label>
	&nbsp;<label>休館日は空き状況のみ<input type="checkbox" name="openkbnval[13]" value="1" {if $para.openkbnval[13] == '1'}checked{/if} {if $mode != 'mod'}disabled{/if}></label>
	</td>
	</td>
</tr>
<tr>
	<td colspan="2" align="center" class="no-border"><br>
{if $mode == 'del'}
	<input type="submit" name="deleteBtn" value="削除" onclick="return confirm('削除しますか？');" {if $success == 1}disabled{/if}>
{elseif $mode == 'mod'}
	<input type="submit" name="updateBtn" value="変更">
{/if}
	&nbsp;&nbsp;<input type="button" name="backBtn" onclick="submitTo(this.form, '{$back_url}')" value="戻る">
	</td>
</tr>
</table>
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="cno" value="{$req.cno}">
</form>

{include file='footer.tpl'}
