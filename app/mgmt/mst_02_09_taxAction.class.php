<?php
/*
 *  Copyright 2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  消費税率登録・変更・削除
 *
 *  mst_02_09_taxAction.class.php
 *  mst_02_09.tpl
 */

class mst_02_09_taxAction extends adminAction
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
			if (isset($_POST['Rate'])) {
				foreach ($_POST['Rate'] as $key => $val)
				{
					if ($this->is_exists($val,
							$_POST['AppDateFrom'][$key],
							$_POST['LimitDay'][$key])) {
						continue;
					}
					if ($this->check_duplicate($_POST['LimitDay'][$key], $_POST['AppDateFrom'][$key])) {
						$stMsg.= '適用終了日が他の適用期間内に入っています。<br>';
					} elseif (intval($_POST['LimitDay'][$key]) <= intval($_POST['AppDateFrom'][$key])) {
						$stMsg.= '適用終了日が適用開始日と同じもしくは超えています。<br>';
					} else {
						$dataset = array(
								'limitday' => $_POST['LimitDay'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND taxrate='".$val."' AND appdatefrom='".$_POST['AppDateFrom'][$key]."'";
						$this->con->autoExecute('m_tax', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (trim($_POST['rate']) != ''
				&& !empty($_POST['appdatefrom'])) {

				$msg = $this->check_input_data($_POST);
				if ($msg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'taxrate' => $_POST['rate'],
							'appdatefrom' => $_POST['appdatefrom'],
							'limitday' => $_POST['limitday'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_tax', $dataset, DB_AUTOQUERY_INSERT);
				} else {
					$stMsg .= $msg;
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_tax WHERE localgovcode=? AND taxrate=? AND appdatefrom=?";
					$a_where = array(_CITY_CODE_, $_POST['Rate'][$key], $_POST['AppDateFrom'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		}

		$sql = "SELECT * FROM m_tax ORDER BY appdatefrom";
		$rows = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_09.tpl');
	}

	function is_exists($p_rate, $p_appdatefrom, $p_limitday)
	{
		$sql = "SELECT COUNT(*) FROM m_tax WHERE localgovcode=? AND taxrate=? AND appdatefrom=? AND limitday=?";
		$a_where = array(_CITY_CODE_, $p_rate, $p_appdatefrom, $p_limitday);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';
		$check_from = true;
		$check_to = true;

		if (!preg_match('/^[0-9]+$/', $dataset['rate'])) {
			$msg.= '消費税率は半角数字で入力してください。<br>';
		}
		if (trim($dataset['appdatefrom']) == '') {
			$msg.= '適用開始日を入力してください。<br>';
			$check_from = false;
		} elseif (!preg_match('/^[0-9]+$/', $dataset['appdatefrom'])) {
			$msg.= '適用開始日は半角数字で入力してください。<br>';
			$check_from = false;
		} elseif ($this->check_duplicate($dataset['appdatefrom'])) {
			$msg.= '適用開始日が他の適用期間内に入っています。<br>';
			$check_from = false;
		}
		if (trim($dataset['limitday']) != '') {
			if (!preg_match('/^[0-9]+$/', $dataset['limitday'])) {
				$msg.= '適用終了日は半角数字で入力してください。<br>';
				$check_to = false;
			} elseif ($this->check_duplicate($dataset['limitday'])) {
				$msg.= '適用終了日が他の適用期間内に入っています。<br>';
				$check_to = false;
			}
			if ($check_from && $check_to) {
				if (intval($dataset['limitday']) <= intval($dataset['appdatefrom'])) {
					$msg.= '適用終了日が適用開始日と同じもしくは超えています。<br>';
				}
			}
		}
		return $msg;
	}

	function check_duplicate($p_date, $p_from='')
	{
		$a_where = array(_CITY_CODE_, $p_date, $p_date);

		$sql = "SELECT COUNT(*) FROM m_tax";
		$sql.= " WHERE localgovcode=? AND appdatefrom<=?";
		$sql.= " AND (limitday>=? OR limitday='')";
		if ($p_from != '') {
			$sql.= " AND appdatefrom<>?";
			array_push($a_where, $p_from);
		}
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
