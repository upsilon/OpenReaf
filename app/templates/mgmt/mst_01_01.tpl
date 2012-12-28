{include file='header.tpl'}
<!-- templates mst_01_01.tpl -->

<body>

{include file='page-header.tpl'}
{include file='menu.tpl'}

<div id=searchbox>
マスタ管理&nbsp;&gt;&nbsp;<strong>マスタデータ登録</strong>
</div>

<h2 class="subtitle01">マスタデータ登録</h2>

<div id="room-menu" align="center">
<div class="itemtop-area">
	<h4>以下のメニューから選択してください。</h4>
</div>
<table width="400">
<tr>
	<td>
	<input name="btm_01" type="button" class="btn" onclick="location.href='index.php?op=mst_02_01_system';" value="システムコード">
	</td>
	<td>
	<input name="btm_02" type="button" class="btn" onclick="location.href='index.php?op=mst_02_02_holiday';" value="閉庁日・祝祭日">
	</td>
</tr>
<tr>
	<td>
	<input name="btm_06" type="button" class="btn" onclick="location.href='index.php?op=mst_02_06_feekbn';" value="料金区分">
	</td>
	<td>
	<input name="btm_03" type="button" class="btn" onclick="location.href='index.php?op=mst_02_03_purpose';" value="利用目的">
	</td>
</tr>
<tr>
	<td>
	<input name="btm_04" type="button" class="btn" onclick="location.href='index.php?op=mst_02_04_exemption';" value="固定減免率">
	</td>
	<td>
	<input name="btm_05" type="button" class="btn" onclick="location.href='index.php?op=mst_02_05_exemption';" value="申請減免率">
	</td>
</tr>
<tr>
	<td>
	<input name="btm_07" type="button" class="btn" onclick="location.href='index.php?op=mst_02_07_cancel';" value="変更・取消事由">
	</td>
	<td>
	<input name="btm_08" type="button" class="btn" onclick="location.href='index.php?op=mst_02_08_busho';" value="部署コード">
	</td>
</tr>
<tr>
	<td>
	<input name="btm_09" type="button" class="btn" onclick="location.href='index.php?op=mst_02_09_tax';" value="消費税率">
	</td>
	<td>
	<input name="btm_10" type="button" class="btn" onclick="location.href='index.php?op=mst_02_10_extra';" value="割増率">
	</td>
</tr>
</table>
</div>

{include file='footer.tpl'}
