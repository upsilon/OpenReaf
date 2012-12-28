{include file='header.tpl'}
<!-- templates usr_02_01.tpl -->

<script type="text/javascript" src="script/ajax.js"></script>
{literal}
<script type="text/javascript" language="javascript">
<!--
function confirm_duplicate()
{
	if (confirm("既存利用者との情報重複があります。登録しますか？")) {  
		document.formb.usernameflg.value='1';
		document.formb.submit();
	} else {
		return false;
	}
}
//-->
</script>
{/literal}

{if $is_duplicate}
<body onLoad="confirm_duplicate();">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
利用者管理 &gt; <a href="index.php?op=usr_01_01_search&back=1">利用者検索</a> &gt; <strong><u>{if $mode == 'mod'}利用者情報変更{elseif $mode == 'ref'}利用者情報照会{else}利用者登録{/if}</u></strong>
</div>

<h2 class="subtitle01">{if $mode == 'mod'}利用者情報変更{elseif $mode == 'ref'}利用者情報照会{else}利用者登録{/if}</h2>
<input type="button" value="検索へ戻る" onclick="location.href='index.php?op=usr_01_01_search&back=1';">
<!--基本情報-->
{if $mode != 'reg'}
<ul class="user-reg" id="basic">
<li class="user-reg-title">基本情報</li>
<li><a href="#access">アクセス状況</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>
{/if}

{if $message}<div id="errorbox">{$message}</div>{/if}
<form name="formb" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
<input type="hidden" name="usernameflg" value="0">
<input type="hidden" name="UserID" value="{$UserID}">

{include file='usr_basic.tpl'}

{if $mode != 'ref'}
<table cellspacing="1">
<tr>
  <td width="650" align="center">
    <input type="submit" name="tourokuBtn" value="登録" onclick="return confirm('登録しますか？');" {if $success == 1 && $mode == 'reg'}disabled{/if}>
    {if $mode == 'mod'}
    &nbsp;&nbsp;<input type="button" onclick="openUrl('index.php?op=usr_03_01_pdf&UserID={$UserID}','UserTsuChi')" value="登録通知書">
    {/if}
  </td>
</tr>
</table>
{/if}
</form>
<!--/基本情報-->

{if $mode != 'reg'}

<hr>
<!--アクセス状況-->
<ul class="user-reg" id="access">
<li class="user-reg-title">アクセス状況</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

<div class="margin-box">
<table class="ri-table">
<tr>
	<th>最終ログイン時間</th>
	<td>{$para.LastLogin}</td>
</tr>
<tr>
	<th>パスワードエラー回数</th>
	<td>{$para.loginerr_count}&nbsp;回
	{if $para.LockOut == 1}
	&nbsp;<strong class="f-red">ロックアウト中</strong>
	{/if}
	</td>
</tr>
</table>
</div>

{if $mode == 'mod' && $aSystem.lockoutflg == '1'}
<table width="650" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="button" name="access_status" value="アクセス状態変更画面へ" onclick="location.href='index.php?op=usr_03_06_mod&UserID={$UserID}';" {$button_control}></td>
</tr>
</table>
{/if}
<!--/アクセス状況-->

<!--メッセージ-->
<ul class="user-reg" id="message">
<li class="user-reg-title">メッセージ</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#access">アクセス状況</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

<div class="margin-box">
<table class="ri-table">
<tr>
	<th>メッセージ</th>
	<td>
	<textarea name="notice" cols="60" rows="3" readonly style="background-color:#ffffcc;font-color:#000000;">{$para.notice}</textarea>
	</td>
</tr>
<tr>
	<th>表示可否</th>
	<td>{$dispflg_arr[$para.notice_flg]}</td>
</tr>
<tr>
	<th>表示期間</th>
	<td>{$para.Published}&nbsp;～&nbsp;{$para.Expired}</td>
</tr>
</table>
</div>

{if $mode == 'mod'}
<table width="650" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="button" name="notice" value="メッセージ設定画面へ" onclick="location.href='index.php?op=usr_03_07_mod&UserID={$UserID}';" {$button_control}></td>
</tr>
</table>
{/if}
<!--/メッセージ-->

<!--詳細情報-->
<ul class="user-reg" id="detail">
<li class="user-reg-title">詳細情報</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#access">アクセス状況</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

<div class="margin-box">
<table class="ri-table">
<tr>
	<th>{$col.katudogaiyou[0]}</th>
	<td><input type="text" name="katudogaiyou" value="{$para.katudogaiyou}" size="70" maxlength="70" readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.kaihijyouhou[0]}</th>
	<td><input type="text" name="kaihijyouhou" value="{$para.kaihijyouhou}" readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.katudodate[0]}</th>
	<td><input type="text" name="katudodate" value="{$para.katudodate}" size="30" maxlength="30" readonly style="background-color:#ffffcc;font-color:#000000;"></td>
</tr>
<tr>
	<th>{$col.lecturerjyouhou[0]}</th>
	<td><input type="text" name="lecturerjyouhou" value="{$para.lecturerjyouhou}" readonly style="background-color:#ffffcc;font-color:#000000;">
</tr>
<tr>
	<th>{$col.thanksjyouhou[0]}</th>
	<td><input type="text" name="thanksjyouhou" value="{$para.thanksjyouhou}" size="40" maxlength="40" readonly style="background-color:#ffffcc;font-color:#000000;">
</tr>
<tr>
	<th>{$col.bikou[0]}</th>
	<td>
	<textarea name="bikou" cols="60" rows="3" readonly style="background-color:#ffffcc;font-color:#000000;">{$para.bikou}</textarea>
	</td>
</tr>
</table>
</div>

{if $mode == 'mod'}
<table width="650" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="button" name="touroku_detail" value="詳細情報変更画面へ" onclick="location.href='index.php?op=usr_03_02_mod&UserID={$UserID}';" {$button_control}></td>
</tr>
</table>
{/if}
<!--/詳細情報-->

<!--施設利用権限-->
<ul class="user-reg" id="kengen">
<li class="user-reg-title">施設権限</li>
<li><a href="#basic">基本情報</a></li>
<li><a href="#access">アクセス状況</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#mokuteki">利用目的</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

{if $mode == 'mod'}
<strong>利用者の施設利用権限を設定します。</strong>
<input type="button" name="kengenBtn" class="btn-01" id="btn1" value="利用権限設定画面へ" onclick="location.href='index.php?op=usr_03_03_mod&UserID={$UserID}';" {$button_control} ><br>
{/if}
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
<li><a href="#access">アクセス状況</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#genmen">減免情報</a></li>
</ul>

{if $mode == 'mod'}
<strong>利用者の施設利用目的を設定します。</strong>
<input type="button" name="add_mokuteki" class="btn-01" id="btn1" value="利用目的設定画面へ" onclick="location.href='index.php?op=usr_03_04_mod&UserID={$UserID}';" {$button_control} ><br>
{/if}
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
<li><a href="#access">アクセス状況</a></li>
<li><a href="#message">メッセージ</a></li>
<li><a href="#detail">詳細情報</a></li>
<li><a href="#kengen">施設権限</a></li>
<li><a href="#mokuteki">利用目的</a></li>
</ul>

{if $mode == 'mod'}
<strong>利用者に適用する減免を設定します。</strong>
{/if}
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
	{$genmen_data.koteigenname}&nbsp;
	{if $genmen_data.keizokuflg=="0"}新規{else}継続{/if}
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
{if $mode == 'mod'}
<table width="500" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="button" name="touroku_genmen" value="減免設定画面へ" onclick="location.href='index.php?op=usr_03_05_mod&UserID={$UserID}';" {$button_control} ></td>
</tr>
</table>
{/if}
<!--/減免-->
{/if}
<div id="ajaxContainerDiv" style="display:none"></div>

{include file='footer.tpl'}
