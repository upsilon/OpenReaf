<?php
/*
 *  Copyright 2011-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  eventAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/guide.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class eventAction
{
	private $oDB = null;
	private $con = null;
	private $oSmarty = null;


	function __construct($type)
	{
		$this->oDB = new DBUtil();
		$this->con = $this->oDB->connect();
		$this->oSmarty = new MySmarty($type);
	}

	function execute()
	{
		$oSC = new system_common($this->con);

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_POST['ShisetsuCode'];

		//表示する年月を取得
		$UseYM = empty($_POST['UseYM']) ? date('Ym') : $_POST['UseYM'];
		$Year = intval(substr($UseYM, 0, 4));
		$Month = intval(substr($UseYM, 4, 2));

		$YearMonths = $oSC->make_month_array(OR_MONTH_FORMAT);

		$sql = "SELECT DISTINCT y.yoyakuname,
			y.usedatefrom, y.usetimefrom, y.usetimeto,
			y.shitsujyocode, y.combino, s.shitsujyoname
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			WHERE y.localgovcode=? AND y.shisetsucode=?
			AND y.usedatefrom LIKE '$UseYM%'
			AND y.yoyakuname IS NOT NULL AND y.yoyakuname<>''
			AND y.honyoyakukbn<>'03' AND y.honyoyakukbn<>'04'
			AND s.shitsujyokbn<>'3'
			ORDER BY usedatefrom, usetimefrom, usetimeto";
		$rows = $this->con->getAll($sql, array(_CITY_CODE_, $ShisetsuCode), DB_FETCHMODE_ASSOC);

		$aCombi = $oSC->get_combi_name_array($ShisetsuCode);

		$res = array();
		foreach ($rows as $val)
		{
			$day = intval(substr($val['usedatefrom'], 6, 2));
			if (!isset($res[$day])) {
				$res[$day] = array();
			}
			$combiname = '';
			if ($val['combino'] != 0) {
				$combiname = $aCombi[$ShisetsuCode][$val['shitsujyocode']][$val['combino']];
			}
			$values = array(
					'name' => $val['yoyakuname'],
					'usetime' => $oSC->timeFormat($val['usetimefrom']).'-'.$oSC->timeFormat($val['usetimeto']),
					'shitsujyoname' => $val['shitsujyoname'],
					'combiname' => $combiname
					);
			$res[$day][] = $values;
		}
		unset($aCombi, $rows);

		$firstDate = mktime(0, 0, 0, $Month, 1, $Year);
		$last_day = date('t', $firstDate);
		$dow = date('w', $firstDate);

		$recs = array();
		for ($i = 1; $i <= $last_day; ++$i) {
			$recs[$i] = array(
					'dow' => $dow%7,
					'event' => array()
					);
			if (isset($res[$i])) $recs[$i]['event'] = $res[$i];
			++$dow;
		}
		unset($res);

		$strYM = sprintf('%s年%d月', $oSC->getNengouView($Year), $Month);
		if (_LANGUAGE_ != 'ja')  {
			$strYM = date(OR_YEARMONTH_FORMAT, $firstDate);
		}

		$this->oSmarty->assign('condition', OR_EVENT_GUIDE);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('ShisetsuName', $oSC->get_shisetsu_name($ShisetsuCode));
		$this->oSmarty->assign('ShisetsuCode', $ShisetsuCode);
		$this->oSmarty->assign('YearMonths', $YearMonths);
		$this->oSmarty->assign('SelectedMonth', $UseYM);
		$this->oSmarty->assign('strYM', $strYM);
		$this->oSmarty->assign('aWeek', $GLOBALS['aWeek']);
		$this->oSmarty->assign('MODE', 3);
		if (_TermClass_ == 'Kiosk') {
			$this->oSmarty->assign('BACK_LINK', '?op=guide');
		}
		$this->oSmarty->display('event.tpl');
	}
}
?>
