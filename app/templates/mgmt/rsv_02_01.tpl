{include file='header.tpl'}
<!-- templates rsv_02_01.tpl -->

<body>
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
{if $fmode=='rsv_01_04'}
	収納管理 &gt; <a href="{$returnUrl}">使用料等受付/使用許可</a>
{else}
	予約管理 &gt; <a href="{$returnUrl}">予約状況検索</a>
{/if}
&nbsp;&gt;&nbsp;<strong><u>予約内容詳細</u></strong>
</div>

<h2 class="subtitle01">予約内容詳細</h2>
<div class="itemtop-area">
<input type="button" value="戻る" onclick="location.href='{$returnUrl}';">
&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="userinfoBtn" value="利用者情報" {if $d.userid === $smarty.const._UNREGISTED_USER_ID_} disabled {/if} onclick="window.open('index.php?op=usr_02_01_03_ref&UserID={$d.userid}&refonly=1#basic', 'user');" >
&nbsp;&nbsp;<input type="button" name="genmenBtn" value="減免情報" {if $d.userid === $smarty.const._UNREGISTED_USER_ID_} disabled {/if} onclick="window.open('index.php?op=usr_02_01_03_ref&UserID={$d.userid}&refonly=1#genmen', 'genmen');" >
</div>

{if $d.class == 'h'}
<h3 class="subtitle03">予約状況</h3>
<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="100">予約状況</th>
	<th width="100">取消</th>
	<td width="380"><span class="f-red">取消前の状態を表示しています</span></td>
</tr>
<tr>
	<th>取消日時</th>
	<td colspan="2">{$d.LstUpdDate}&nbsp;{$d.LstUpdTime}&nbsp;{$d.CancelStaffName|default:'&nbsp;'}</td>
</tr>
<tr>
	<th>取消事由</th>
	<td colspan="2">{$d.CancelJiyu|default:'なし'}</td>
</tr>
</table>
</div>
{/if}

<h3 class="subtitle02">予約情報</h3>
<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="100">予約番号</th>
	<td width="200">{$d.yoyakunum}</td>
	<th width="100">予約区分</th>
	<td width="320">{$d.YoyakuKbnName|default:'&nbsp;'}</td>
</tr>
<tr>
	<th>利用者ID</th>
	<td>{$d.userid}</td>
	<th>利用者名</th>
	<td nowrap>
		{$d.namesei}　{$d.nameseikana}
{if $d.userid === $smarty.const._UNREGISTED_USER_ID_}
		<br>
		<table class="none-table">
		<tr>
			<th>氏名</th><td>{$d.unreg_name}</td>
		</tr>
		<tr>
			<th>住所</th><td>{$d.unreg_address}</td>
		</tr>
		<tr>
			<th>電話番号</th><td>{$d.unreg_tel}</td>
		</tr>
		<tr>
			<th>連絡先</th><td>{$d.unreg_contact}</td>
		</tr>
		</table>
{/if}
	</td>
</tr>
<tr>
	<th>利用日時</th>
	<td colspan="3">{$d.UseDateView}　{$d.UseTime}</td>
</tr>
<tr>
	<th>利用施設</th>
	<td colspan="3">{$d.ShisetsuName}&nbsp;{$d.shitsujyoname}</td>
</tr>
<tr>
	<th>利用目的</th>
	<td>{$d.MokutekiName}</td>
	<th>利用者人数</th>
	<td>{$d.useninzu|default:'0'}人{if $d.ShowDanjyoNinzuFlg}&nbsp;&nbsp;({$aNinzu.ninzu1[0]}{$d.ninzu1}人&nbsp;{$aNinzu.ninzu2[0]}{$d.ninzu2}人){/if}</td>
</tr>
<tr>
	<th>予約状態</th>
	<td>{$d.HonYoyakuKbnName|default:'&nbsp;'}</td>
	<th>催事名</th>
	<td>{$d.yoyakuname}</td>
</tr>
<tr>
	<th>備考</th>
	<td colspan="3">{$d.bikou|nl2br}</td>
</tr>
<tr>
	<th>受付日時</th>
	<td colspan="3">{$d.AppDateView}&nbsp;{$d.AppTimeView}&nbsp;{$d.DaikouStaffName}</td>
</tr>
<tr>
	<th>更新日時</th>
	<td colspan="3">{$d.UpdDateView}&nbsp;{$d.UpdTimeView}&nbsp;{$d.UpdStaffName}</td>
</tr>
</table>
</div>

<h3 class="subtitle02">料金情報</h3>
<div class="margin-box">
<table class="itemtable02">
<tr>
	<th width="120">支払期限</th>
	<th width="120" colspan="3">料金区分</th>
	<th width="120" colspan="2">減免</th>
</tr>
<tr>
	<td align="center">{$d.PayLimitDate|default:'なし'}</td>
	<td colspan="3" align="center">{$d.UseKbnName|default:'----'}
	{if $d.ExtName != ''}&nbsp;(割増&nbsp;&nbsp;{$d.ExtName}&nbsp;{$d.ExtRate}%){/if}
	</td>
	<td colspan="2" align="center">
	    {$d.genTypeName}{if $d.GenName}&nbsp;{$d.GenName}{if $d.GenRate}&nbsp;{$d.GenRate}%{/if}{/if}
	</td>
</tr>
<tr>
	<th width="120">基本施設使用料</th>
	<th width="120">施設使用料</th>
	<th width="120">調整額</th>
	<th width="120">合計額</th>
	<th width="120">納付済額</th>
	<th width="120">収納状態</th>
</tr>
<tr>
	<td align="right">{$d.BaseShisetsuFee}円</td>
	<td align="right">{$d.ShisetsuFee}円</td>
	<td align="right">{$d.OptionFee4}円</td>
	<td align="right">{$d.SumFee}円</td>
	<td align="right">{$d.paynum}円</td>
	<td align="center">{$d.PayKbnName|default:'&nbsp;'}</td>
</tr>
<tr>
	<th>キャンセル料</th>
	<th rowspan="2">調整理由</th>
	<td colspan="4" rowspan="2">{$d.ChouseiRiyuu}</td>
</tr>
<tr>
	<td align="right">{$d.Receipt[7]}円</td>
</tr>
{if $smarty.const._USE_OPTIONFEE_}
<tr>
	<th>{$aOptionFee[1]}</th>
	<th>{$aOptionFee[2]}</th>
	<th>{$aOptionFee[3]}</th>
	<th>{$aOptionFee[5]}</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
</tr>
<tr>
	<td align="right">{$d.OptionFee1}円</td>
	<td align="right">{$d.OptionFee2}円</td>
	<td align="right">{$d.OptionFee3}円</td>
	<td align="right">{$d.OptionFee5}円</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
{/if}
</table>
<br>
<table class="itemtable02">
<tr>
	<th colspan="6">収納内訳</th>
</tr>
<tr>
	{foreach $aKinshu as $value}
	<th width="120">{$value}</th>
	{/foreach}
</tr>
<tr>
	<td align="right">{$d.Receipt[1]}円</td>
	<td align="right">{$d.Receipt[2]}円</td>
	<td align="right">{$d.Receipt[3]}円</td>
	<td align="right">{$d.Receipt[4]}円</td>
	<td align="right">{$d.Receipt[5]}円</td>
	<td align="right">{$d.Receipt[6]}円</td>
</tr>
<tr>
	<th>収納日時</th>
	<td colspan="5">{$d.ReceptDateView}&nbsp;{$d.ReceptTimeView}&nbsp;{$d.ReceptStaffName}&nbsp;{$d.ReceptPlaceName}</td>
</tr>
</table>
{if $d.addition}
<br>
<h4 class="subtitle03">加算料金内訳</h4>
<table class="itemtable02">
<tr>
	<th width="120">名称</th>
	<th width="100">使用時間</th>
	<th width="40">数量</th>
	<th width="80">基本料金</th>
	<th width="80">使用料</th>
	<th width="300">料金区分・割増・減免</th>
</tr>
{foreach $d.addition as $val}
<tr>
	<td>{$val.item_name}</td>
	<td align="center">{$val.usetime}</td>
	<td align="center">{$val.amount}</td>
	<td align="right">{$val.basefee}円</td>
	<td align="right">{$val.billingfee}円</td>
	<td>{$val.FeeInfo}</td>
</tr>
{/foreach}
</table>
{/if}
</div>
<input type="button" value="戻る" onclick="location.href='{$returnUrl}';">

{include file='footer.tpl'}
