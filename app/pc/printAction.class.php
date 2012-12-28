<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  printAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class printAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		global $aStatusName;

		$this->check_login('top');

		$oSC = new system_common($this->con);

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$YoyakuNum = $_SESSION['Y_I']['yoyakunum'];

		$sql = 'SELECT appdate, apptime FROM t_yoyaku WHERE localgovcode=? AND yoyakunum=?';
		if ($_SESSION['Y_I']['YoyakuKbn'] == 1) {
			$sql = 'SELECT pulloutukedate AS appdate, pulloutuketime AS apptime FROM t_pulloutyoyaku WHERE localgovcode=? AND pulloutyoyakunum=?';
		}
		$row = $this->con->getRow($sql, array($LocalGovCode, $YoyakuNum), DB_FETCHMODE_ASSOC);

		$StatusCode = 4;
		$showFeePayLimit = '';
		if ($_SESSION['Y_I']['YoyakuKbn'] == 2) {
			if ($_SESSION['Y_I']['shinsaflg'] == 1) {
				$StatusCode = 12;
			} else {
				$StatusCode = 3;
				$showFeePayLimit = $oSC->get_pay_day($ShisetsuCode, $ShitsujyoCode, $YoyakuNum);
			}
		}
		$YoyakuCondition = $aStatusName[$StatusCode];

		$this->oSmarty->assign('info', $_SESSION['M_I']);
		$this->oSmarty->assign('CombiNo', $_SESSION['Y_I']['combino']);
		$this->oSmarty->assign('Fee', number_format($_SESSION['Y_I']['TotalFee']));
		$this->oSmarty->assign('YoyakuNum', $YoyakuNum);
		$this->oSmarty->assign('showFeePayLimit', $showFeePayLimit);
		$this->oSmarty->assign('YoyakuCondition', $YoyakuCondition);
		$this->oSmarty->assign('AppDate', $oSC->put_wareki_date($row['appdate']));
		$this->oSmarty->assign('AppTime', $oSC->timeFormat($row['apptime']));
		$this->oSmarty->assign('UID', $_SESSION['UID']);
		$this->oSmarty->assign('Name', $_SESSION['UNAME']);
		$this->oSmarty->display('print.tpl');
	}
}
?>
