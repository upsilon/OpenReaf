<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  user_menuAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/top.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class user_menuAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$this->check_login('user_menu');

		$sql = 'SELECT shisetsuclassscreenflg FROM m_systemparameter WHERE localgovcode=?';
		$screenflg = $this->con->getOne($sql, array(_CITY_CODE_));

		$ShisetsuSentaku = 'shisetsu';
		if ($screenflg == 1) {
			$ShisetsuSentaku = 'shisetsuclass';
		}

		$recs = $this->get_lot_result($_SESSION['UID']);

		if (isset($_SESSION['Y_I'])) unset($_SESSION['Y_I']);
		if (isset($_SESSION['M_I'])) unset($_SESSION['M_I']);
		if (isset($_SESSION['LIST'])) unset($_SESSION['LIST']);
		if (isset($_SESSION['HISTORY'])) unset($_SESSION['HISTORY']);

		$message = OR_CLICK_MENU;
		$condition = OR_USER_MENU.' :: 【'.$_SESSION['UNAME'].'】';

		$notice = $this->set_notice($_SESSION['UID']);

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('notice', $notice);
		$this->oSmarty->assign('ShisetsuSentaku', $ShisetsuSentaku);
		$this->oSmarty->assign('KakuteiKensu', count($recs));
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('hiddenUserButton', 1);
		$this->oSmarty->assign('BACK_LINK', '');
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('user_menu.tpl');
	}

	function get_lot_result($uid)
	{
		$oSC = new system_common($this->con);

		$ShisetsuNameArr = $oSC->get_shisetsu_name_array();
		$CombiNameArr = $oSC->get_combi_name_array();

		$sql = "SELECT p.*, s.shitsujyoname, s.shitsujyokbn
		FROM t_pulloutyoyaku p
		JOIN m_shitsujyou s
		USING (localgovcode, shisetsucode, shitsujyocode)
		JOIN  m_yoyakuscheduleptn c
		USING (localgovcode, shisetsucode, shitsujyocode)
		WHERE p.localgovcode=? AND p.userid=?
		AND p.pulloutjoukyoukbn='3'
		AND p.hitfixappdate IS NULL AND c.fixflg='1'
		ORDER BY shitsujyokbn, pulloutyoyakunum,
		shisetsucode, shitsujyocode, combino";
		$res = $this->con->getAll($sql, array(_CITY_CODE_, $uid), DB_FETCHMODE_ASSOC);
		$records = array();
		foreach ($res as $val)
		{
			if (array_key_exists($val['pulloutyoyakunum'], $records)) {
				if ($val['shitsujyokbn'] == '3') {
					$records[$val['pulloutyoyakunum']]['useShowName'].= '&nbsp;'.$val['shitsujyoname'];
				}
				continue;
			}
			$val['UseDateView'] = $oSC->date4lang($val['usedate'], _LANGUAGE_);
			$val['UseTimeView'] = $oSC->timeFormat($val['usetimefrom']).'-'.$oSC->timeFormat($val['usetimeto']);
			$val['useShowName'] = $ShisetsuNameArr[$val['shisetsucode']].'&nbsp;'.$val['shitsujyoname'];
			if ($val['combino'] != 0) {
				$val['useShowName'].='&nbsp;'.$CombiNameArr[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$uYear = substr($val['usedate'], 0, 4);
			$uMonth = substr($val['usedate'], 4, 2);

			$sql = "SELECT * FROM t_monthpulloutdate 
				WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND month=?";
			$aWhere = array(_CITY_CODE_, $val['shisetsucode'], $val['shitsujyocode'], sprintf("%d",$uMonth));
			$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

			$pYear = substr($row['pulloutopenfromday'],0,2) > $uMonth ? $uYear-1:$uYear;
			$PullOutResultDate = strtotime($pYear.$row['pulloutopenfromday'].$row['pulloutopenfromtime'].'00');

			$val['StatusName'] = '';
			if ($PullOutResultDate < time()) {
				$val['StatusName'] = '当選(未確定)';
				$records[$val['pulloutyoyakunum']] = $val;
			}
		}
		unset($res);
		return $records;
	}

	function set_notice($uid)
	{
		$sql = 'SELECT notice, notice_published, notice_expired, notice_flg';
		$sql.= ' FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if ($row['notice'] == '') return '';
		if ($row['notice_flg'] == '0') return '';
		$now = time();
		if ($row['notice_published'] <= $now && ($now <= $row['notice_expired'] || $row['notice_expired'] == 0)) {
			return $row['notice'];
		}
		return '';
	}
}
?>
