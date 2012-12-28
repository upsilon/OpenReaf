{include file='header.tpl'}
<!-- templates usr_02_04.tpl -->

<body>
<div id="contents">

<p><input type="button" name="closeBtn" value="閉じる" onClick="window.close();"></p>
<!--基本情報-->
<ul class="user-reg" id="basic">
<li class="user-reg-title">基本情報</li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

{include file='usr_basic.tpl'}
<!--/基本情報-->

<!--詳細情報-->
<ul class="user-reg" id="detail">
<li class="user-reg-title">詳細情報</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>
<div class="margin-box">
<table class="ri-table">
<tr>
	<th>{$col.katudogaiyou[0]}</th>
	<td><input type="text" name="katudogaiyou" value="{$para.katudogaiyou}" {$err.katudogaiyou} size="70" maxlength="70" readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.kaihijyouhou[0]}</th>
	<td><input type="text" name="kaihijyouhou" value="{$para.kaihijyouhou}" {$err.kaihijyouhou} readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.katudodate[0]}</th>
	<td><input type="text" name="katudodate" value="{$para.katudodate}" {$err.katudodate} size="30" maxlength="30" readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.lecturerjyouhou[0]}</th>
	<td><input type="text" name="lecturerjyouhou" value="{$para.lecturerjyouhou}" {$err.lecturerjyouhou} readonly style="background-color:#ffffcc;font-color:#000000;">
</tr>
<tr>
	<th>{$col.thanksjyouhou[0]}</th>
	<td><input type="text" name="thanksjyouhou" value="{$para.thanksjyouhou}" {$err.thanksjyouhou} size="40" maxlength="40" readonly style="background-color:#ffffcc;font-color:#000000;">
</tr>
<tr>
	<th>{$col.bikou[0]}</th>
	<td>
	<textarea name="bikou" cols="60" rows="3" readonly style="background-color:#ffffcc;font-color:#000000;">{$bikou}</textarea>
	</td>
</tr>
</table>
</div>
<!--/詳細情報-->

<!--施設利用権限-->
<ul class="user-reg" id="kengen">
<li class="user-reg-title">施設権限</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>
<div class="margin-box" id="itm1">
<table width="350" class="itemtable02">
<tr>
	<th width="300" height="30">施設利用権限</th>
</tr>
{foreach $kengen_list as $kengen}
<tr>
	<td>{$kengen.shisetsuname}</td>
</tr>
{/foreach}
</table>
</div>
<!--/施設利用権限-->

<!--利用目的-->
<ul class="user-reg" id="mokuteki">
<li class="user-reg-title">利用目的</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>
<div class="margin-box" id="itm1">
<table width="350" class="itemtable02">
<tr>
	<th width="300" height="30">利用目的</th>
</tr>
{foreach $mokuteki_list as $mokuteki}
<tr>
	<td>{$mokuteki.mokutekiname}</td>
</tr>
{/foreach}
</table>
</div>
<!--/利用目的-->

<!--減免-->
<ul class="user-reg" id="genmen">
<li class="user-reg-title">減免情報</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
</ul>
<div class="margin-box" id="itm1">
<table border="1"  width="500" class="itemtable02">
<tr>
	<th width="220" height="30">適用減免</th>
	<th width="140" height="30">適用開始</th>
	<th width="140" height="30">有効期限</th>
</tr>
<tr align="center">
{if $genmen_data}
	<td>
	{$genmen_data.KoteiGenName}&nbsp;
	{if $genmen_data.KeizokuFlg=="0"}新規{else}継続{/if}
	</td>
	<td>
	{$genmen_data.AppDayYear}年{$genmen_data.AppDayMonth}月{$genmen_data.AppDayDay}日
	</td>
	<td>
	{$genmen_data.LimitDayYear}年{$genmen_data.LimitDayMonth}月{$genmen_data.LimitDayDay}日
	</td>
{else}
	<td>なし</td>
	<td>-</td>
	<td>-</td>
{/if}
</tr>
</table>
</div>
<!--/減免-->

</div>
</body>
</html>
