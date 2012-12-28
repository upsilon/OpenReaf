<!-- templates menu.tpl -->
<div id="navi-area">
<ul id="navi">
        {if $user_type == 1}
	<li class="dead">お知らせ
        {else}
        <li class="over"><a href="index.php?op=nws_01_01_top" class="gre">お知らせ</a>
        {/if}
        </li>	    
	<li class="over"><a href="index.php?op=rsv_00_00_list" class="gre">本日分予約</a></li>
{******************** 利用受付 ***********************}
	{if $user_view.fee == "FORBIDDEN"}
	<li class="dead">使用料等受付/使用許可
	{else}
	<li class="over"><a href="index.php?op=rsv_01_04_search" class="gre">使用料等受付/使用許可</a>
	{/if}
	</li>
{******************** 予約管理 ***********************}
	<li class="off" onmouseover="this.className='on'" onmouseout="this.className='off'">
	<a href="#">予約管理</a>
	<ul>	    	
	{if $user_view.rsv == "FORBIDDEN"}
	<li class="dead">予約状況検索</li>
	<li class="dead">空き状況照会/予約申込</li>
	{else}
	    <li><a href="index.php?op=rsv_01_01_search">予約状況検索</a></li>
	    <li><a href="index.php?op=rsv_01_02_search">空き状況照会/予約申込</a></li>
	{/if}
	</ul>
	</li>
{****************** 利用者管理 ***********************}
	<li class="over">
	<a href="index.php?op=usr_01_01_search" class="gre">利用者管理</a>
	{*******
	<ul>  
	    <li><a href="index.php?op=usr_01_01_search">利用者検索</a></li>
	    {if $user_view.ctz == "FORBIDDEN"}
	    <li class="dead">利用者登録期限更新</li>
	    {else}
	    <li><a href="index.php?op=ctz_01_04_">利用者登録期限更新</a></li>
	    {/if}
	</ul>
	******}
	</li>
{******************** 職員管理 ***********************}
	<li class="off" onmouseover="this.className='on'" onmouseout="this.className='off'"> 
	<a href="#">職員管理</a>
	<ul>  
	{if $user_view.stf == "FORBIDDEN"}
	    <li class="dead">職員一覧</li>
	{else}
	    <li><a href="index.php?op=stf_01_01_top">職員一覧</a></li>
	{/if}
	<li><a href="index.php?op=stf_01_02_pwd">パスワード変更</a></li>
	{***
	{if $showLdapExchangeFlag == 1}
		{if $user_type == 3}
		<li><a href="index.php?op=stf_01_03_ldap">LDAP同期</a></li>
		{/if}
	{/if}
	***}
	</ul>
	</li>
{******************** 施設管理 ***********************}
	<li class="off" onmouseover="this.className='on'" onmouseout="this.className='off'"> 
	<a href="#">施設管理</a>
	<ul>  
	{if $user_view.fcl == "FORBIDDEN"}
	    <li class="dead">施設分類一覧</li>
	    <li class="dead">施設一覧</li>
	{else}
	    <li><a href="index.php?op=fcl_01_03_list">施設分類一覧</a></li>
	    <li><a href="index.php?op=fcl_01_01_list">施設一覧</a></li>
	{/if}
	</ul>
	</li>
{******************* マスタ管理 ***********************}
{if $user_type == 3}
	<li class="off" onmouseover="this.className='on'" onmouseout="this.className='off'"> 
	<a href="#">マスタ管理</a>
	<ul>      
	    <li><a href="index.php?op=mst_01_02_system">システムデータ登録</a></li>
	    <li><a href="index.php?op=mst_01_01_top">マスタデータ登録</a></li>
	    <li><a href="index.php?op=mst_01_03_number">発番管理</a></li>
	</ul>
	</li>		  
{/if}
</ul>
</div>

<div id="contents">
{*** contents start ***}
