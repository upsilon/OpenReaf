{include file='header.tpl'}
<!-- templates mst_01_02_01.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;<strong>システムデータ登録</strong>
</div>

<h2 class="subtitle01">システムデータ登録</h2>

{if $rec}
<div class="margin-box">
<input type="button" name="addBtn" value="変更画面へ" class="btn-01" onClick="window.location.href='index.php?op=mst_01_02_system&mode=edit';">

<table class="itemtable03" width="720">
  <tr>
	<th colspan="2" align="left">自治体コード</th>
	<td>&nbsp;001&nbsp;</td>
  </tr>
  <tr>
	<th colspan="2" align="left">自治体名称</th>
	<td>{$rec.localgovname}</td>
  </tr>
  <tr>
	<th colspan="2" align="left">{$smarty.const._MAYOR_}名</th>
	<td>{$rec.mayorname}</td>
  </tr>
<tr>
	<th width="120" rowspan="3">時間帯</th>
	<th width="180">午前</th>
	<td>{$rec.AMFromView}&nbsp;～&nbsp;{$rec.AMToView}</td>
</tr>
<tr>
	<th>午後</th>
	<td>{$rec.PMFromView}&nbsp;～&nbsp;{$rec.PMToView}</td>
</tr>
<tr>
	<th>夜間</th>
	<td>{$rec.NTFromView}&nbsp;～&nbsp;{$rec.NTToView}</td>
</tr>
<tr>
	<th rowspan=2">桁数</th>
	<th>利用者ID</th>
	<td>最小桁数&nbsp;{$rec.useridlngmin|default:'&nbsp;'}&nbsp;最大桁数&nbsp;{$rec.useridlng|default:'&nbsp;'}&nbsp;入力サイズ&nbsp;{$rec.userid_size}</td>
</tr>
<tr>
	<th>パスワード</th>
	<td>最小桁数&nbsp;{$rec.pwdlngmin|default:'&nbsp;'}&nbsp;最大桁数&nbsp;{$rec.pwdlng|default:'&nbsp;'}&nbsp;入力サイズ&nbsp;{$rec.pwd_size}</td>
</tr>
<tr>
	<th rowspan=2">文字種</th>
	<th>利用者ID</th>
	<td>{$rec.UserIDType}</td>
</tr>
<tr>
	<th>パスワード</th>
	<td>{$rec.PwdType}</td>
</tr>
<tr>
	<th colspan="2" align="left">利用者パスワードの自動発番</th>
	<td>{$rec.UserPassAutoFlg}</td>
</tr>
<tr>
	<th colspan="2" align="left">利用者登録期限の使用</th>
	<td>{$rec.UserLimitDispFlg}</td>
</tr>
<tr>
	<th colspan="2" align="left">施設分類選択画面の使用</th>
	<td>{$rec.ShisetsuClassScreenFlg}</td>
</tr>
<tr>
	<th colspan="2" align="left">施設利用権限の使用</th>
	<td>{$rec.ShisetsuRestrictionFlg}</td>
</tr>
<tr>
	<th colspan="2" align="left">ログイン時間の制限</th>
{if $rec.loginkbn == 1}
	<td nowrap>制限する&nbsp;{$rec.logintimefrom}〜{$rec.logintimeto}</td>
{else}
	<td>制限しない</td>
{/if}
</tr>
<tr>
	<th colspan="2" align="left">パスワードエラー時のロックアウト</th>
	<td>{$rec.LockOutFlg}
{if $rec.lockoutflg != 0}
	&nbsp;制限回数&nbsp;{$rec.lockout_count}
{/if}
{if $rec.lockoutflg == 2}
	&nbsp;自動解除時間&nbsp;{$rec.reentry_interval}&nbsp;分
{/if}
	</td>
</tr>
<tr>
	<th colspan="2" align="left">サイト閉鎖</th>
	<td>{$rec.SiteCloseFlg}
{if $rec.sitecloseflg == 2}
	&nbsp;閉鎖期間&nbsp;{$rec.siteclosefrom|date_format:"%Y/%m/%d %H:%M"}～{$rec.sitecloseto|date_format:"%Y/%m/%d %H:%M"}
{/if}
	</td>
</tr>
<tr>
	<th rowspan="2">URL</th>
	<th>自治体のホームページ</th>
	<td>{$rec.homepageurl}</td>
</tr>
<tr>
	<th>システムトップURL</th>
	<td>{$rec.topmenuurl}</td>
</tr>
<tr>
	<th rowspan="3">メールアドレス</th>
	<th>送信元メールアドレス</th>
	<td>{$rec.mailfromaddr}</td>
</tr>
<tr>
	<th>送信元名</th>
	<td>{$rec.mailfromname}</td>
</tr>
<tr>
	<th>BCCメールアドレス</th>
	<td>{$rec.mailbccaddr}</td>
</tr>
<tr>
	<th rowspan="4">メールサーバ</th>
	<th>サーバ</th>
	<td>{$rec.mailhost}</td>
</tr>
<tr>
	<th>ポート番号</th>
	<td>{$rec.mailhostport}</td>
</tr>
<tr>
	<th>アクセスID</th>
	<td>{$rec.mailhostuserid}</td>
</tr>
<tr>
	<th>アクセスパスワード</th>
	<td>{$rec.mailhostuserpass}</td>
</tr>
</table>
</div>
<br>
{/if}

{include file='footer.tpl'}
