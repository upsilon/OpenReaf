<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設分類情報削除
 *
 *  fcl_02_03_04_delAction.class.php
 *  fcl_02_03.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_03_04_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $delflg_arr, $limitflg_arr, $pulloutmonlimitkbn_arr;

		$message = '';

		$this->set_header_info();

		$oFA = new facility($this->con);

		$ccd = get_request_var('ccd');

		$using = $this->check_using($ccd);
		if ($using) {
			$message = '当該施設分類情報を使用している「施設」があるため、削除できません。';
		}
		$para = $oFA->get_shisetsuclass_data($ccd);

		if (isset($_POST['deleteBtn']) && !$using) {

			$aWhere = array(_CITY_CODE_, $ccd);
			$sql = "DELETE FROM m_shisetsuclass WHERE localgovcode=? AND shisetsuclasscode=?";
			$rs = $this->con->query($sql, $aWhere);
			$rc = $this->oDB->check_error($rs);
			if ($rc < 0) {
				$message = '削除できませんでした。';
			} else {
				$message = '正常に削除しました。';
			}
		}

		$this->oSmarty->assign('ccd', $ccd);
		$this->oSmarty->assign('req', $para);
		$this->oSmarty->assign('delflg_arr', $delflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_02_03_04_del');
		$this->oSmarty->display('fcl_02_03.tpl');
	}

	function check_using($code)
	{
		$sql = 'SELECT COUNT(shisetsucode) FROM m_shisetsu  WHERE localgovcode=? AND shisetsuclasscode=?';
		$aWhere = array(_CITY_CODE_, $code);
		$count = $this->con->getOne($sql,$aWhere);
		if ($count) return true;
		return false;
	}
}
?>
