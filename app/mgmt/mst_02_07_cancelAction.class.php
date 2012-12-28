<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  変更・取消事由登録・変更・削除
 *
 *  mst_02_07_cancelAction.class.php
 *  mst_02_07.tpl
 */

class mst_02_07_cancelAction extends adminAction
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
								'canceljiyuname' => $_POST['CodeName'][$key],
								'rate' => $_POST['Rate'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND cancelcode='".$val."'";
						$this->con->autoExecute('m_canceljiyucode', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (!empty($_POST['code'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'cancelcode' => $_POST['code'],
							'cancelkbn' => '1',
							'canceljiyuname' => $_POST['codename'],
							'rate' => $_POST['rate'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_canceljiyucode', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_canceljiyucode WHERE localgovcode=? AND cancelcode=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$sql = "SELECT * FROM m_canceljiyucode ORDER BY cancelcode";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_07.tpl');
	}

	function is_exists($p_code, $p_name, $p_rate)
	{
		$sql = "SELECT COUNT(*) FROM m_canceljiyucode WHERE localgovcode=? AND cancelcode=? AND canceljiyuname=? AND rate=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name, $p_rate);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[0-9]+$/', $dataset['code'])) {
			$msg = '事由コードは半角数字で入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['code'])) {
			$msg = '事由コードが重複しています。<br>';
		}
		if (trim($dataset['codename']) == '') {
			$msg = '事由を入力してください。<br>';
		}
		if (!preg_match('/^[0-9]+$/', $dataset['rate'])) {
			$msg = '還付率は半角数字で入力してください。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_canceljiyucode WHERE localgovcode=? AND cancelcode=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
