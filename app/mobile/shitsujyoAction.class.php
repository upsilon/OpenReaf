<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  shitsujyoAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class shitsujyoAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$oSC = new system_common($this->con);

		$ShisetsuCode = '';
		if (isset($_REQUEST['ShisetsuCode'])) {
			$_SESSION['Y_I']['shisetsuclasscode'] = $_REQUEST['ShisetsuClassCode'];
			$ShisetsuCode = $_REQUEST['ShisetsuCode'];
			$_SESSION['Y_I']['shisetsucode'] = $ShisetsuCode;
		} elseif (isset($_SESSION['Y_I']['shisetsucode'])) {
			$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		}

		$sql = 'SELECT s.shitsujyocode, count(m.mencode) as mencount';
		$sql.= ' FROM m_shitsujyou s';
		$sql.= ' LEFT JOIN m_men m USING (localgovcode, shisetsucode, shitsujyocode)';
		$sql.= ' WHERE s.localgovcode=? AND s.shisetsucode=?';
		$sql.= ' GROUP BY shitsujyocode ORDER BY shitsujyocode';

		$aWhere = array(_CITY_CODE_, $ShisetsuCode);
		$recs = $this->con->getAll($sql, $aWhere);
		$menCount = array();
		foreach ($recs as $val) $menCount[$val[0]] = $val[1];

		$sql = "SELECT s.shitsujyocode, s.shitsujyoname, s.shitsujyoskbcode,";
		$sql.= " p.webuketimekbn, p.webuketimefrom, p.webuketimeto";
		$sql.= " FROM m_shitsujyou s";
		$sql.= " JOIN m_yoyakuscheduleptn p USING (localgovcode, shisetsucode, shitsujyocode)";
		$sql.= " WHERE s.localgovcode=? AND s.shisetsucode=?";
		$sql.= " AND s.shitsujyokbn<'3' AND s.openflg='1'";
		$sql.= " AND DATE(s.appdatefrom)<=DATE(NOW())";
		$sql.= " AND (s.haishidate>? OR s.haishidate='' OR s.haishidate IS NULL)";
		$sql.= " ORDER BY shitsujyoskbcode, shitsujyocode";

		$aWhere = array(_CITY_CODE_, $ShisetsuCode, date('Ymd'));
		$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$total = count($recs);

		if ($total == 1 && is_web_enable($recs[0]) == 1 && $menCount[$recs[0]['shitsujyocode']] == 0) {
			$_SESSION['skip_stj'] = $recs[0]['shitsujyocode'];
			$_SESSION['Y_I']['shitsujyocode'] = $recs[0]['shitsujyocode'];
			$_SESSION['Y_I']['combino'] = 0;
			$_SESSION['Y_I']['mencode'] = array('ZZ');
			$class_name = 'monthlyAction';
			require OPENREAF_ROOT_PATH.'/app/'.$this->type.'/'.$class_name.'.class.php';
			$oAction = new $class_name($this->type);
			$oAction->execute();
			exit();
		}

		$res = array();
		foreach ($recs as $val)
		{
			$val['WebOpen'] = is_web_enable($val);
			if ($menCount[$val['shitsujyocode']] == 0) {
				$val['combino'] = 0;
				$val['combiname'] = '';
				$res[] = $val;
			} else {
				$list = $this->get_combi_list($ShisetsuCode, $val['shitsujyocode']);
				foreach ($list as $v)
				{
					$val['combino'] = $v['combino'];
					$val['combiname'] = $v['combiname'];
					$res[] = $val;
				}
			}
		}
		unset($recs);

		$message = OR_CHOOSE_A_PLACE;
		$mode = 0;
		$condition = OR_PLACE_CHOICE;

		if (isset($_SESSION['UID'])) {
			$mode = 1;
			$condition.= ' :: 【'.$_SESSION['UNAME'].'】';
		}

		$ShisetsuName = $oSC->get_shisetsu_name($ShisetsuCode);

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $res);
		$this->oSmarty->assign('ShisetsuName', $ShisetsuName);
		$this->oSmarty->assign('MODE', $mode);
		$this->oSmarty->assign('BACK_LINK', '?op=shisetsu');
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('shitsujyo.tpl');
	}

	function get_combi_list($scd, $rcd)
	{
		$sql = "SELECT DISTINCT c.combino, c.combiskbno";
		$sql.= " FROM m_mencombination c";
		$sql.= " JOIN m_men m USING (localgovcode, shisetsucode, shitsujyocode, mencode)";
		$sql.= " WHERE c.localgovcode=? AND c.shisetsucode=?";
		$sql.= " AND c.shitsujyocode=?";
		$sql.= " AND (DATE(m.appdatefrom)>DATE(NOW())";
		$sql.= " OR (m.menhaishidate<=? AND m.menhaishidate<>'' AND m.menhaishidate IS NOT NULL))";
		$sql.= " ORDER BY combiskbno, combino";

		$aWhere = array(_CITY_CODE_, $scd, $rcd, date('Ymd'));
		$HaishiCombiNo = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_FLIPPED);

		$sql = "SELECT DISTINCT combino, combiname, combiskbno FROM m_mencombination";
		$sql.= " WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?";
		$sql.= " AND openflg='1' ORDER BY combiskbno, combino";
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$rows = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$res = array();
		if (isset($HaishiCombiNo[0])) {
			foreach ($rows as $val)
			{
				if (in_array($val['combino'], $HaishiCombiNo[0])) continue;
				$res[] = $val;
			}
			unset($HaishiCombiNo);
		} else {
			$res = $rows;
		}
		unset($rows);
		return $res;
	}
}
?>
