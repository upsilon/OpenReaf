<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  充当
 *
 *  rsv_03_05_jyutouAction.class.php
 *  rsv_03_05.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';
require OPENREAF_ROOT_PATH.'/app/class/log.class.php';

class rsv_03_05_jyutouAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$scd = '';

		$this->set_header_info();

		$uid = $_POST['UserID'];
		$yoyakuNum = isset($_POST['YoyakuNum']) ? $_POST['YoyakuNum'] : '';

		$res = $this->get_db_info($uid, $yoyakuNum);

		if (isset($_POST['commitBtn'])) {
			if ($this->update_db_info($_POST, $res[0])) {
				$message = '充当処理が完了しました。';
				$success = 1;
				$scd = $res[0]['shisetsucode'];
				$res = $this->get_db_info($uid, $yoyakuNum);
			} else {
				$message = '充当処理ができませんでした。';
			}
		} elseif (isset($_POST['receiptBtn'])) {
			$_SESSION['rsv_01_04'] = array(
					'YoyakuNum' => $yoyakuNum,
					'FromYear' => substr($res[0]['usedate'], 0, 4),
					'FromMonth' => substr($res[0]['usedate'], 4, 2),
					'FromDay' => substr($res[0]['usedate'], 6, 2),
					'ShisetsuCode' => $res[0]['shisetsucode'],
					'ShitsujyoCode' => $res[0]['shitsujyocode'],
					'searchBtn' => '検索'
				);
			header('Location:index.php?op=rsv_02_05_receipt&YoyakuNum='.$yoyakuNum);
			return;
		}

		$recs = $this->remake_data($res);
		unset($res);

		$this->oSmarty->assign('req', $_POST);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('user', $this->oSC->get_user_data($uid));
		$this->oSmarty->assign('YoyakuNum', $yoyakuNum);
		$this->oSmarty->assign('ShisetsuCode', $scd);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('op', 'rsv_03_05_jyutou');
		$this->oSmarty->display('rsv_03_05.tpl');
	}

	function get_db_info($uid, $yoyakuNum)
	{
		$sql = "SELECT y.userid, y.yoyakunum,
			f.suuryo, y.honyoyakukbn,
			y.usedatefrom as usedate, y.usetimefrom, y.usetimeto,
			y.localgovcode, y.shisetsucode, y.shitsujyocode, y.combino,
			s.shitsujyokbn, s.shitsujyoname, 0 as syunougaku
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeshinsei f
			USING (localgovcode, yoyakunum)
			WHERE y.localgovcode=? AND y.userid=? AND y.usedatefrom>=?
			AND y.yoyakukbn='02' AND y.honyoyakukbn='01'";
		if ($yoyakuNum == '') {
			$sql.= " AND f.suuryo <> 0";
		} else {
			$sql.= " AND y.yoyakunum='".$yoyakuNum."'";
		}

		$sql.= " UNION SELECT y.userid, y.yoyakunum,
			u.shisetsufee as suuryo, y.honyoyakukbn,
			y.usedatefrom as usedate, y.usetimefrom, y.usetimeto,
			y.localgovcode, y.shisetsucode, y.shitsujyocode, y.combino,
			s.shitsujyokbn, s.shitsujyoname,
			(u.cash+u.chg+u.ticket+u.kouzafurikomi+u.others+u.jyutou) as syunougaku
			FROM t_yoyaku y
			JOIN m_shitsujyou s
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN t_yoyakufeeuketsuke u
			USING (localgovcode, yoyakunum)
			WHERE y.localgovcode=? AND y.userid=? AND y.usedatefrom>=?";
		if ($yoyakuNum == '') {
			$sql.= "AND u.shisetsufee > (u.cash+u.chg+u.ticket+u.kouzafurikomi+u.others+u.jyutou) and u.shisetsufee <> 0";
		} else {
			$sql.= " AND y.yoyakunum='".$yoyakuNum."'";
		}

		$order = " ORDER BY usedate, usetimefrom, shisetsucode,
				shitsujyokbn, shitsujyocode, combino";

		$aWhere = array(_CITY_CODE_, $uid, date('Ymd'), _CITY_CODE_, $uid, date('Ymd'));

		return $this->con->getAll($sql.$order, $aWhere, DB_FETCHMODE_ASSOC);
	}

	function remake_data(&$dataset)
	{
		global $aPayKbn;

		$aShisetsu = $this->oSC->get_shisetsu_name_array();
		$aCombi = $this->oSC->get_combi_name_array();

		$i = 0;
		$res = array();
		foreach ($dataset as $val)
		{
			if (array_key_exists($val['yoyakunum'], $res)) {
				if ($val['shitsujyokbn'] == '3') {
					$res[$val['yoyakunum']]['shitsujyoname'] .= '<br>'.$val['shitsujyoname'];
				}
				continue;
			}
			$oRS = new receipt_status($this->con, $val['yoyakunum'], $val['honyoyakukbn'], $val['suuryo']);
			$receiptStatus = $oRS->getReceiptStatus();

			$val['PayKbnName'] = $aPayKbn[$receiptStatus];
			$val['useShowFee'] = number_format($val['suuryo'] - $val['syunougaku']);
			$val['unpaidFee'] = $val['suuryo'] - $val['syunougaku'];

			$val['ShisetsuName'] = $aShisetsu[$val['shisetsucode']];
			if ($val['combino'] != 0) {
				$val['shitsujyoname'] .= '&nbsp;'.$aCombi[$val['shisetsucode']][$val['shitsujyocode']][$val['combino']];
			}
			$val['UseDateView'] = $this->oSC->getDateView($val['usedate']);
			$val['UseTimeFromView'] = $this->oSC->getTimeView($val['usetimefrom']);
			$val['UseTimeToView'] = $this->oSC->getTimeView($val['usetimeto']);
			$val['key'] = $i;
			$res[$val['yoyakunum']] = $val;
			++$i;
		}
		unset($aShisetsu, $aCombi);
		return $res;
	}

	function update_db_info(&$req, $rec)
	{
		$oFB = new fee_base($this->con, $rec);
		$taxRate = $oFB->get_tax_rate();

		$this->con->autoCommit(false);

		$dataset = array();
		$dataset['useukeflg'] = '1';
		$dataset['honyoyakukbn'] = '02';
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		$where = "localgovcode='"._CITY_CODE_
			."' AND yoyakunum='".$req['YoyakuNum']."'";
		$rc = $this->oDB->update('t_yoyaku', $dataset, $where);
		if ($rc < 0) {
			$this->con->rollback();
			return false;
		}

		$sql = "SELECT * FROM t_yoyakufeeuketsuke WHERE localgovcode=? AND yoyakunum=?";
		$aWhere = array(_CITY_CODE_, $req['YoyakuNum']);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$dataset = array();
		$dataset['receptdate'] = date('Ymd');
		$dataset['uketime'] = date('His');
		$dataset['receptid'] = $_SESSION['userid'];
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];
		if ($row) {
			$dataset['jyutou'] = $row['jyutou'] + $req['KanpuFee'];
			$dataset['tax'] = $oFB->calc_tax_from_fee($dataset['jyutou'], $taxRate);
			$rc = $this->oDB->update('t_yoyakufeeuketsuke', $dataset, $where);
		} else {
			$sql = 'SELECT * FROM t_yoyakufeeshinsei WHERE localgovcode=? AND yoyakunum=?';
			$aWhere = array(_CITY_CODE_, $req['YoyakuNum']);
			$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
			$dataset['localgovcode'] = _CITY_CODE_;
			$dataset['shisetsucode'] = $res['shisetsucode'];
			$dataset['yoyakunum'] = $res['yoyakunum'];
			$dataset['receptnum'] = '01';
			$dataset['userid'] = $req['UserID'];
			$dataset['shisetsufee'] = $res['suuryo'];
			$dataset['jyutou'] = $req['KanpuFee'];
			$dataset['tax'] = $oFB->calc_tax_from_fee($req['KanpuFee'], $taxRate);
			$dataset['receptplace'] = $res['shisetsucode'];
			$rc = $this->oDB->insert('t_yoyakufeeuketsuke', $dataset);
		}
		if ($rc < 0) {
			$this->con->rollback();
			return false;
		}

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['yoyakunum'] = $req['srcYoyaku'];
		$dataset['uketsukeno'] = $this->get_next_uketsuke_no($req['srcYoyaku']);
		$dataset['status'] = 2;
		$dataset['fee'] = $req['KanpuFee'];
		$dataset['kinshucode'] = '06';
		$dataset['destyoyakunum'] = $req['YoyakuNum'];
		$dataset['cancelflg'] = 0;
		$dataset['kanpujyutoudate'] = date('Ymd');
		$dataset['receiptdatetime'] = date('Y-m-d H:i:s');
		$dataset['receiptstaffid'] = $_SESSION['userid'];
		$rc = $this->oDB->insert('t_yoyakukanpujyutou', $dataset);
		if ($rc < 0) {
			$this->con->rollback();
			return false;
		}
		$this->con->commit();

		$oLog = new log();
		$oLog->setLog($_SESSION['userid'].' Receipt');
		return true;
	}

	function get_next_uketsuke_no($num)
	{
		$sql = 'SELECT CASE WHEN MAX(uketsukeno) IS NULL THEN 1 ELSE MAX(uketsukeno)+1 END FROM t_yoyakukanpujyutou WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array(_CITY_CODE_, $num);
		return $this->con->getOne($sql, $aWhere);
	}
}
?>
