<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  mokutekiAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/side_menu.php';

class mokutekiAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$this->check_login('mokuteki');

		$BTN_MAX = 12;
		$PageNO = isset($_GET['page_no']) ? intval($_GET['page_no']):0;
		$Offset = $PageNO * $BTN_MAX;

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$CombiNo = $_SESSION['Y_I']['combino'];

		$FuzokuCodeArr = array();
		if (isset($_POST['chkClickfuzoku'])) {
			foreach ($_POST['chkClickfuzoku'] as $key => $val)
			{
				if ($val == 1) {
					$FuzokuCodeArr[] = $_POST['FuzokuCode'][$key];
				}
			}
		} elseif (isset($_SESSION['Y_I']['Fuzoku'])) {
			$FuzokuCodeArr = $_SESSION['Y_I']['Fuzoku'];
		}
		$_SESSION['Y_I']['Fuzoku'] = $FuzokuCodeArr;
		$_SESSION['M_I']['Fuzoku'] = $this->get_fuzoku_name($LocalGovCode, $ShisetsuCode, $FuzokuCodeArr);

		if (isset($_SESSION['Y_I']['mokutekicode'])) {
			unset($_SESSION['Y_I']['mokutekicode']);
		}

		if (isset($_REQUEST['MokutekiCode'])) {
			$_SESSION['Y_I']['mokutekicode'] = $_REQUEST['MokutekiCode'];
			$_SESSION['M_I']['MokutekiName'] = $this->get_purpose_name($LocalGovCode, $_REQUEST['MokutekiCode']);
			header('Location:index.php?op=apply_conf');
			
			exit();
		}

		$sql = "SELECT DISTINCT m.mokutekicode, m.mokutekiname, m.mokutekiskbcode";
		$sql.= " FROM m_mokuteki m";
		$sql.= " JOIN m_stjpurpose p USING (localgovcode, mokutekicode)";
		$sql.= " WHERE p.localgovcode=? AND p.shisetsucode=? AND p.shitsujyocode=?";
		$sql.= " AND (p.combino=? OR p.combino=0) AND m.delflg<>'1'";
		$sql.= " ORDER BY mokutekiskbcode, mokutekicode";
		$aWhere = array($LocalGovCode, $ShisetsuCode, $ShitsujyoCode, $CombiNo);
		$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$total = count($recs);

		if (isset($_SESSION['skip_pps'])) {
			unset($_SESSION['skip_pps']);
		}
		if ($total == 1) {
			$_SESSION['skip_pps'] = $recs[0]['mokutekicode'];
			$_SESSION['Y_I']['mokutekicode'] = $recs[0]['mokutekicode'];
			$_SESSION['M_I']['MokutekiName'] = $recs[0]['mokutekiname'];
			header('Location:index.php?op=apply_conf');
			exit();
		}

		if ($BTN_MAX < $total) {
			$sql.= " LIMIT ? OFFSET ?";
			array_push($aWhere, $BTN_MAX, $Offset);
			$recs = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		}

		$max_page = ceil($total / $BTN_MAX);

		$BtnNextFG = 0;
		if (($PageNO + 1) < $max_page) {
			$BtnNextFG = 1;
		}

		$message = OR_CHOOSE_A_PURPOSE;
		$condition = OR_PURPOSE_CHOICE.' :: 【'.$_SESSION['UNAME'].'】';

		$BackLink = '?op=daily';
		if (count($_SESSION['Y_I']['Fuzoku']) > 0) {
			$BackLink = '?op=fuzoku';
		}

		$this->oSmarty->assign('condition', $condition);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('screenflg', $_SESSION['screenflg']);
		$this->oSmarty->assign('ShisetsuClassName', $_SESSION['M_I']['ShisetsuClassName']);
		$this->oSmarty->assign('ShisetsuName', $_SESSION['M_I']['ShisetsuName']);
		$this->oSmarty->assign('ShitsujyoName', $_SESSION['M_I']['ShitsujyoName']);
		$this->oSmarty->assign('CombiName', $CombiNo == 0 ? '-' : $_SESSION['M_I']['CombiName']);
		$this->oSmarty->assign('CombiNo', $CombiNo);
		$this->oSmarty->assign('UseDateDisp', $_SESSION['M_I']['UseDateDisp']);
		$this->oSmarty->assign('UseTime', $_SESSION['M_I']['UseTime']);
		$this->oSmarty->assign('FuzokuName', $_SESSION['M_I']['Fuzoku']);
		$this->oSmarty->assign('page_no', $PageNO);
		$this->oSmarty->assign('next_fg', $BtnNextFG);
		$this->oSmarty->assign('MODE', 1);
		$this->oSmarty->assign('BACK_LINK', $BackLink);
		$this->oSmarty->assign('TOP_LINK', getTopUrl());
		$this->oSmarty->display('mokuteki.tpl');
	}

	function get_fuzoku_name($LocalGovCode, $ShisetsuCode, $FuzokuCode)
	{
		$fuzokuArr = array();
		if (count($FuzokuCode) != 0) {
			$sql = "SELECT shitsujyoname From m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND (shitsujyocode='". implode("' OR shitsujyocode='", $FuzokuCode) ."')";
			$res = $this->con->getAll($sql, array($LocalGovCode, $ShisetsuCode), DB_FETCHMODE_ASSOC);
			foreach ($res as $val)
			{
				$fuzokuArr[] = $val['shitsujyoname'];
			}
			unset($res);
		}
		return $fuzokuArr;
	}

	function get_purpose_name($LocalGovCode, $MokutekiCode)
	{
		$sql = "SELECT mokutekiname FROM m_mokuteki";
		$sql.= " WHERE localgovcode=? AND mokutekicode=?";
		$aWhere = array($LocalGovCode, $MokutekiCode);
		return $this->con->getOne($sql, $aWhere);
	}
}
?>
