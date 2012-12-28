<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  面削除
 *
 *  fcl_05_07_02_delAction.class.php
 *  fcl_05_07.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_05_07_02_delAction extends adminAction
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
		$mcd = $_REQUEST['mcd'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);

		$para = $oFA->get_men_data($scd, $rcd, $mcd);

		if ($this->check_using($scd, $rcd, $mcd)) {
			$success = 1;
			$message = '当該利用単位に「組合せ」が登録されている、または<br>予約・抽選のトランザクションデータに当該利用単位のコードが存在するため、削除できません。';
		}
		if (isset($_POST['deleteBtn'])) {
			if ($this->delete_men($scd, $rcd, $mcd)) {
				$message = '削除しました。';
				$success = 1;
			} else {
				$message = '削除できませんでした。';
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('back_url', 'fcl_04_08_summary');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_05_07_02_del');
		$this->oSmarty->display('fcl_05_07.tpl');
	}

	function delete_men($scd, $rcd, $mcd)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $mcd);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND mencode=?';

		$sql = 'DELETE FROM m_men'.$where;
		$rs = $this->con->query($sql, $aWhere);
		$rc = $this->oDB->check_error($rs);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function check_using($scd, $rcd, $mcd)
	{
		$aWhere = array(_CITY_CODE_, $scd, $rcd, $mcd);
		$where = ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND mencode=?';

		$sql = 'SELECT COUNT(combino) FROM m_mencombination'.$where;
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

		return false;
	}
}
?>
