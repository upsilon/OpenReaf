<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面組合せ情報削除
 *
 *  fcl_05_09_02_delAction.class.php
 *  fcl_05_09.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_09_02_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $useflg_arr, $openflg_arr, $openkbn_arr;

		$message = '';
		$success = 0;

		$this->set_header_info();

		$scd = $_REQUEST['scd'];
		$rcd = $_REQUEST['rcd'];
		$cno = $_REQUEST['cno'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$res = $oFA->get_mencombination_data($scd, $rcd, $cno);
		$para = $oFA->make_mencombination_info($res);
		$para['openkbnval'] = explode(',', $para['openkbn']);
		unset($res);

		if ($this->check_using($scd, $rcd, $cno)) {
			$success = 1;
			$message = '当該利用単位組合せが予約・抽選のトランザクションデータに存在するため、削除できません。';
		}
		if (isset($_POST['deleteBtn'])) {

			$rc = $this->delete_mencombination($scd, $rcd, $cno);
			if ($rc) {
				$message = '組合せ情報を削除しました。';
				$success = 1;
			} else {
				$message.= '組合せ情報の削除ができませんでした。<br>';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('openkbn_arr', $openkbn_arr);
		$this->oSmarty->assign('month_arr', range(0, 11));
		$this->oSmarty->assign('back_url', 'fcl_04_09_summary');
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_05_09_02_del');
		$this->oSmarty->display('fcl_05_09.tpl');
	}

	function delete_mencombination($scd, $rcd, $cno)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';

		$sql = 'DELETE FROM m_stjfee'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_stjpurpose'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_fuzokushitsujyou'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		$sql = 'DELETE FROM m_mencombination'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) return false;

		return true;
	}

	function check_using($scd, $rcd, $cno)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $cno);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';

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

		return false;
	}
}
?>
