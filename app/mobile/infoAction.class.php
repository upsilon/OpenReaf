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
		$sql = 'SELECT m.upkikanfrom, m.title, m.memo, m.url,';
		$sql.= ' m.shisetsucode, s.shisetsuname';
		$sql.= ' FROM t_potalmemo m';
		$sql.= ' LEFT JOIN m_shisetsu s USING (localgovcode, shisetsucode)';
		$sql.= ' WHERE m.localgovcode=? AND m.shisetsucode=?';
		$sql.= ' AND m.tourokudate=? AND m.tourokutime=? AND m.seqno=?';
		$aWhere = array(_CITY_CODE_, $_REQUEST['sCode'], $_REQUEST['tDate'], $_REQUEST['tTime'], $_REQUEST['seq']);
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$oSC = new system_common($this->con);

		$rec['UpdDate'] = $oSC->date4lang($rec['upkikanfrom'], _LANGUAGE_);

		$this->oDB->disconnect();

		$this->oSmarty->assign('condition', OR_NEWS);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('MODE', 0);
		$this->oSmarty->assign('BACK_LINK', '?op=info_top');
		$this->oSmarty->display('info.tpl');
	}
}
?>
