<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者減免変更
 *
 *  usr_03_05_modAction.class.php
 *  usr_03_05.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';

class usr_03_05_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$para = array();

		$this->set_header_info();

		$uid = get_request_var('UserID');

		$oSC = new system_common($this->con);

		if (isset($_POST['updateBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				if ($this->update_genmen($_POST, $uid)) {
					$this->update_yoyaku_fee($uid, $_POST);
					$message = '正常に登録しました。';
				} else {
					$message = '登録できませんでした。';
				}
			}
		}
		$para = $oSC->get_user_data($uid);
		$gen = $this->get_user_genmen($uid);
		$para = array_merge($para, $gen);
		$aGenmen = $this->get_genmen_options();

		$this->oSmarty->assign('aGenmen', $aGenmen);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('UserID', $uid);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('op', 'usr_03_05_mod');
		$this->oSmarty->display('usr_03_05.tpl');
	}

	function check_input_data(&$req)
	{
		$msg = '';

		$appCheck = checkdate(intval($req['AppDayMonth']), intval($req['AppDayDay']), intval($req['AppDayYear']));
		if (!$appCheck) {
			$msg.= '適用開始年月日が正しくありません。&nbsp;';
			$msg.= $req['AppDayYear'].'年'.$req['AppDayMonth'].'月'.$req['AppDayDay'].'日\n';
		}
		$limitCheck = checkdate(intval($req['LimitDayMonth']), intval($req['LimitDayDay']), intval($req['LimitDayYear']));
		if (!$limitCheck) {
			$msg.= '有効期限年月日が正しくありません。&nbsp;';
			$msg.= $req['LimitDayYear'].'年'.$req['LimitDayMonth'].'月'.$req['LimitDayDay'].'日\n';
		}
		if ($appCheck && $limitCheck) {
			if (intval($req['AppDayYear'].$req['AppDayMonth'].$req['AppDayDay']) > intval($req['LimitDayYear'].$req['LimitDayMonth'].$req['LimitDayDay'])) {
				$msg.= '適用開始年月日が有効期限年月日を超えています。\n';
			}
			if (intval($req['AppDayYear'].$req['AppDayMonth'].$req['AppDayDay']) < intval(date('Ymd'))) {
				$msg.= '適用開始年月日は本日以降にしてください。\n';
			}
		}
		return $msg;
	}

	function get_user_genmen($uid)
	{
		$sql = "SELECT g.koteigenname, u.koteigencode, u.appday, u.limitday, u.keizokuflg
			FROM m_usrgenmen u
			JOIN m_genmen g USING (localgovcode, koteigencode)
			WHERE u.localgovcode=? AND u.userid=?";
		$aWhere = array(_CITY_CODE_, $uid);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if ($res) {
			$res['appday'] = strtotime($res['appday']);
			$res['limitday'] = strtotime($res['limitday']);
		} else {
			$res = array();
		}
		return $res;
	}

	function get_genmen_options()
	{
		$sql = "SELECT koteigencode, koteigenname";
		$sql.= " FROM m_genmen WHERE localgovcode=?";
		$sql.= " ORDER BY koteigencode";
		$aWhere = array(_CITY_CODE_);
		$rows = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($rows as $val)
		{
			$recs[$val[0]] = $val[1];
		}
		unset($rows);
		return $recs;
	}

	function update_genmen(&$req, $uid)
	{
		$sql = 'DELETE FROM m_usrgenmen';
		$sql.= ' WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		$this->con->query($sql, $aWhere);

		if ($req['KoteiGenCode'] == '') return true;

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['koteigencode'] = $req['KoteiGenCode'];
		$dataset['keizokuflg'] = $req['KeizokuFlg'];
		$dataset['appday'] = $req['AppDayYear'].$req['AppDayMonth'].$req['AppDayDay'];
		$dataset['limitday'] = $req['LimitDayYear'].$req['LimitDayMonth'].$req['LimitDayDay'];
		$dataset['userid'] = $uid;
		$dataset['upddate'] = date('Ymd');
		$dataset['updtime'] = date('His');
		$dataset['updid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('m_usrgenmen', $dataset);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	function update_yoyaku_fee($uid, &$req)
	{
		$nowDate = date('Ymd');
		$nowTime = date('His');
		$rate = 0;
		if ($req['KoteiGenCode'] != '') {
			$sql = "SELECT rate FROM m_genmen 
				WHERE localgovcode=? AND koteigencode=?";
			$rate = $this->con->getOne($sql, array(_CITY_CODE_, $req['KoteiGenCode']));

			if ($req['KeizokuFlg'] == '0') {
				$nowDate = $req['AppDayYear'].$req['AppDayMonth'].$req['AppDayDay'];
				$nowTime = "000000";
			}
		}

		$LimitDay = $req['LimitDayYear'].$req['LimitDayMonth'].$req['LimitDayDay'];

		//yoyaku
		$sql1 = "SELECT y.yoyakunum,y.localgovcode,y.shisetsucode,
			y.shitsujyocode,y.mencode,y.usedatefrom as usedate,y.usetimefrom,
			y.baseshisetsufee,f.basefee,t.shitsujyokbn,t.genapplyflg,s.fractionflg
			FROM t_yoyaku y 
			JOIN m_shitsujyou t
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN m_shisetsu s
			USING (localgovcode, shisetsucode)
			JOIN t_yoyakufeeshinsei f USING (localgovcode, yoyakunum)
			WHERE (SUBSTRING(f.suuryotani,1,1)='1' OR f.suuryotani='' OR f.suuryotani IS NULL) 
			AND y.userid=?";

		$whr1 = " AND (y.usedatefrom>? OR (y.usedatefrom=? AND y.usetimefrom>?))";
		$aWhere = array($uid, $nowDate, $nowDate, $nowTime);
		if ($req['KoteiGenCode'] != '') {
			$whr1.= " AND y.usedatefrom<=?";
			array_push($aWhere, $LimitDay);
		}
		$order = " ORDER BY yoyakunum, shitsujyokbn, shitsujyocode, mencode";
		$rst = $this->con->getAll($sql1.$whr1.$order, $aWhere, DB_FETCHMODE_ASSOC);
		$lastYoyakuNum = '';
		$genFee = 0;
		$nogenFee = 0;
		foreach ($rst as $row)
		{
			$oFB = new fee_base($this->con, $row);
			$taxRate = $oFB->get_tax_rate();

			if ($lastYoyakuNum != $row['yoyakunum']) {
				$lastYoyakuNum = $row['yoyakunum'];
				$genFee = 0;
				$nogenFee = 0;
			}
			$fee = $row['baseshisetsufee'];
			if (preg_match('/1/', $row['genapplyflg'])) {
				$fee = $oFB->calc_fee($row['baseshisetsufee'], 0, $rate, $row['fractionflg']);
			}
			$tax = $oFB->calc_tax($fee, $taxRate, 0);
			$fee = $oFB->calc_fee($fee, $taxRate, 0);
			$sql2 = "UPDATE t_yoyaku SET 
				upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid']."'
				,shisetsufee=".$fee.",shisetsutax=".$tax;
			$whr2 = " WHERE localgovcode='".$row['localgovcode']."' 
				AND shisetsucode='".$row['shisetsucode']."'
				AND shitsujyocode='".$row['shitsujyocode']."'
				AND mencode='".$row['mencode']."'
				AND yoyakunum='".$row['yoyakunum']."'";
			$this->con->query($sql2.$whr2);

			if ($row['shitsujyokbn'] == '3') {
				if (preg_match('/1/', $row['genapplyflg'])) {
					$genFee += $row['baseshisetsufee'];
				} else {
					$nogenFee += $row['baseshisetsufee'];
				}
			} else {
				if (preg_match('/1/', $row['genapplyflg'])) {
					$genFee = $row['baseshisetsufee'];
				} else {
					$nogenFee = $row['baseshisetsufee'];
				}
			}
			$fee = $oFB->calc_fee($genFee, 0, $rate, $row['fractionflg']) + $nogenFee;
			$tax = $oFB->calc_tax($fee, $taxRate, 0);
			$fee = $oFB->calc_fee($fee, $taxRate, 0);
			$sql2 = "UPDATE t_yoyakufeeshinsei SET 
				upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid'] . "'";
			if ($req['KoteiGenCode'] == '') {
				$sql2.= ",suuryo=".$fee.",suuryotani='',Tax=".$tax;
			} else {
				$sql2.= ",suuryo=".$fee.",suuryotani='1,".$req['KoteiGenCode']."',Tax=".$tax;
			}
			$whr2 = " WHERE localgovcode='".$row['localgovcode']."' 
				AND yoyakunum='".$row['yoyakunum']."'";
			$this->con->query($sql2.$whr2);
		}

		if ($req['KoteiGenCode'] != '') {
			$whr1 = " AND y.usedatefrom>?";
			$aWhere = array($uid, $LimitDay);

			$rst = $this->con->getAll($sql1.$whr1.$order, $aWhere, DB_FETCHMODE_ASSOC);
			foreach ($rst as $row)
			{
				$oFB = new fee_base($this->con, $row);
				$taxRate = $oFB->get_tax_rate();
				$fee = $oFB->calc_fee($row['baseshisetsufee'], $taxRate, 0);
				$tax = $oFB->calc_tax($row['baseshisetsufee'], $taxRate, 0);

				$sql2 = "UPDATE t_yoyaku SET 
					upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid'] . "'
					,shisetsufee=".$fee.",shisetsutax=".$tax;
				$whr2 = " WHERE localgovcode='".$row['localgovcode']."' 
					AND shisetsucode='".$row['shisetsucode']."'
					AND shitsujyocode='".$row['shitsujyocode']."'
					AND mencode='".$row['mencode']."'
					AND yoyakunum='".$row['yoyakunum']."'";
				$this->con->query($sql2.$whr2);

				$fee = $oFB->calc_fee($row['basefee'], $taxRate, 0);
				$tax = $oFB->calc_tax($row['basefee'], $taxRate, 0);
				$sql2 = "UPDATE t_yoyakufeeshinsei SET 
					upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid']."',suuryo=".$fee.",suuryotani='',tax=".$tax;
				$whr2 = " WHERE localgovcode='".$row['localgovcode']."' 
					AND yoyakunum='".$row['yoyakunum']."'";
				$this->con->query($sql2.$whr2);
			}
		}

		//pullout
		$sql1 = "SELECT y.pulloutyoyakunum,y.localgovcode,y.shisetsucode,
			y.shitsujyocode,y.mencode,y.usedate,y.usetimefrom,
			y.baseshisetsufee,f.basefee,t.shitsujyokbn,t.genapplyflg,s.fractionflg
			FROM t_pulloutyoyaku y 
			JOIN m_shitsujyou t
			USING (localgovcode, shisetsucode, shitsujyocode)
			JOIN m_shisetsu s
			USING (localgovcode, shisetsucode)
			JOIN t_yoyakufeeshinsei f
			ON y.localgovcode=f.localgovcode AND y.pulloutyoyakunum=f.yoyakunum
			WHERE (SUBSTRING(f.suuryotani,1,1)='1' OR f.suuryotani='' OR f.suuryotani IS NULL) 
			AND y.userid=?";

		$whr1 = " AND (y.usedate>? OR (y.usedate=? AND y.usetimefrom>?))";
		$aWhere = array($uid, $nowDate, $nowDate, $nowTime);
		if ($req['KoteiGenCode'] != '') {
			$whr1.= " AND y.usedate<=?";
			array_push($aWhere, $LimitDay);
		}
		$order = " ORDER BY pulloutyoyakunum, shitsujyokbn, shitsujyocode, mencode";
		$rst = $this->con->getAll($sql1.$whr1.$order, $aWhere, DB_FETCHMODE_ASSOC);
		$lastYoyakuNum = '';
		$genFee = 0;
		$nogenFee = 0;
		foreach ($rst as $row)
		{
			$oFB = new fee_base($this->con, $row);
			$taxRate = $oFB->get_tax_rate();

			if ($lastYoyakuNum != $row['pulloutyoyakunum']) {
				$lastYoyakuNum = $row['pulloutyoyakunum'];
				$genFee = 0;
				$nogenFee = 0;
			}
			$fee = $row['baseshisetsufee'];
			if (preg_match('/1/', $row['genapplyflg'])) {
				$fee = $oFB->calc_fee($row['baseshisetsufee'], 0, $rate, $row['fractionflg']);
			}
			$tax = $oFB->calc_tax($fee, $taxRate, 0);
			$fee = $oFB->calc_fee($fee, $taxRate, 0);
			$sql2 = "UPDATE t_pulloutyoyaku SET 
				upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid'] . "'
				,shisetsufee=".$fee.",shisetsutax=".$tax;
			$whr2 = " WHERE localgovcode='".$row['localgovcode']."'
				AND shisetsucode='".$row['shisetsucode']."'
				AND shitsujyocode='".$row['shitsujyocode']."'
				AND mencode='".$row['mencode']."'
				AND pulloutyoyakunum='".$row['pulloutyoyakunum']."'";
			$this->con->query($sql2.$whr2);

			if ($row['shitsujyokbn'] == '3') {
				if (preg_match('/1/', $row['genapplyflg'])) {
					$genFee += $row['baseshisetsufee'];
				} else {
					$nogenFee += $row['baseshisetsufee'];
				}
			} else {
				if (preg_match('/1/', $row['genapplyflg'])) {
					$genFee = $row['baseshisetsufee'];
				} else {
					$nogenFee = $row['baseshisetsufee'];
				}
			}
			$fee = $oFB->calc_fee($genFee, 0, $rate, $row['fractionflg']) + $nogenFee;
			$tax = $oFB->calc_tax($fee, $taxRate, 0);
			$fee = $oFB->calc_fee($fee, $taxRate, 0);
			$sql2 = "UPDATE t_yoyakufeeshinsei SET 
				upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid'] . "'";
			if ($req['KoteiGenCode'] == '') {
				$sql2.= ",suuryo=".$fee.",suuryotani='',tax=".$tax;
			} else {
				$sql2.= ",suuryo=".$fee.",suuryotani='1,".$req['KoteiGenCode']."',tax=".$tax;
			}
			$whr2 = " WHERE localgovcode='".$row['localgovcode']."'
				AND yoyakunum='".$row['pulloutyoyakunum']."'";
			$this->con->query($sql2.$whr2);
		}
		if ($req['KoteiGenCode'] != '') {
			$whr1 = " AND y.usedate>?";
			$aWhere = array($uid, $LimitDay);

			$rst = $this->con->getAll($sql1.$whr1.$order, $aWhere, DB_FETCHMODE_ASSOC);
			foreach ($rst as $row)
			{
				$oFB = new fee_base($this->con, $row);
				$taxRate = $oFB->get_tax_rate();
				$fee = $oFB->calc_fee($row['baseshisetsufee'], $taxRate, 0);
				$tax = $oFB->calc_tax($row['baseshisetsufee'], $taxRate, 0);
				$sql2 = "UPDATE t_pulloutyoyaku SET 
					upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid'] . "'
					,shisetsufee=".$fee.",shisetsutax=".$tax;
				$whr2 = " WHERE localgovcode='".$row['localgovcode']."'
					AND shisetsucode='".$row['shisetsucode']."'
					AND shitsujyocode='".$row['shitsujyocode']."'
					AND mencode='".$row['mencode']."'
					AND pulloutyoyakunum='".$row['pulloutyoyakunum']."'";
				$this->con->query($sql2.$whr2);

				$fee = $oFB->calc_fee($row['basefee'], $taxRate, 0);
				$tax = $oFB->calc_tax($row['basefee'], $taxRate, 0);
				$sql2 = "UPDATE t_yoyakufeeshinsei SET 
					upddate='".date("Ymd")."',updtime='".date("His")."',updid='".$_SESSION['userid']."',suuryo=".$fee.",suuryotani='',tax=".$tax;
				$whr2 = " WHERE localgovcode='".$row['localgovcode']."'
					AND yoyakunum='".$row['pulloutyoyakunum']."'";
				$this->con->query($sql2.$whr2);
			}
		}

		return true;
	}
}
?>
