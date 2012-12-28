<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  システムコード登録・変更・削除
 *
 *  mst_02_01_systemAction.class.php
 *  mst_02_01.tpl
 */

class mst_02_01_systemAction extends adminAction
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
			foreach ($_POST['CodeID'] as $key => $val)
			{
				if (!$this->is_exists($val,
						$_POST['Code'][$key],
						$_POST['CodeName'][$key]))
				{
					$dataset = array(
							'codename' => $_POST['CodeName'][$key],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);
					$a_where = "localgovcode='"._CITY_CODE_."' AND codeid='".$val."' AND code='".$_POST['Code'][$key]."'";
					$this->con->autoExecute('m_codename', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
				}
			}

			if (!empty($_POST['codeid'])
				&& !empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'codeid' => $_POST['codeid'],
							'code' => $_POST['code'],
							'codename' => $_POST['codename'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_codename', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_codename WHERE localgovcode=? AND codeid=? AND code=?";
					$a_where = array(_CITY_CODE_, $_POST['CodeID'][$key], $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$code_id = isset($_POST['code_id']) ? $_POST['code_id'] : '';

		$sql = 'SELECT * FROM m_codename';
		if ($code_id != '') {
			$sql.= " WHERE codeid='$code_id'";
		}
		$sql.= ' ORDER BY codeid, code';
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$sql = 'SELECT DISTINCT codeid FROM m_codename ORDER BY codeid';
		$recs = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
		$t_options = array();
		foreach ($recs as $val)
		{
			$t_options[$val['codeid']] = $val['codeid'];
		}

		$this->oSmarty->assign('type_options', $t_options);
		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('code_id', $code_id);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_01.tpl');
	}

	function is_exists($p_type, $p_value, $p_label)
	{
		$sql = "SELECT COUNT(*) FROM m_codename WHERE localgovcode=? AND codeid=? AND code=? AND codename=?";
		$a_where = array(_CITY_CODE_, $p_type, $p_value, $p_label);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (!preg_match('/^[_a-zA-Z0-9]+$/', $dataset['codeid'])) {
			$msg.= 'コードIDは半角英数字で入力してください。<br>';
		}
		if (!preg_match('/^[0-9]+$/', $dataset['code'])) {
			$msg.= 'コードは半角数字で入力してください。<br>';
		}
		if ($this->check_duplicate($dataset['codeid'], $dataset['code'])) {
			$msg.= 'コードIDとコードの組合せが重複しています。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_type, $p_value)
	{
		$sql = "SELECT COUNT(*) FROM m_codename WHERE LocalGovCode=? AND CodeID=? AND Code=?";
		$a_where = array(_CITY_CODE_, $p_type, $p_value);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
