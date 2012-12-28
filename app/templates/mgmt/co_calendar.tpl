{include file='header.tpl'}
<!-- templates co_calendar.tpl -->

<body>

<div id="pop_up" class="calendar-area" align="center">
<table>
<tr>
	<td width="70" valign="middle">
	<a href="index.php?op=popup_calendar&{$para.prev}" class="prevMonth">&lt;&nbsp;前月</a>
	</td>
	<th valign="middle">{$para.y}年{$para.m}月</th>
	<td width="70" valign="middle">
	<a href="index.php?op=popup_calendar&{$para.next}" class="nextMonth">次月&nbsp;&gt;</a>
	</td>
</tr>
</table>
<table class="calendar-table">
<tr height="30" align="center">
    <th class="sun">日</th>
    <th>月</th>
    <th>火</th>
    <th>水</th>
    <th>木</th>
    <th>金</th>
    <th class="sat">土</th>
</tr>
{foreach $recs as $key => $value}
  {if $key%7 == 0}<tr align="center">{/if}
    {if $value == 0}
	<td class="gray">
      &nbsp;
    {else}
    <td>
      <a href="#" onClick="clickDay('{$para.name}', '{$para.ver}', '{$para.y}', '{$para.m}', '{$value}');">{$value}</a>
    {/if}
    </td>
  {if $key%7 == 6}</tr>{/if}
{/foreach}
</table>

<br />
<input type="button" name="closeBtn" value="閉じる" onclick="window.close();" />
</div>

</body>
</html>
