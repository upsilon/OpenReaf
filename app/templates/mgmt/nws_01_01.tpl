{include file='header.tpl'}
<!-- templates nws_01_01.tpl -->

{if $success == 1}
<body onload="alert('{$message}')">
{else}
<body>
{/if}
{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id="searchbox">
お知らせ &gt; <strong><u>お知らせの編集</u></strong>
</div>

<h2 class="subtitle01">お知らせの編集</h2>

<form name="form1" method="post" action="index.php">
<input type="hidden" name="op" value="{$op}">
{* ページングＩＤ *}
<input type="hidden" name="PageNo" value="{$PageNo}">
<input type="hidden" name="PrimaryKey" value="{$PrimaryKey}">

<div class="margin-box">
{if $message && $success < 0}<div id="errorbox">{$message}</div>{/if}
<table class="itemtable03">
<tr height="29">
	<th width="106" align="center">施設</th>
	<td>
	<select name="shisetsucode">
	<option value="000">指定しない</option>
	{html_options options=$aShisetsu selected=$para.shisetsucode}
	</select>
	</td>
</tr>
<tr height="29">
	<th align="center">掲載期間</th>
	<td>
	{html_select_date prefix='From' start_year='-5' end_year='+5' display_days=false display_months=false time=$para.dateFrom}年
	{html_select_date prefix='From' display_days=false display_years=false month_format='%m' time=$para.dateFrom}月
	{html_select_date prefix='From' display_months=false display_years=false day_value_format='%02d' time=$para.dateFrom}日
	<input name="calendarFrom" type="button" value="カレンダー" onclick="openCalendar('From', 'form1');" class="f-s_down"/>
	〜
	{html_select_date prefix='To' start_year='-5' end_year='+5' display_days=false display_months=false time=$para.dateTo}年
	{html_select_date prefix='To' display_days=false display_years=false month_format='%m' time=$para.dateTo}月
	{html_select_date prefix='To' display_months=false display_years=false day_value_format='%02d' time=$para.dateTo}日
	<input name="calendarTo" type="button" value="カレンダー" onclick="openCalendar('To','form1');" class="f-s_down"/>
</tr>
<tr height="22">
	<th align="center">見出し</th>
	<td>
	<input type="text" name="title" value="{$para.title}" size="60" maxlength="100" style="ime-mode:active;" {$err.Title} />
	</td>
</tr>
<tr height="22">
	<th align="center">内容</th>
	<td>
	<textarea name="memo" cols="60" rows="8" style="ime-mode:active;" style="ime-mode:active;" {$err.Memo}>{$para.memo}</textarea>
	</td>
</tr>
<tr height="22">
	<th align="center">関連情報URL</th>
	<td>
	<input type="text" name="url" value="{$para.url}" size="60" maxlength="160" style="ime-mode:disabled;" />
	</td>
</tr>
<tr height="22">
	<th align="center">掲載先</th>
	<td>
	{html_radios name="disptermflg" options=$aDispTerm selected=$para.disptermflg|default:0}
	</td>
</tr>
<tr height="29">
	<th align="center">表示優先度</th>
	<td>
	<select name="prioritykbn">
	{html_options options=$aPriority selected=$para.prioritykbn}
	</select>
	</td>
</tr>
</table>

<!--/margin-box-->
</div>

<div class="bt-area" align="center">
<input type="submit" name="insertBtn" value="登録" onclick="return confirm('登録しますか？');">
<input type="reset" name="pReset" value="リセット">
</div>

<hr align="center" width="99%">

{*-----以下、登録済みのお知らせ一覧-----------*}
<div class="itemtop-area">
<h3>登録済みのお知らせ</h3>

{*-----データがある場合だけテーブルを表示する。----*}
{if $res}
	<input type="button" name="selectAll" value="選択反転" onclick="alternateCheckBox(this.form, 'pAryKey');">
	&nbsp;選択したお知らせの&nbsp;
	<input type="submit" name="deleteBtn" value="削除" onclick="return confirm('削除しますか？');">
	<input type="submit" name="editBtn" value="編集">
{/if}

{* ページングリンク *}
{strip}
{if $PageNoPrev >= 0}
	<a href="index.php?op=nws_01_01_top&ShisetsuCode={$ShisetsuCode}&PageNo={$PageNoPrev}">&lt;&nbsp;前の{$PageRowLimit}件</a>
{else}
	<span class="f-brack">&lt;&nbsp;前の{$PageRowLimit}件</span>
{/if}
&nbsp;&nbsp;
{if $PageNoNext >=0}
	<a href="index.php?op=nws_01_01_top&ShisesuCode={$ShisetsuCode}&PageNo={$PageNoNext}">次の{$PageRowLimit}件&nbsp;&gt;</a>
{else}
	<span class="f-brack">次の{$PageRowLimit}件&nbsp;&gt</span>
{/if}
{/strip}
</div>
{*-------------ここから、一覧表----------------------*}
{if $res}
<table width="98%" class="itemtable02">
<tr>
	<th rowspan="3">&nbsp;</th>
	<th>登録日時</th>
	<th width="66%">見出し</th>
	<th width="8%" rowspan="3">登録者</th>
	<th width="8%" rowspan="3">優先度<br><br>掲載先</th>
</tr>
<tr>
	<th rowspan="2" align="center">掲載期間</th>
	<th align="center">内容</h>
</tr>
<tr>
	<th align="center">関連URL</th>
</tr>
{foreach $res as $row}
<tr>
	<td rowspan="3" align="center">
	<input type="checkbox" name="pAryKey[]" value="{$row.shisetsucode}@{$row.tourokudate}@{$row.seqno}">
	</td>
	<td align="center">{$row.TourokuDateView}<br>{$row.TourokuTimeView}</td>
	<td>{$row.title}</td>
	{*登録者*}
	<td rowspan="3" align="center" nowrap>{$row.staffname}</td>
	{*優先度・掲載先*}
	<td rowspan="3" align="center">{$row.PriorityName}<br><br>{$row.DispTermName}</td>
</tr>
<tr>
	{*掲載日*}
	<td rowspan="2" align="center">{$row.UpKikanFromView}<br>〜<br>{$row.UpKikanToView}</td>
	{*内容*}
	<td>{$row.memo|nl2br}</td>
</tr>
<tr>
	{*関連URL*}
	<td>{$row.url|default:'&nbsp;'}</td>
</tr>
{/foreach}
</table>
{/if}
{*------データがある場合ここまで------*}
{if !$res &&  $PageNoPrev < 0 && $PageNoNext <0}
	<font color="#FF0000"><b>登録されているデータがありません。</b></font>
{/if}
</form>

{include file='footer.tpl'}
