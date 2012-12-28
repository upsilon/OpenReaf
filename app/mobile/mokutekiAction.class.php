<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  mokutekiAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/facility.php';

class mokutekiAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);
	}

	function execute()
	{
		$this->check_login('mokuteki');

		$LocalGovCode = _CITY_CODE_;
		$ShisetsuCode = $_SESSION['Y_I']['shisetsucode'];
		$ShitsujyoCode = $_SESSION['Y_I']['shitsujyocode'];
		$CombiNo = $_SESSION['Y_I']['combino'];

		$FuzokuCodeArr = array();
		if (isset($_POST['chkClickfuzoku'])) {
			foreach ($_POST['chkClickfuzoku'] as $key => $val)
			{
				if ($val == 1) $FuzokuCodeArr[] = $_POST['FuzokuCode'][$key];
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
			$class_name = 'apply_confAction';
			require OPENREAF_ROOT_PATH.'/app/'.$this->type.'/'.$class_name.'.class.php';
			$oAction = new $class_name($this->type);
			$oAction->execute();
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
			$class_name = 'apply_confAction';
			require OPENREAF_ROOT_PATH.'/app/'.$this->type.'/'.$class_name.'.class.php';
			$oAction = new $class_name($this->type);
			$oAction->execute();
			exit();
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
}
?>
