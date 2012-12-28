<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  システムパラメータ
 *
 *  mst_01_03_numberAction.class.php
 *  mst_01_03_01.tpl
 *  mst_01_03_02.tpl
 */

class mst_01_03_numberAction extends adminAction
{
	// 発番コード => 名称 変換
	private $aSaibanCode = array("Application"=>"申請書",
					"Permit"=>"許可書",
					"Reciept"=>"領収書",
					"BihinYoyakuNum"=>"備品貸出番号",
					"UserID"=>"利用者ID",
					"YoyakuNum"=>"予約番号");

	// 利用フラグ => 名称 変換
	private $aSaibanFlg = array("0"=>"利用しない", "1"=>"利用する");

	// 発番コード => 最大文字数 変換
	private $aKetaLimit = array("YoyakuNum" => 10,
				    "UserID" => 16,
				    "BihinYoyakuNum" => 10);

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$template = '';
		$success = 0;
		$rec = array();

		$this->set_header_info();

		if (isset($_REQUEST['SaibanCode'])) {
			$template = 'mst_01_03_02.tpl';
			if (isset($_POST['updateBtn'])) {
				$message = $this->check_input_data($_POST);
				if ($message == '') {
					$dataset = $this->oDB->make_base_dataset($_POST, 'm_saiban');
					unset($dataset['saibancode']);
					$dataset['saibannolng'] = intval($dataset['saibannolng']);
					$dataset['upddate'] = date('Ymd');
					$dataset['updtime'] = date('His');
					$dataset['updid'] = $_SESSION['userid'];
					$where = "localgovcode='"._CITY_CODE_."' AND saibancode='".$_POST['SaibanCode']."'";
					$rs = $this->con->autoExecute('m_saiban', $dataset, DB_AUTOQUERY_UPDATE, $where);
					$rc = $this->oDB->check_error($rs);
					if ($rc < 0) {
						$message = '発番情報を更新できませんでした。';
						$success = -1;
					} else {
						$message = '発番情報を更新しました。';
						$success = 1;
					}
				} else {
					$success = -1;
				}
			}

			if ($success < 0) {
				$rec = $_POST;
			} else {
				$sql = "SELECT * FROM m_saiban WHERE localgovcode=? AND saibancode=?";
				$aWhere = array(_CITY_CODE_, $_REQUEST['SaibanCode']);
				$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
			}
			$rec['saibanName'] = $this->aSaibanCode[$rec['saibancode']];
			$rec['enabledSaibanFlg'] = ($rec['saibancode'] != 'YoyakuNum' && $rec['saibancode'] != 'BihinYoyakuNum');
			$this->oSmarty->assign('saibanList', $this->aSaibanFlg);
		} else {
			$template = 'mst_01_03_01.tpl';
			$sql = "SELECT * FROM m_saiban WHERE localgovcode=? ORDER BY displayorder";
			$aWhere = array(_CITY_CODE_);
			$res = $this->con->getAll($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);
			$i = 0;
			foreach ($res as $val)
			{
				if (!array_key_exists($val['saibancode'], $this->aSaibanCode)) {
					continue;
				}
				$rec[$i] = $val;
				$rec[$i]['saibanName'] = $this->aSaibanCode[$val['saibancode']];
				$rec[$i]['showSaibanFlg'] = $this->aSaibanFlg[$val['saibanflg']];
				$rec[$i]['enabledSaibanFlg'] = ($val['saibancode'] != 'YoyakuNum' && $val['saibancode'] != 'BihinYoyakuNum');
				if ($val['saibannolng'] > 0) {
					$outStyle = "%0{$val['saibannolng']}d";
					$rec[$i]['outputValue'] = $val['prefix'].sprintf($outStyle, $val['saibanno']).$val['suffix'];
				} else {
					$rec[$i]['outputValue'] = $val['prefix'].$val['saibanno'].$val['suffix'];
				}
				++$i;
			}
			unset($res);
		}

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('op', 'mst_01_03_number');
		$this->oSmarty->display($template);
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if ($req['saibanno'] == '') {
			$msg .= '発番番号を入力してください。<br>';
		} elseif (!preg_match('/^[0-9]+$/', $req['saibanno'])) {
			$msg .= '発番番号は半角数字で入力してください。<br>';
		}
		if (!preg_match('/^[0-9]+$/', $req['saibannolng'])) {
			$msg .= '発番ケタ数は半角数字で入力して下さい。<br>';
		}
		return $msg;
	}
}
?>
