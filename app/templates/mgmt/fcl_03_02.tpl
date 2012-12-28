{include file='header.tpl'}
<!-- templates fcl_03_02.tpl -->

{literal}
<script type="text/javascript" language="javascript">
<!--
function btn_disabled(shitsujyo_kbn)
{
	if(shitsujyo_kbn == '3' || shitsujyo_kbn == '4') {
		document.forma.purpose.disabled = true;
		document.forma.men.disabled = true;
		document.forma.schedule.disabled = true;
		document.forma.combination.disabled = true;
	}
	if(shitsujyo_kbn == '4') {
		document.forma.restriction.disabled = true;
		document.forma.close.disabled = true;
	}
	if(shitsujyo_kbn != '2') {
		document.forma.fuzoku.disabled = true;
	}
}
// -->
</script>
{/literal}

<body onLoad="btn_disabled('{$rec.shitsujyokbn}')">
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
施設管理 &gt; <a href="index.php?op=fcl_01_01_list">施設選択</a> &gt; <a href="index.php?op=fcl_02_02_list&scd={$req.scd}">室場選択</a> &gt; <strong><u>処理選択</u></strong>
</div>

<h2 class="subtitle01">処理選択</h2>

<div class="margin-box">
<p>
室場の情報を{if $req.type == 'ref'}照会{else}変更{/if}します。以下のメニューから選択し、入力してください。
</p>

<input type="button" value="室場選択へ戻る" class="btn-01" onClick="location.href='index.php?op=fcl_02_02_list&scd={$req.scd}';">
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
</div>
<div id="room-menu" align="center">
<form name="forma" method="post" action="index.php">
<input type="hidden" name="scd" value="{$req.scd}">
<input type="hidden" name="rcd" value="{$req.rcd}">
<input type="hidden" name="type" value="{$req.type}">
<input type="hidden" name="op" value="">
<table width="70%">
{foreach $menu_arr as $key => $val}
	{if $val@iteration mod 3 == 1}<tr height="50" valign="middle">{/if}
	<td align="center" width="33%">
	{if $val.label == ''}&nbsp;{else}<input type="button" name="{$key}" value="{$val.label}" class="btn" onclick="submitTo(this.form, '{$val.op}')">{/if}
	</td>
	{if $val@iteration mod 3 == 0}</tr>{/if}
{/foreach}
{if $val@iteration mod 3 == 1}<td>&nbsp;</td><td>&nbsp;</td></tr>{/if}
{if $val@iteration mod 3 == 2}<td>&nbsp;</td></tr>{/if}
</table>
</form>
</div>

{include file='footer.tpl'}
