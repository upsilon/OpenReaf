<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  guideAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/guide.php';

class guideAction
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
		$sql = "SELECT * FROM m_shisetsu";
		$sql.= " WHERE showguideflg = '1' AND appdatefrom <= ?";
		$sql.= " AND (haishidate='' OR haishidate is null OR haishidate > ?)";
		$sql.= " ORDER BY shisetsuskbcode, shisetsucode";
		$recs = $this->con->getAll($sql, array(date('Ymd'), date('Ymd')), DB_FETCHMODE_ASSOC);
		$event_button = 0;
		foreach ($recs as $val)
		{
			if ($val['showeventflg'] == '1') {
				$event_button = 1;
				break;
			}
		}

		$this->oSmarty->assign('condition', OR_FACILITY_GUIDE);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('MODE', 3);
		$this->oSmarty->assign('event_button', $event_button);
		if ($event_button == 1) {
			$this->oSmarty->assign('UseYM', date('Ym'));
		}
		$this->oSmarty->display('guide.tpl');
	}
}
?>
