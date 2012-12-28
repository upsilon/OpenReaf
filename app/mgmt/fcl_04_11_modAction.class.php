<?php
/*
 *  Copyright 2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用目的設定
 *
 *  fcl_04_11_modAction.class.php
 *  fcl_04_11.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/facility.class.php';

class fcl_04_11_modAction extends adminAction
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
			if ($this->update_stj_purpose($req, $scd, $rcd)) {
				$message = '登録しました。';
			} else {
				$message = '登録できませんでした。';
			}
		}

		$aPurpose = $this->get_purpose_options($scd);
		$req['mokuteki'] = $this->get_stj_purpose_code($scd, $rcd);

		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('req', $req);
		$this->oSmarty->assign('aPurpose', $aPurpose);
		$this->oSmarty->assign('back_url', 'fcl_03_02_menu');
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('mode', $type);
		$this->oSmarty->assign('op', 'fcl_04_11_mod');
		$this->oSmarty->display('fcl_04_11.tpl');
	}

	function get_purpose_options($scd)
	{
		$aWhere = array(_CITY_CODE_, $scd);
		$sql = 'SELECT m.mokutekicode, m.mokutekiname';
		$sql.= ' FROM m_mokuteki m';
		$sql.= ' JOIN m_shisetsu s';
		$sql.= ' ON m.localgovcode=s.localgovcode AND m.mokutekidaicode=s.shisetsuclassdaicode';
		$sql.= ' WHERE s.localgovcode=? AND s.shisetsucode=?';
		$sql.= ' ORDER BY mokutekicode ASC';
		$rows = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($rows as $val) $recs[$val[0]] = $val[1];
		unset($rows);
		if (isset($recs['00'])) unset($recs['00']);
		return $recs;
	}

	function get_stj_purpose_code($scd, $rcd)
	{
		$sql = 'SELECT combino, mokutekicode';
		$sql.= ' FROM m_stjpurpose';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=?';
		$sql.= ' AND shitsujyocode=?';
		$sql.= ' ORDER BY combino, mokutekicode';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]][] = $val[1];
		unset($res);
		return $recs;
	}

	function update_stj_purpose(&$req, $scd, $rcd)
	{
		$sql = 'DELETE FROM m_stjpurpose';
		$sql.= ' WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array(_CITY_CODE_, $scd, $rcd);
		$this->con->query($sql, $aWhere);

		if (!isset($req['mokuteki'])) return true;

		$dataset = array();
		$dataset['localgovcode'] = _CITY_CODE_;
		$dataset['shisetsucode'] = $scd;
		$dataset['shitsujyocode'] = $rcd;
		$dataset['upddate'] = date('Ymd');
		$dataset['updid'] = $_SESSION['userid'];

		foreach ($req['mokuteki'] as $cno => $code)
		{
			foreach ($code as $val)
			{
				$dataset['combino'] = $cno;
				$dataset['mokutekicode'] = $val;
				$dataset['updtime'] = date('His');
				$rc = $this->oDB->insert('m_stjpurpose', $dataset);
				if ($rc < 0) return false;
			}
		}
		return true;
	}
}
?>
