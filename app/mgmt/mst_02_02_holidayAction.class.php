<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  閉庁日・祝祭日登録・変更・削除
 *
 *  mst_02_02_holidayAction.class.php
 *  mst_02_02.tpl
 */

class mst_02_02_holidayAction extends adminAction
{
	private $aHoliFlg = array('閉庁日', '祝祭日');

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$stMsg = '';
		$pages = array();
		$rows = array();

		$page_no = get_request_var('page_no');

		if (isset($_POST['saveBtn'])) {
			if (isset($_POST['Code'])) {
				foreach ($_POST['Code'] as $key => $val)
				{
					if (!$this->is_exists($val,
							$_POST['CodeName'][$key],
							$_POST['Flg'][$key]))
					{
						$dataset = array(
								'heichouholidayname' => $_POST['CodeName'][$key],
								'holiflg' => $_POST['Flg'][$key],
								'upddate' => date('Ymd'),
								'updtime' => date('His'),
								'updid' => $_SESSION['userid']
								);
						$a_where = "localgovcode='"._CITY_CODE_."' AND heichouholiday='".$val."'";
						$this->con->autoExecute('m_holiday', $dataset, DB_AUTOQUERY_UPDATE, $a_where);
					}
				}
			}

			if (!empty($_POST['codename'])) {

				$stMsg = $this->check_input_data($_POST);
				if ($stMsg == '') {
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'heichouholiday' => $_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay'],
							'heichouholidayname' => $_POST['codename'],
							'holiflg' => $_POST['flg'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_holiday', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['checkCode'])) {
				foreach ($_POST['checkCode'] as $key => $val)
				{
					$sql = "DELETE FROM m_holiday WHERE localgovcode=? AND heichouholiday=?";
					$a_where = array(_CITY_CODE_, $_POST['Code'][$key]);
					$this->con->query($sql, $a_where);
				}
			}
		} elseif (isset($_POST['copyBtn'])) {
			$sql = "SELECT DISTINCT SUBSTRING(heichouholiday, 1, 4) AS _year FROM m_holiday ORDER BY _year DESC";
			$years = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
			if (!empty($years)) { 
				$pYear = $years[0]['_year'];
				$sql = "SELECT * FROM m_holiday WHERE SUBSTRING(heichouholiday, 1, 4)=? ORDER BY heichouholiday";
				$rows = $this->con->getAll($sql, array($pYear), DB_FETCHMODE_ASSOC);
				++$pYear;
				foreach ($rows as $val)
				{
					$pDay = sprintf('%d%s', $pYear, substr($val['heichouholiday'], 4, 4));
					$dataset = array(
							'localgovcode' => _CITY_CODE_,
							'heichouholiday' => $pDay,
							'heichouholidayname' => $val['heichouholidayname'],
							'holiflg' => $val['holiflg'],
							'upddate' => date('Ymd'),
							'updtime' => date('His'),
							'updid' => $_SESSION['userid']
							);

					$this->con->autoExecute('m_holiday', $dataset, DB_AUTOQUERY_INSERT);
				}
			}
		}

		$sql = "SELECT DISTINCT SUBSTRING(heichouholiday, 1, 4) AS _year FROM m_holiday ORDER BY _year DESC";
		$years = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
		if (!empty($years)) { 
			if ($page_no == '') $page_no = $years[0]['_year'];

			$hit = 0;
			foreach ($years as $val)
			{
				$set = ($val['_year'] == $page_no) ? 1 : 0;
				$pages[] = array('pagenum' => $val['_year'], 'url' => 'index.php?op=mst_02_02_holiday&page_no='.$val['_year'], 'set' => $set);
				if ($val['_year'] == $page_no) $hit = 1;
			}
			if ($hit == 0) {
				$page_no = $years[0]['_year'];
				$pages[0]['set'] = 1;
			}

			$sql = "SELECT * FROM m_holiday WHERE SUBSTRING(heichouholiday, 1, 4)=? ORDER BY heichouholiday";
			$rows = $this->con->getAll($sql, array($page_no), DB_FETCHMODE_ASSOC);
		}

		$this->oSmarty->assign('dateFrom', time());
		$this->oSmarty->assign('aHoliFlg', $this->aHoliFlg);
		$this->oSmarty->assign('pages', $pages);
		$this->oSmarty->assign('page_no', $page_no);
		$this->oSmarty->assign('results', $rows);
		$this->oSmarty->assign('errmsg', $stMsg);
		$this->oSmarty->display('mst_02_02.tpl');
	}

	function is_exists($p_code, $p_name, $p_flg)
	{
		$sql = "SELECT COUNT(*) FROM m_holiday WHERE localgovcode=? AND heichouholiday=? AND heichouholidayname=? AND holiflg=?";
		$a_where = array(_CITY_CODE_, $p_code, $p_name, $p_flg);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}

	function check_input_data(&$dataset)
	{
		$msg = '';

		if (trim($dataset['codename']) == '') {
			$msg.= '閉庁日・祝祭日の名称を入力してください。<br>';
		}
		if (!checkdate($dataset['FromMonth'], $dataset['FromDay'], $dataset['FromYear'])) {
			$msg.= '正しい年月日を入力してください。<br>';
		} elseif ($this->check_duplicate($dataset['FromYear'].$dataset['FromMonth'].$dataset['FromDay'])) {
			$msg.= '閉庁日・祝祭日の日付が重複しています。<br>';
		}
		return $msg;
	}

	function check_duplicate($p_code)
	{
		$sql = "SELECT COUNT(*) FROM m_holiday WHERE localgovcode=? AND heichouholiday=?";
		$a_where = array(_CITY_CODE_, $p_code);
		$res = $this->con->getOne($sql, $a_where);
		if ($res == 0) return false;
		return true;
	}
}
?>
