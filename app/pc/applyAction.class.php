<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  applyAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/apply.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/yoyaku_touroku.class.php';
require OPENREAF_ROOT_PATH.'/app/class/confirmation_mail.class.php';
require OPENREAF_ROOT_PATH.'/app/include/restriction.php';
require OPENREAF_ROOT_PATH.'/app/include/mail_template.php';

class applyAction extends Action
{
	private $oSC = null;
	private $oYT = null;

	function __construct($type)
	{
		parent::__construct($type);

		$this->oSC = new system_common($this->con);
		$this->oYT = new yoyaku_touroku($this->oDB, _TermClass_);
	}

	function execute()
	{
		global $aStatusName;

		$this->check_login('apply_conf');

		$message = '';
		$PresetResult = '';
		$showFeePayLimit = '';

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$CombiNo = $_SESSION['Y_I']['combino'];
		$UseDate = $_SESSION['Y_I']['usedate'];

		if (isset($_POST['apply'])) {
			$_REQUEST['useninzu'] = intval($_REQUEST['useninzu']);
			$_REQUEST['ninzu1'] = intval($_REQUEST['ninzu1']);
			$_REQUEST['ninzu2'] = intval($_REQUEST['ninzu2']);
			$_REQUEST['useninzu'] += $_REQUEST['ninzu1'] + $_REQUEST['ninzu2'];
			$msg = $this->oYT->check_ninzu($_REQUEST, $_SESSION['Y_I'], $_SESSION['ShowDanjyoNinzuFlg'], false); 
			if ($msg == '') {
				$_SESSION['Y_I']['useninzu'] = $_REQUEST['useninzu'];
				$_SESSION['Y_I']['ninzu1'] = $_REQUEST['ninzu1'];
				$_SESSION['Y_I']['ninzu2'] = $_REQUEST['ninzu2'];
			} else {
				$this->display_error_msg($msg, '?op=apply_conf');
			}

			if (!$this->complete_yoyaku($_SESSION['Y_I'], $message)) {
				$this->display_error_msg($message, '?op=apply_conf');
			}

			$oCM = new confirmation_mail($this->con, $_SESSION['Y_I']['YoyakuKbn'], 0);
			$mailinfo = $oCM->get_mail_info($_SESSION['UID']);
			if ($mailinfo['mailsendflg'] == '1' && !empty($mailinfo['mailadr'])) {
				$maildata = $_SESSION['M_I'];
				$maildata['shinsakbn'] = 0;

				if ($_SESSION['Y_I']['YoyakuKbn'] == 2) {
					if ($_SESSION['Y_I']['shinsaflg'] == 1) {
						$maildata['shinsakbn'] = '4';
					} else {
						$showFeePayLimit = $this->oSC->get_pay_day($ShisetsuCode, $ShitsujyoCode, $_SESSION['Y_I']['yoyakunum']);
					}
				}

				$maildata['namesei'] = $_SESSION['UNAME'];
				$maildata['yoyakunum'] = $_SESSION['Y_I']['yoyakunum'];
				$maildata['Fee'] = number_format($_SESSION['Y_I']['TotalFee']);
				$maildata['shisetsucode'] = $ShisetsuCode;
				$maildata['shitsujyoname'] = $maildata['ShitsujyoName'];
				if ($CombiNo != 0) {
					$maildata['shitsujyoname'].= ' '.$maildata['CombiName'];
				}
				$maildata['Notice'] = $showFeePayLimit;
				$bodydata = $oCM->make_body_data($maildata);
				$body = confirm_mail_body($bodydata);
				$body.= '送信日時: '.date('Y-m-d H:i')."\r\n";
				$subject = $oCM->make_subject();
				$oCM->send_mail($mailinfo['mailadr'], $subject, $body, $this->type);
				unset($maildata);
				unset($bodydata);
			}
		}

		$StatusCode = 4;
		if ($_SESSION['Y_I']['YoyakuKbn'] == 2) {
			if ($_SESSION['Y_I']['shinsaflg'] == 1) {
				$StatusCode = 12;
			} else {
				$StatusCode = 3;
				$showFeePayLimit = $this->oSC->get_pay_day($ShisetsuCode, $ShitsujyoCode, $_SESSION['Y_I']['yoyakunum']);
			}
		}
		$YoyakuCondition = $aStatusName[$StatusCode];

		$condition = OR_APPLICATION_COMPLETE.' :: 【'.$_SESSION['UNAME'].'】';

		$this->oSmarty->assign('info', $_SESSION['M_I']);
		$this->oSmarty->assign('CombiNo', $CombiNo);
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());

		if (isset($_GET['preset'])) {
			$PresetResult = $this->set_preset_data($_SESSION['Y_I']);
			if (_TermClass_ == 'Mobile' || _TermClass_ == 'SmartPhone') {
				$this->oSmarty->assign('message', $PresetResult);
				$this->oSmarty->assign('BACK_LINK', '?op=apply');
				$this->oSmarty->display('preset_res.tpl');
				exit();
			}
		}

		$RepeatLink = 'index.php?op=monthly&UseYM='.substr($UseDate, 0, 6);

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('Fee', number_format($_SESSION['Y_I']['TotalFee']));
		$this->oSmarty->assign('YoyakuNum', $_SESSION['Y_I']['yoyakunum']);
		$this->oSmarty->assign('showFeePayLimit', $showFeePayLimit);
		$this->oSmarty->assign('YoyakuCondition', $YoyakuCondition);
		if (_TermClass_ != 'Mobile' && _TermClass_ != 'SmartPhone') {
			$this->oSmarty->assign('PresetResult', $PresetResult);
		}
		$this->oSmarty->assign('REPEAT_LINK', $RepeatLink);
		$this->oSmarty->assign('BACK_LINK', '');
		$this->oSmarty->display('apply.tpl');
	}

	function complete_yoyaku(&$ses, &$message)
	{
		$checkres = restrict_request($this->con, $ses, $ses['YoyakuKbn']);
		if ($checkres != 0) {
			$message = put_restriction_error_msg($checkres);
			return false;
		}

		$errmsg = array();
		$errmsg[4] = OR_DUPLICATE_CONDITION;
		$errmsg[6] = OR_ALREADY_RESERVED;

		$this->con->query('START TRANSACTION');

		if ($ses['YoyakuKbn'] == 1) {
			$check = $this->oYT->check_duplicate_lot($ses);
			if ($check == 11) {
				$YoyakuNum = $this->oYT->emit_yoyaku_number($ses);
				if (!$YoyakuNum) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at YoyakuNum');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				if (!$this->oYT->insert_lot($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at PullOutYoyaku');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				if (!$this->oYT->insert_yoyaku_shinsei($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at YoyakuFeeShinsei');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				$LotDay = sprintf('%2d月%2d日',
					intval(substr($this->oYT->get_LotDay(), 0, 2)),
					intval(substr($this->oYT->get_LotDay(), 2, 2)));
				$_SESSION['M_I']['LotDay'] = $LotDay;
				$message = sprintf(OR_LOTTERY_COMPLETE, $LotDay);
				$ses['yoyakunum'] = $YoyakuNum;
			} else {
				$this->con->query('ROLLBACK');
				$message = $errmsg[$check];
				return false;
			}
		} else {
			$check = $this->oYT->check_duplicate_yoyaku($ses);
			if ($check == 10) {
				$YoyakuNum = $this->oYT->emit_yoyaku_number($ses);
				if (!$YoyakuNum) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at YoyakuNum');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				if (!$this->oYT->set_yoyaku($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at YoyakuKanri');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				if (!$this->oYT->insert_yoyaku_shinsei($ses, $YoyakuNum)) {
					$this->con->query('ROLLBACK');
					//err_output('apply', 'error occurred at YoyakuFeeShinsei');
					$message = OR_ALREADY_RESERVED;
					return false;
				}
				$message = OR_COMPLETE;
				$ses['yoyakunum'] = $YoyakuNum;
			} else {
				$this->con->query('ROLLBACK');
				$message = $errmsg[$check];
				return false;
			}
		}
		$this->con->query('COMMIT');
		return true;
	}

	function set_preset_data(&$ses)
	{
		$maxNum = intval(_PRESET_MAX_NUM_);
		if ($maxNum < 1) return '現在は登録できません。';

		$sql = "SELECT COUNT(*) FROM t_preset
			WHERE userid=? AND localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?";
		$aWhere = array($ses['userid'], $ses['localgovcode'], $ses['shisetsucode'], $ses['shitsujyocode'], $ses['combino']);
		$presetCount = $this->con->getOne($sql, $aWhere);
		if ($presetCount > 0) return 'この施設は登録済みです。';

		$sql = "SELECT Max(tourokuno)-Min(tourokuno)+1 AS cur_num,
			Max(tourokuno) AS max_num, Min(tourokuno) AS min_num
			FROM t_preset WHERE userid=?";
		$row = $this->con->getRow($sql, array($ses['userid']), DB_FETCHMODE_ASSOC);
		if (empty($row['cur_num'])) $row['cur_num'] = 0;

		if ($row['cur_num'] >= $maxNum) {
			if ($row['min_num'] > 0) {
				$sql = "DELETE FROM t_preset WHERE userid=? AND tourokuno=?";
				$aWhere = array($ses['userid'], $row['min_num']);
				$this->con->query($sql, $aWhere);
			}
		}

		$dataset = array();
		$dataset['userid'] = $ses['userid'];
		$dataset['localgovcode'] = $ses['localgovcode'];
		$dataset['shisetsucode'] = $ses['shisetsucode'];
		$dataset['shitsujyocode'] = $ses['shitsujyocode'];
		$dataset['appdatefrom'] = date('Ymd');
		$dataset['mencode'] = '';
		$dataset['combino'] = $ses['combino'];
		$dataset['tourokuno'] = $row['max_num'] +1;
		$this->oDB->insert('t_preset', $dataset);
		return 'よく使う施設に登録しました。';
	}
}
?>
