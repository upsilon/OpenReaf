<div id="main">
<!-- templates page-header.tpl -->
{*** main start **}

<div id="header">
{*** ヘッダー ***}
	<h1 id="logo" style="color:{$smarty.const._TITLE_COLOR_};">{$smarty.const._TITLE_}</h1>

	<div id="logout">
        <strong class="day">{$ymd}</strong>{$weekday}：
        <span class="user">担当者名</span><strong class="user-name">{$user_name}</strong>
        <input name="btn_logout" type="button"  class="logout-bt" value="ログアウト" onClick="location.href='index.php?op=logout'">
	</div>
            
{*** ヘッダー終了 ***}
</div>
