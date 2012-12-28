<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  presetAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/preset.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class presetAction extends Action
{
	private $oSC = null;

	function __construct($type)
	{
		parent::__construct($type);

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$this->check_login('user_menu');

		$recs = $this->get_preset_list();
		$YearMonths = $this->oSC->make_month_array(OR_MONTH_FORMAT);
		$presetCount = count($recs);

		$message = OR_MESSAGE;
		if ($presetCount == 0) {
			$message = OR_NO_FACILITY;
		}
		$condition = OR_CONDITION.' :: 【'.$_SESSION['UNAME'].'】';

		$_SESSION['SentakuMode'] = 2;

		if (!isset($_SESSION['screenflg'])) {
			$sql = 'SELECT shisetsuclassscreenflg FROM m_systemparameter WHERE localgovcode=?';
			$_SESSION['screenflg'] = $this->con->getOne($sql, array(_CITY_CODE_));
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('presetCount', $presetCount);
		$this->oSmarty->assign('YearMonths', $YearMonths);
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('BACK_LINK', '?op=user_menu');
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('preset.tpl');
	}

	function get_preset_list()
	{

		$sql = 'SELECT DISTINCT shisetsucode, shitsujyocode, combino, tourokuno';
		$sql.= ' FROM t_preset WHERE localgovcode=? AND userid=?';

		$RestrictionFlg = checkShisetsuRestriction($this->con, _CITY_CODE_);

		if ($RestrictionFlg) {
			$sql2 = 'SELECT shisetsu FROM m_user WHERE localgovcode=? AND userid=?';
			$value = $this->con->getOne($sql2, array(_CITY_CODE_, $_SESSION['UID']));
			if ($value != '') {
				$sql.= " AND (shisetsucode='".str_replace(',', "' OR shisetsucode='", $value)."')";
			}
		}
		$sql.= ' ORDER BY tourokuno, shisetsucode, shitsujyocode, combino';
		$aWhere = array(_CITY_CODE_, $_SESSION['UID']);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$CombiNameArr = $this->oSC->get_combi_name_array();

		foreach ($res as $key => $val)
		{
			$row = $this->get_shisetsu_info($val['shisetsucode'], $val['shitsujyocode']);
			$res[$key]['ShisetsuClassCode'] = $row['shisetsuclasscode'];
			$res[$key]['ShisetsuName'] = $row['shisetsuname'];
			$res[$key]['ShitsujyoName'] = $row['shitsujyoname'];
			$res[$key]['MenName'] = '';
			if ($val['combino'] != 0) {
				$res[$key]['MenName'] = $CombiNameArr[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$res[$key]['WebOpen'] = is_web_enable($row);
		}
		return $res;
	}

	function get_shisetsu_info($ShisetsuCode, $ShitsujyoCode)
	{
		$sql = "SELECT s.shisetsuclasscode, s.shisetsuname, t.shitsujyoname,";
		$sql.= " p.webuketimekbn, p.webuketimefrom, p.webuketimeto";
		$sql.= " FROM m_shitsujyou t";
		$sql.= " JOIN m_yoyakuscheduleptn p";
		$sql.= " USING (localgovcode, shisetsucode, shitsujyocode)";
		$sql.= " JOIN m_shisetsu s";
		$sql.= " USING (localgovcode, shisetsucode)";
		$sql.= " WHERE t.localgovcode=? AND t.shisetsucode=? AND t.shitsujyocode=?";

		$aWhere = array(_CITY_CODE_, $ShisetsuCode, $ShitsujyoCode);

		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}
}
?>
