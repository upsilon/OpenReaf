<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  listAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/list.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class listAction extends Action
{
	private $sort;

	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$DISP_MAX = intval(_LIST_DISP_MAX_);
		$fixflg = 0;

		$this->check_login('user_menu');

		$this->sort = 'num_dsc';
		if (isset($_POST['sort'])) {
			$this->sort = $_POST['sort'];
		} elseif (isset($_SESSION['LIST'])) {
			$this->sort = $_SESSION['LIST']['sort'];
		}
		$PageNO = 0;
		if (isset($_GET['page_no'])) {
			$PageNO = intval($_GET['page_no']);
		} elseif (isset($_SESSION['LIST'])) {
			$PageNO = $_SESSION['LIST']['page_no'];
		}
		$_SESSION['LIST']['sort'] = $this->sort;
		$_SESSION['LIST']['page_no'] = $PageNO;

		$recs = $this->get_reserve_list($_SESSION['UID'], $fixflg);
		usort($recs, array($this, 'yoyaku_sort'));

		$total = count($recs);
		$Offset = $PageNO * $DISP_MAX;

		$entries = array();
		$i = 0;
		foreach ($recs as $val)
		{
			if ($Offset <= $i && $i < ($Offset + $DISP_MAX)) {
				$val['order_num'] = sprintf(OR_ORDER_NUMBER, $i + 1);
				$entries[] = $val;
			}
			++$i;
		}
		unset($recs);

		$max_page = ceil($total / $DISP_MAX);

		$pages = array();
		for ($i = 0; $max_page > 1 && $i < $max_page; ++$i)
		{
			$set = ($i == $PageNO) ? 1 : 0;
			$pages[] = array('pagenum' => $i+1, 'url' => 'index.php?op=list&page_no='.$i.'&sort='.$this->sort, 'set' => $set);
		}
		$nextpage = '';
		if (($PageNO + 1) < $max_page && $max_page > 1) {
			$nextpage = 'index.php?op=list&page_no='.($PageNO+1).'&sort='.$this->sort;
		}
		$prevpage = '';
		if ($PageNO > 0) {
			$prevpage = 'index.php?op=list&page_no='.($PageNO-1).'&sort='.$this->sort;
		}

		$message = '';
		if (count($entries) < 1) {
			$message = OR_NO_RESERVATION;
			if (_LIST_RESERVE_ONLY_) {
				$message.= OR_NOTICE;
			}
		}
		$condition = OR_RESERVATION_LIST.' :: 【'.$_SESSION['UNAME'].'】';

		$_SESSION['LIST_NAME'] = get_class($this);

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		if (_TermClass_ == 'Mobile' || _TermClass_ == 'SmartPhone') {
			$this->oSmarty->assign('total_num', sprintf(OR_TOTAL_NUMBER, $total));
		} else {
			$this->oSmarty->assign('sort', $this->sort);
		}
		$this->oSmarty->assign('fixflg', $fixflg);
		$this->oSmarty->assign('entries', $entries);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('pages', $pages);
		$this->oSmarty->assign('prevpage', $prevpage);
		$this->oSmarty->assign('nextpage', $nextpage);
		$this->oSmarty->assign('op', 'list');
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('BACK_LINK', '?op=user_menu');
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('list.tpl');
	}

	function get_reserve_list($UID, &$fixflg)
	{
		global $aStatusName, $aHitFixAppStatus;

		$oSC = new system_common($this->con);

		$aShisetsu = $oSC->get_shisetsu_name_array();
		$aCombi = $oSC->get_combi_name_array();
		$aFixFlg = $oSC->get_fixflg_array();

		$aWhere = array(_CITY_CODE_, $UID);
		$sql = "SELECT DISTINCT y.pulloutyoyakunum as yoyakunum,
			y.shisetsucode, y.shitsujyocode, y.combino,
			y.pulloutjoukyoukbn, y.hitfixappdate,
			y.usedate, y.usetimefrom, y.usetimeto,
			s.shitsujyokbn, s.shitsujyoname,
			f.bihinyoyakunum, 'p' AS class
			FROM t_pulloutyoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			ON y.localgovcode=f.localgovcode AND y.pulloutyoyakunum=f.yoyakunum
			WHERE y.localgovcode=? AND y.userid=?
			AND DATE(y.usedate)>=DATE(NOW())";

		if (_LIST_RESERVE_ONLY_) {
			$sql.= " AND y.pulloutjoukyoukbn<>'4'";
		} else {
			$sql.= " UNION SELECT DISTINCT y.pulloutyoyakunum as yoyakunum,
				y.shisetsucode, y.shitsujyocode, y.combino,
				y.pulloutjoukyoukbn, y.hitfixappdate,
				y.usedate, y.usetimefrom, y.usetimeto,
				s.shitsujyokbn, s.shitsujyoname,
				f.bihinyoyakunum, 'hp' AS class
				FROM h_pullout y
				JOIN m_shitsujyou s
				USING (localgovcode, shisetsucode, shitsujyocode)
				JOIN h_fee f
				ON y.localgovcode=f.localgovcode AND y.pulloutyoyakunum=f.yoyakunum
				WHERE y.localgovcode=? AND y.userid=?
				AND DATE(y.usedate)>=DATE(NOW())";
			$aWhere = array(_CITY_CODE_, $UID, _CITY_CODE_, $UID);
		}
		$sql.= ' ORDER BY yoyakunum, shitsujyokbn, shitsujyocode';
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$records = array();

		$fixflg = 0;
		foreach ($res as $val)
		{
			if (array_key_exists($val['yoyakunum'], $records)) {
				if ($val['shitsujyokbn'] == '3') {
					if ($val['pulloutjoukyoukbn'] == 4 && $records[$val['yoyakunum']]['pulloutjoukyoukbn'] != 4 && $records[$val['yoyakunum']]['pulloutjoukyoukbn'] != 1 && $records[$val['yoyakunum']]['StatusCode'] != 4 && $records[$val['yoyakunum']]['StatusCode'] != 5) {
						$records[$val['yoyakunum']]['useShowName'][] = '<span style="color:red;">'.$val['shitsujyoname'].'(落選)</span>';
					} else {
						$records[$val['yoyakunum']]['useShowName'][] = $val['shitsujyoname'];
					}
				}
				continue;
			}

			$val['fixflg'] = $aFixFlg[$val['shisetsucode']][$val['shitsujyocode']];
			$val['UseDateView'] = $oSC->date4lang($val['usedate'], _LANGUAGE_);
			$val['UseTimeView'] = $oSC->timeFormat($val['usetimefrom']).'-'.$oSC->timeFormat($val['usetimeto']);
			$val['ShisetsuName'] = $aShisetsu[$val['shisetsucode']];
			$val['useShowName'] = array();
			if ($val['combino'] != 0) {
				$val['shitsujyoname'].= '&nbsp;'.$aCombi[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$val['useShowName'][] = $val['shitsujyoname'];

			$val['StatusCode'] = 4;
			$HitFixApp = 0;
			if ($val['pulloutjoukyoukbn'] == 1) {
				if ($val['class'] == 'hp') {
					$val['StatusCode'] = 8;
				}
			} else {
				$uYear = substr($val['usedate'], 0, 4);
				$uMonth = substr($val['usedate'], 4, 2);

				$sql = "SELECT * FROM t_monthpulloutdate 
					WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND month=?";
				$aWhere = array(_CITY_CODE_, $val['shisetsucode'], $val['shitsujyocode'], sprintf("%d",$uMonth));
				$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

				$pYear = substr($row['pulloutday'],0,2) > $uMonth ? $uYear-1:$uYear;
				$PullOutDate = strtotime($pYear.$row['pulloutday'].$row['pullouttime'].'00');
				$pYear = substr($row['pulloutopenfromday'],0,2) > $uMonth ? $uYear-1:$uYear;
				$PullOutResultDate = strtotime($pYear.$row['pulloutopenfromday'].$row['pulloutopenfromtime'].'00');

				if ($PullOutDate < time() && $PullOutResultDate >= time()) {
					$val['StatusCode'] = 5;
				} elseif ($PullOutResultDate < time()) {
					if ($val['pulloutjoukyoukbn'] == 3) {
						if ($val['class'] == 'hp') {
							$val['StatusCode'] = 6;
						} elseif ($val['fixflg'] == 1 && $val['hitfixappdate'] == '') {
							$val['StatusCode'] = 1;
							$HitFixApp = 1;
							$fixflg = 1;
						} else {
							$val['StatusCode'] = 2;
						}
					} else {
						$val['StatusCode'] = 10;
					}
				}
			}
			$val['StatusName'] = $aStatusName[$val['StatusCode']];
			if ($HitFixApp == 1) {
				if (_TermClass_ != 'Mobile' && _TermClass_ != 'SmartPhone') {
					$val['StatusName'] .= '<br>';
				}
				$val['StatusName'] .= '('.$aHitFixAppStatus[1].')';
			}

			$records[$val['yoyakunum']] = $val;
		}
		unset($res);

		$aWhere = array(_CITY_CODE_, $UID, _CITY_CODE_, $UID);
		$sql = "SELECT DISTINCT y.yoyakunum, shinsakbn, escapeflg,
			y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom as usedate, y.usetimefrom, y.usetimeto,
			s.shitsujyokbn, s.shitsujyoname,
			f.bihinyoyakunum, 'y' AS class
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			WHERE y.localgovcode=? AND y.userid=?
			AND DATE(y.usedatefrom)>=DATE(NOW())
			UNION SELECT DISTINCT y.yoyakunum, shinsakbn, escapeflg,
			y.shisetsucode, y.shitsujyocode, y.combino,
			y.usedatefrom as usedate, y.usetimefrom, y.usetimeto,
			s.shitsujyokbn, s.shitsujyoname,
			f.bihinyoyakunum, 'h' AS class
			FROM h_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN h_fee f
			USING (localgovcode, yoyakunum)
			WHERE y.localgovcode=? AND y.userid=?
			AND DATE(y.usedatefrom)>=DATE(NOW())
			ORDER BY yoyakunum, shitsujyokbn, shitsujyocode";
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$pkeys = array_keys($records);

		foreach ($res as $val)
		{
			$val['UseTimeView'] = $oSC->timeFormat($val['usetimefrom']).'-'.$oSC->timeFormat($val['usetimeto']);
			if (in_array($val['yoyakunum'], $pkeys)) {
				if ($records[$val['yoyakunum']]['StatusCode'] != 5) {
					$records[$val['yoyakunum']]['UseTimeView'] = $val['UseTimeView'];
				}
				continue;
			}
			if (array_key_exists($val['yoyakunum'], $records)) {
				if ($val['shitsujyokbn'] == '3') {
					$records[$val['yoyakunum']]['useShowName'][] = $val['shitsujyoname'];
				}
				continue;
			}

			$val['StatusCode'] = 3;
			switch (intval($val['shinsakbn']))
			{
				case 1:
					$val['StatusCode'] = 13;
					break;
				case 2:
					$val['StatusCode'] = 14;
					break;
				case 3:
					$val['StatusCode'] = 15;
					break;
				case 4:
					$val['StatusCode'] = 12;
					break;
			}
			if ($val['escapeflg'] == '1') {
				$val['StatusCode'] = 9;
			}
			if ($val['class'] == 'h') {
				$val['StatusCode'] = 7;
			}
			
			$val['StatusName'] = $aStatusName[$val['StatusCode']];
			$val['UseDateView'] = $oSC->date4lang($val['usedate'], _LANGUAGE_);
			$val['ShisetsuName'] = $aShisetsu[$val['shisetsucode']];
			$val['useShowName'] = array();
			if ($val['combino'] != 0) {
				$val['shitsujyoname'].= '&nbsp;'.$aCombi[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$val['useShowName'][] = $val['shitsujyoname'];
			$val['pulloutjoukyoukbn'] = '';

			$records[$val['yoyakunum']] = $val;
		}
		unset($res);

		return $records;
	}

	function yoyaku_sort($a, $b)
	{
		switch ($this->sort)
		{
		case 'stt_asc' :
			if ($a['StatusCode'] < $b['StatusCode']) return -1;
			if ($a['StatusCode'] > $b['StatusCode']) return 1;
			if ($a['usedate'] < $b['usedate']) return -1;
			if ($a['usedate'] > $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			if ($a['shisetsucode'] < $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] > $b['shisetsucode']) return 1;
			break;
		case 'stt_dsc' :
			if ($a['StatusCode'] > $b['StatusCode']) return -1;
			if ($a['StatusCode'] < $b['StatusCode']) return 1;
			if ($a['usedate'] < $b['usedate']) return -1;
			if ($a['usedate'] > $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			if ($a['shisetsucode'] < $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] > $b['shisetsucode']) return 1;
			break;
		case 'day_asc' :
			if ($a['usedate'] < $b['usedate']) return -1;
			if ($a['usedate'] > $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			if ($a['shisetsucode'] < $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] > $b['shisetsucode']) return 1;
			break;
		case 'day_dsc' :
			if ($a['usedate'] > $b['usedate']) return -1;
			if ($a['usedate'] < $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			if ($a['shisetsucode'] < $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] > $b['shisetsucode']) return 1;
			break;
		case 'num_asc' :
			if ($a['yoyakunum'] < $b['yoyakunum']) return -1;
			if ($a['yoyakunum'] > $b['yoyakunum']) return 1;
			break;
		case 'fac_asc' :
			if ($a['shisetsucode'] < $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] > $b['shisetsucode']) return 1;
			if ($a['usedate'] < $b['usedate']) return -1;
			if ($a['usedate'] > $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			break;
		case 'fac_dsc' :
			if ($a['shisetsucode'] > $b['shisetsucode']) return -1;
			if ($a['shisetsucode'] < $b['shisetsucode']) return 1;
			if ($a['usedate'] < $b['usedate']) return -1;
			if ($a['usedate'] > $b['usedate']) return 1;
			if ($a['usetimefrom'] < $b['usetimefrom']) return -1;
			if ($a['usetimefrom'] > $b['usetimefrom']) return 1;
			break;
		case 'num_dsc' :
		default :
			if ($a['yoyakunum'] > $b['yoyakunum']) return -1;
			if ($a['yoyakunum'] < $b['yoyakunum']) return 1;
			break;
		}
		return 0;
	}
}
?>
