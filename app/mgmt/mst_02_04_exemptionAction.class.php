<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  固定減免率登録・変更・削除
 *
 *  mst_02_04_exemptionAction.class.php
 *  mst_02_04.tpl
 */

class mst_02_04_exemptionAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$stMsg = '';

		if (isset($_POST['saveBtn'])) {
			if (isset($_POST['Code'])) {
				foreach ($_POST['Code'] as $key => $val)
				{
					if (!$this->is_exists($val,
							$_POST['CodeName'][$key],
							$_POST['Rate'][$key]))
					{
						$dataset = array(
								'koteigenname' => $_POST['CodeName'][$key],
								'rate' => $_POST['Rate'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND koteigencode='".$val."'";
						$this->con->autoExecute('m_genmen', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (!empty($_POST['code'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'koteigencode' => $_POST['code'],
							'koteigenname' => $_POST['codename'],
							'rate' => $_POST['rate'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_genmen', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_genmen WHERE localgovcode=? AND koteigencode=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$sql = "SELECT * FROM m_genmen ORDER BY koteigencode";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_04.tpl');
	}

	function is_exists($p_code, $p_name, $p_rate)
	{
		$sql = "SELECT COUNT(*) FROM m_genmen WHERE localgovcode=? AND koteigencode=? AND koteigenname=? AND rate=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name, $p_rate);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[0-9]+$/', $dataset['code'])) {
			$msg.= '減免コードは半角数字で入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['code'])) {
			$msg.= '減免コードが重複しています。<br>';
		}
		if (trim($dataset['codename']) == '') {
			$msg.= '名称を入力してください。<br>';
		}
		if (!preg_match('/^[0-9]+$/', $dataset['rate'])) {
			$msg.= '減免率は半角数字で入力してください。<br>';
		} elseif (intval($dataset['rate']) > 100) {
			$msg.= '減免率は 100 以下の値を入力してください。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_genmen WHERE localgovcode=? AND koteigencode=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
