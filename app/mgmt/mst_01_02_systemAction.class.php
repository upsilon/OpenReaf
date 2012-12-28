<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  システムパラメータ
 *
 *  mst_01_02_systemAction.class.php
 *  mst_01_02_01.tpl
 *  mst_01_02_02.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class mst_01_02_systemAction extends adminAction
{
	private $UserPassAutoFlgOptions = array('自動発番しない', '自動発番する');
	private $LoginKbnOptions = array('制限しない', '制限する');
	private $LockOutFlgOptions = array('ロックアウトしない', 'ロックアウトする(手動解除)', 'ロックアウトする(自動解除)');
	private $SiteCloseFlgOptions = array('閉鎖しない', '閉鎖する', '閉鎖する(期間指定)');

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		global $dispflg_arr, $useflg_arr, $aInputType;

		$message = '';
		$template = '';
		$success = 0;
		$rec = array();

		$oSC = new system_common($this->con);

		$this->set_header_info();

		if (isset($_REQUEST['mode'])) {
			$template = 'mst_01_02_02.tpl';
			if (isset($_POST['updateBtn'])) {
				$dataset = $this->oDB->make_base_dataset($_POST, 'm_systemparameter');
				$dataset['siteclosemessage'] = htmlspecialchars_decode($_POST['siteclosemessage'], ENT_QUOTES);
				$dataset['amfrom'] = $_POST['AMFromH'].$_POST['AMFromM'];
				$dataset['amto'] = $_POST['AMToH'].$_POST['AMToM'];
				$dataset['pmfrom'] = $_POST['PMFromH'].$_POST['PMFromM'];
				$dataset['pmto'] = $_POST['PMToH'].$_POST['PMToM'];
				$dataset['ntfrom'] = $_POST['NTFromH'].$_POST['NTFromM'];
				$dataset['ntto'] = $_POST['NTToH'].$_POST['NTToM'];
				$dataset['logintimefrom'] = $_POST['LoginTimeFromH'].$_POST['LoginTimeFromM'];
				$dataset['logintimeto'] = $_POST['LoginTimeToH'].$_POST['LoginTimeToM'];
				$dataset['siteclosefrom'] = strtotime($_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay'].$_POST['FromHour'].$_POST['FromMinute'].'00');
				$dataset['sitecloseto'] = strtotime($_POST['ToYear'].$_POST['ToMonth'].$_POST['ToDay'].$_POST['ToHour'].$_POST['ToMinute'].'59');
				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];

				$message = $this->check_input_data($dataset);
				if ($message == '') {
					$where = "localgovcode='"._CITY_CODE_."'";
					$rs = $this->con->autoExecute('m_systemparameter', $dataset, DB_AUTOQUERY_UPDATE, $where);
					$rc = $this->oDB->check_error($rs);
					if ($rc < 0) {
						$message = '更新できませんでした。';
						$success = -1;
					} else {
						$message = '正常に更新しました。';
						$success = 1;
					}
				} else {
					$success = -1;
				}
			}

			if ($success < 0) {
				$rec = $_POST;
				$rec['siteclosefrom'] = strtotime($_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay'].$_POST['FromHour'].$_POST['FromMinute'].'00');
				$rec['sitecloseto'] = strtotime($_POST['ToYear'].$_POST['ToMonth'].$_POST['ToDay'].$_POST['ToHour'].$_POST['ToMinute'].'59');
			} else {
				$rec = $oSC->get_system_parameters();
				$rec['AMFromH'] = substr($rec['amfrom'], 0, 2);
				$rec['AMFromM'] = substr($rec['amfrom'], 2, 2);
				$rec['AMToH'] = substr($rec['amto'], 0, 2);
				$rec['AMToM'] = substr($rec['amto'], 2, 2);
				$rec['PMFromH'] = substr($rec['pmfrom'], 0, 2);
				$rec['PMFromM'] = substr($rec['pmfrom'], 2, 2);
				$rec['PMToH'] = substr($rec['pmto'], 0, 2);
				$rec['PMToM'] = substr($rec['pmto'], 2, 2);
				$rec['NTFromH'] = substr($rec['ntfrom'], 0, 2);
				$rec['NTFromM'] = substr($rec['ntfrom'], 2, 2);
				$rec['NTToH'] = substr($rec['ntto'], 0, 2);
				$rec['NTToM'] = substr($rec['ntto'], 2, 2);
				$rec['LoginTimeFromH'] = substr($rec['logintimefrom'], 0, 2);
				$rec['LoginTimeFromM'] = substr($rec['logintimefrom'], 2, 2);
				$rec['LoginTimeToH'] = substr($rec['logintimeto'], 0, 2);
				$rec['LoginTimeToM'] = substr($rec['logintimeto'], 2, 2);
			}

			$formatD2 = create_function('$v', 'return sprintf("%02d", $v);');
			$rec['hourValues'] = array_map($formatD2, range(0, 23));
			$rec['minuteValues'] = array_map($formatD2, range(0, 59));
			$rec['UserPassAutoFlgOptions'] = $this->UserPassAutoFlgOptions;
			$rec['UserLimitDispFlgOptions'] = $useflg_arr;
			$rec['ShisetsuClassScreenFlgOptions'] = $useflg_arr;
			$rec['ShisetsuRestrictionFlgOptions'] = $useflg_arr;
			$rec['LoginKbnOptions'] = $this->LoginKbnOptions;
			$rec['LockOutFlgOptions'] = $this->LockOutFlgOptions;
			$rec['SiteCloseFlgOptions'] = $this->SiteCloseFlgOptions;
			$rec['InputTypeOptions'] = $aInputType;
		} else {
			$template = 'mst_01_02_01.tpl';
			$rec = $oSC->get_system_parameters();
			$rec['UserIDType'] = $aInputType[$rec['useridtype']];
			$rec['PwdType'] = $aInputType[$rec['pwdtype']];
			$rec['LoginTimeFrom'] = $oSC->getTimeView($rec['logintimefrom'].'00');
			$rec['LoginTimeTo'] = $oSC->getTimeView($rec['logintimeto'].'00');
			$rec['UserPassAutoFlg'] = $this->UserPassAutoFlgOptions[$rec['userpassautoflg']];
			$rec['UserLimitDispFlg'] = $useflg_arr[$rec['userlimitdispflg']];
			$rec['ShisetsuClassScreenFlg'] = $useflg_arr[$rec['shisetsuclassscreenflg']];
			$rec['ShisetsuRestrictionFlg'] = $useflg_arr[$rec['shisetsurestrictionflg']];
			$rec['LockOutFlg'] = $this->LockOutFlgOptions[$rec['lockoutflg']];
			$rec['SiteCloseFlg'] = $this->SiteCloseFlgOptions[$rec['sitecloseflg']];
		}

		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->display($template);
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if ($req['localgovname'] == '') {
			$msg .= '自治体名称を入力してください。<br>';
		}
		if ($req['mayorname'] == '') {
			$msg .= _MAYOR_.'名を入力してください。<br>';
		}
		if ($req['useridlngmin'] == '') {
			$msg .= '利用者ID最小桁数を入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['useridlngmin'])) {
			$msg .= '利用者ID最小桁数は半角数字で入力してください。<br>';
		}
		if ($req['useridlng'] == '') {
			$msg .= '利用者ID最大桁数を入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['useridlng'])) {
			$msg .= '利用者ID最大桁数は半角数字で入力してください。<br>';
		}
		if($req['useridlngmin'] > $req['useridlng']) {
			$msg .= '利用者ID桁数の値が不正です。<br>';
		} elseif($req['useridlng'] > 128) {
			$msg .= '利用者IDは最大128桁です。<br>';
		}
		if ($req['userid_size'] == '') {
			$msg .= '利用者ID入力サイズを入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['userid_size'])) {
			$msg .= '利用者ID入力サイズは半角数字で入力してください。<br>';
		}
		if ($req['pwdlngmin'] == '') {
			$msg .= 'パスワード最小桁数を入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['pwdlngmin'])) {
			$msg .= 'パスワード最小桁数は半角数字で入力してください。<br>';
		}
		if ($req['pwdlng'] == '') {
			$msg .= 'パスワード最大桁数を入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['pwdlng'])) {
			$msg .= 'パスワード最大桁数は半角数字で入力してください。<br>';
		}
		if ($req['pwdlngmin'] > $req['pwdlng']) {
			$msg .= 'パスワード桁数の値が不正です。<br>';
		} elseif ($req['pwdlng'] > 16) {
			$msg .= 'パスワードは最大16桁です。<br>';
		}
		if ($req['pwd_size'] == '') {
			$msg .= 'パスワード入力サイズを入力してください。<br>';
		} elseif (!preg_match("/^[0-9]+$/", $req['pwd_size'])) {
			$msg .= 'パスワード入力サイズは半角数字で入力してください。<br>';
		}
		if ($req['amfrom'] >= $req['amto']
			|| $req['pmfrom'] >= $req['pmto']
			|| $req['ntfrom'] >= $req['ntto']
			|| $req['amto'] >= $req['pmfrom']
			|| $req['pmto'] >= $req['ntfrom']
			|| $req['ntto'] <= $req['amfrom']) {
			$msg .= '時間帯の設定が不正です。<br>';
		}
		if ($req['logintimefrom'] > $req['logintimeto']) {
			$msg .= 'ログイン制限時間帯の設定が不正です。<br>';
		}
		if ($req['sitecloseflg'] == '2') {
			if ($req['siteclosefrom'] == $req['sitecloseto']) {
				$msg .= 'サイト閉鎖の開始日時と終了日時が同じです。<br>';
			} elseif ($req['siteclosefrom'] > $req['sitecloseto']) {
				$msg .= 'サイト閉鎖の開始日時が終了日時を超えています。<br>';
			}
		}
		return $msg;
	}
}
?>
