<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  料金区分登録・変更・削除
 *
 *  mst_02_06_feekbnAction.class.php
 *  mst_02_06.tpl
 */

class mst_02_06_feekbnAction extends adminAction
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
							$_POST['CodeName'][$key]))
					{
						$dataset = array(
								'feekbnname' => $_POST['CodeName'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND feekbn='".$val."'";
						$this->con->autoExecute('m_feekbn', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (!empty($_POST['code'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'feekbn' => $_POST['code'],
							'feekbnname' => $_POST['codename'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_feekbn', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_feekbn WHERE localgovcode=? AND feekbn=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$sql = "SELECT * FROM m_feekbn ORDER BY feekbn";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_06.tpl');
	}

	function is_exists($p_code, $p_name)
	{
		$sql = "SELECT COUNT(*) FROM m_feekbn WHERE localgovcode=? AND feekbn=? AND feekbnname=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[0-9]+$/', $dataset['code'])) {
			$msg.= '料金区分は半角数字で入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['code'])) {
			$msg.= '料金区分が重複しています。<br>';
		}
		if (trim($dataset['codename']) == '') {
			$msg.= '名称を入力してください。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_feekbn WHERE localgovcode=? AND feekbn=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
