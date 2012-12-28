<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  室場削除
 *
 *  fcl_03_03_02_delAction.class.php
 *  fcl_03_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_03_03_02_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$para = array();

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$para = $oFA->get_shitsujyo_data($scd, $rcd);

		if ($this->check_using($scd, $rcd)) {
			$success = 1;
			$message = '当該室場に「利用単位」が登録されている、または<br>予約・抽選のトランザクションデータに当該室場のコードが存在するため、削除できません。';
		}
		if (isset($_POST['deleteBtn'])) {
			if ($this->delete_shitsujyo($scd, $rcd)) {
				$message = '削除しました。';
				$success = 1;
			} else {
				$message = '削除できませんでした。';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('back_url', 'fcl_02_02_list');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_03_03_02_del');
		$this->oSmarty->display('fcl_03_03.tpl');
	}

	function delete_shitsujyo($scd, $rcd)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';

		$sql = 'DELETE FROM t_monthpulloutdate'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_stjpurpose'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_stjfee'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_stjuserrestime'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_stjtimetable'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_unavailableday'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_closedday'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_staffshisetsu'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_yoyakuscheduleptn'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_shitsujyou'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$aWhere = array(_CITY_CODE_, $scd, $rcd, $rcd);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND (shitsujyocode=? OR fuzokucode=?)';
		$sql = 'DELETE FROM m_fuzokushitsujyou'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		return true;
	}

	function check_using($scd, $rcd)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';

		$sql = 'SELECT COUNT(mencode) FROM m_men'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(yoyakunum) FROM t_yoyaku_fee_option'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(yoyakunum) FROM t_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(yoyakunum) FROM h_yoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(pulloutyoyakunum) FROM t_pulloutyoyaku'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(pulloutyoyakunum) FROM h_pullout'.$where;
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		$sql = 'SELECT COUNT(fuzokucode) FROM m_fuzokushitsujyou WHERE localgovcode=? AND shisetsucode=? AND fuzokucode=?';
		$count = $this->con->getOne($sql, $aWhere);
		if ($count) return true;

		return false;
	}
}
?>
