<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  infoAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/info.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class infoAction
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
		$sql = "SELECT m.upkikanfrom, m.title, m.memo, m.url,
			m.prioritykbn, m.upddate, m.updtime,
			m.shisetsucode, s.shisetsuname
			FROM t_potalmemo m
			LEFT JOIN m_shisetsu s USING (localgovcode, shisetsucode)
			WHERE m.localgovcode=? AND m.upkikanfrom<=? AND m.upkikanto>=?
			AND (m.disptermflg='0' OR m.disptermflg='1')
			ORDER BY prioritykbn DESC, upkikanfrom DESC, upddate DESC, updtime DESC";
		$recs = $this->con->getAll($sql, array(_CITY_CODE_, date('Ymd'), date('Ymd')), DB_FETCHMODE_ASSOC);

		$oSC = new system_common($this->con);

		foreach ($recs as $key => $val)
		{
			$recs[$key]['UpdDate'] = $oSC->date4lang($val['upkikanfrom'], _LANGUAGE_);
		}

		$this->oSmarty->assign('condition', OR_NEWS);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('MODE', 3);
		$this->oSmarty->display('info.tpl');
	}
}
?>
