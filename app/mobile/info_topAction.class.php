<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  info_topAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/info.php';

class info_topAction
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
		$sql = "SELECT m.tourokudate, m.tourokutime, m.seqno, m.title,
			m.upkikanfrom, m.prioritykbn, m.upddate, m.updtime,
			m.shisetsucode, s.shisetsuname
			FROM t_potalmemo m
			LEFT JOIN m_shisetsu s USING (localgovcode, shisetsucode)
			WHERE m.localgovcode=? AND DATE(m.upkikanfrom)<=DATE(NOW())
			AND DATE(m.upkikanto)>=DATE(NOW())
			AND (m.disptermflg='0' OR m.disptermflg='1')
			ORDER BY prioritykbn DESC, upkikanfrom DESC, upddate DESC, updtime DESC";
		$recs = $this->con->getAll($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);

		$this->oDB->disconnect();

		$this->oSmarty->assign('condition', OR_NEWS_LIST);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('MODE', 0);
		$this->oSmarty->display('info_top.tpl');
	}
}
?>
