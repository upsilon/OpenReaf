<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用目的登録・変更・削除
 *
 *  mst_02_03_purposeAction.class.php
 *  mst_02_03.tpl
 */

class mst_02_03_purposeAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$aMokutekiDaiCode = array('01' => 'スポーツ系', '02' => '文化系');
		$aDelFlg = array('表示する', '表示しない');

		$this->set_header_info();

		$stMsg = '';

		if (isset($_POST['saveBtn'])) {
			if (isset($_POST['Code'])) {
				foreach ($_POST['Code'] as $key => $val)
				{
					if (!$this->is_exists($val,
							$_POST['CodeName'][$key],
							$_POST['Order'][$key],
							$_POST['MokutekiDaiCode'][$key],
							$_POST['Flg'][$key]))
					{
						$dataset = array(
								'mokutekiname' => $_POST['CodeName'][$key],
								'mokutekiskbcode' => $_POST['Order'][$key],
								'mokutekidaicode' => $_POST['MokutekiDaiCode'][$key],
								'delflg' => $_POST['Flg'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND mokutekicode='".$val."'";
						$this->con->autoExecute('m_mokuteki', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (!empty($_POST['code'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'mokutekicode' => $_POST['code'],
							'mokutekiname' => $_POST['codename'],
							'mokutekiskbcode' => $_POST['order'],
							'mokutekidaicode' => $_POST['daicode'],
							'delflg' => $_POST['flg'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_mokuteki', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_mokuteki WHERE localgovcode=? AND mokutekicode=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$sql = "SELECT * FROM m_mokuteki ORDER BY mokutekicode";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('aMokutekiDaiCode', $aMokutekiDaiCode);
		$this->oSmarty->assign('aDelFlg', $aDelFlg);
		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_03.tpl');
	}

	function is_exists($p_code, $p_name, $p_order, $p_kind, $p_flg)
	{
		$sql = "SELECT COUNT(*) FROM m_mokuteki WHERE localgovcode=? AND mokutekicode=? AND mokutekiname=? AND mokutekiskbcode=? AND mokutekidaicode=? AND delflg=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name, $p_order, $p_kind, $p_flg);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[0-9]+$/', $dataset['code'])) {
			$msg.= '目的コードは半角数字で入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['code'])) {
			$msg.= '利用目的コードが重複しています。<br>';
		}
		if (trim($dataset['codename']) == '') {
			$msg.= '名称を入力してください。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_mokuteki WHERE localgovcode=? AND mokutekicode=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
