<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  施設情報削除
 *
 *  fcl_02_01_05_delAction.class.php
 *  fcl_02_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/include/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_02_01_05_delAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $openflg_arr, $dispflg_arr, $useflg_arr, $fractionflg_arr,
			$limitflg_arr, $shisetsukbn_arr, $pulloutmonlimitkbn_arr;

		$message = '';

		$this->set_header_info();

		$oFA = new facility($this->con);

		$scd = '';
		if (isset($_GET['scd'])) {
			$scd = $_GET['scd'];
		} elseif (isset($_POST['scd'])) {
			$scd = $_POST['scd'];
		}

		$using = $this->check_using($scd);
		if ($using) {
			$message = '当該施設に「室場」が登録されているため、削除できません。';
		}
		$para = $oFA->get_shisetsu_data($scd);

		if (isset($_POST['deleteBtn']) && !$using) {

			$aWhere = array(_CITY_CODE_, $scd);
			$sql1 = "DELETE FROM m_staffshisetsu WHERE localgovcode=? AND shisetsucode=?";
			$sql2 = "DELETE FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=?";
			$rs = $this->con->query($sql1, $aWhere);
			$rc = $this->oDB->check_error($rs);
			if ($rc < 0) {
				$message = '削除できませんでした。';
			} else {
				$rs = $this->con->query($sql2, $aWhere);
				$rc = $this->oDB->check_error($rs);
				if ($rc < 0) {
					$message = '削除できませんでした。';
				} else {
					$message = '正常に削除しました。';
				}
			}
		}

		$aBusho = $this->oPrivilege->get_busho_options();
		$aShisetsuClass = $oFA->get_shisetsuclass_options();

		$this->oSmarty->assign('scd', $scd);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('aBusho', $aBusho);
		$this->oSmarty->assign('aShisetsuClass', $aShisetsuClass);
		$this->oSmarty->assign('openflg_arr', $openflg_arr);
		$this->oSmarty->assign('dispflg_arr', $dispflg_arr);
		$this->oSmarty->assign('useflg_arr', $useflg_arr);
		$this->oSmarty->assign('fractionflg_arr', $fractionflg_arr);
		$this->oSmarty->assign('limitflg_arr', $limitflg_arr);
		$this->oSmarty->assign('shisetsukbn_arr', $shisetsukbn_arr);
		$this->oSmarty->assign('pulloutmonlimitkbn_arr', $pulloutmonlimitkbn_arr);
		$this->oSmarty->assign('input_control', 'readonly');
		$this->oSmarty->assign('button_control', 'disabled');
		$this->oSmarty->assign('err', array());
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', 'del');
		$this->oSmarty->assign('op', 'fcl_02_01_05_del');
		$this->oSmarty->display('fcl_02_01.tpl');
	}

	function check_using($code)
	{
		$sql = 'SELECT COUNT(shitsujyocode) FROM m_shitsujyou  WHERE localgovcode=? AND shisetsucode=?';
		$aWhere = array(_CITY_CODE_, $code);
		$count = $this->con->getOne($sql,$aWhere);
		if ($count) return true;
		return false;
	}
}
?>
