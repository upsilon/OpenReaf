<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  付属室場選択
 *
 *  fcl_04_10_modAction.class.php
 *  fcl_04_10.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_10_modAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';

		$this->set_header_info();

		$req = $_POST;
		$scd = $req['scd'];
		$rcd = $req['rcd'];
		$type = $req['type'];

		$oFA = new facility($this->con);
		$rec = $oFA->get_shitsujyo_header($scd, $rcd);
		$recs = array(array('combiname' => '室場', 'combino' => 0));
		$res = $oFA->get_mencombination_data($scd, $rcd);
		$recs = array_merge($recs, $oFA->make_mencombination_list($res));

		if (isset($req['updateBtn'])) {
			if ($this->update_fuzoku($req, $scd, $rcd)) {
				$message = '登録しました。';
			} else {
				$message = '登録できませんでした。';
			}
		}

		$aFuzoku = $this->get_all_fuzoku($scd, $rcd);
		$req['fuzoku'] = $this->get_fuzoku_code($scd, $rcd);

		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('req', $req);
		$this->oSmarty->assign('aFuzoku', $aFuzoku);
		$this->oSmarty->assign('back_url', 'fcl_03_02_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_04_10_mod');
		$this->oSmarty->display('fcl_04_10.tpl');
	}

	function get_all_fuzoku($scd, $rcd)
	{
		$sql = "SELECT shitsujyocode, shitsujyoname, shitsujyoskbcode
			FROM m_shitsujyou
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyokbn='3'
			ORDER BY shitsujyoskbcode ASC";
		$aWhere = array(_CITY_CODE_, $scd);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	function get_fuzoku_code($scd, $rcd)
	{
		$sql = 'SELECT combino, fuzokucode
			FROM m_fuzokushitsujyou
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?
			ORDER BY combino, fuzokucode';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]][] = $val[1];
		unset($res);
		return $recs;
	}

	function update_fuzoku(&$req, $scd, $rcd)
	{
		$sql = 'DELETE FROM m_fuzokushitsujyou';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$this->con->query($sql, $aWhere);

		if (!isset($req['fuzoku'])) return true;

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['upddate'] = date('Ymd');
		$dataset['updid'] = $_SESSION['userid'];

		foreach ($req['fuzoku'] as $cno => $code)
		{
			foreach ($code as $val)
			{
				$dataset['combino'] = $cno;
				$dataset['fuzokucode'] = $val;
				$dataset['updtime'] = date('His');
				$rc = $this->oDB->insert('m_fuzokushitsujyou', $dataset);
				if ($rc < 0) return false;
			}
		}
		return true;
	}
}
?>
