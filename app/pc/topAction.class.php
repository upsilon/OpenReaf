<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  topAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/define/language/'._LANGUAGE_.'/top.php';

class topAction extends Action
{
	function __construct($type)
	{
		parent::__construct($type);

		$this->end_session();
	}

	function execute()
	{
		$sql = 'SELECT shisetsuclassscreenflg, sitecloseflg, siteclosemessage, siteclosefrom, sitecloseto, topmenuurl FROM m_systemparameter WHERE localgovcode=?';
		$row = $this->con->getRow($sql, array(_CITY_CODE_), DB_FETCHMODE_ASSOC);

		$ShisetsuSentaku = 'shisetsu';
		if ($row['shisetsuclassscreenflg'] == 1) {
			$ShisetsuSentaku = 'shisetsuclass';
		}

		$sql = "SELECT m.title, m.prioritykbn, m.upkikanfrom, m.seqno,
			m.shisetsucode, s.shisetsuname
			FROM t_potalmemo m
			LEFT JOIN m_shisetsu s USING (localgovcode, shisetsucode)
			WHERE m.localgovcode=? AND m.upkikanfrom<=? AND m.upkikanto>=?
			AND (m.haishidate='' OR m.haishidate IS NULL)
			AND (m.disptermflg='0' OR m.disptermflg='1')
			ORDER BY prioritykbn DESC, upkikanfrom DESC, seqno DESC";
		$recs = $this->con->getAll($sql, array(_CITY_CODE_, date('Ymd'), date('Ymd')), DB_FETCHMODE_ASSOC);

		$template = 'top.tpl';
		$SiteCloseMessage ='';
		if ($row['sitecloseflg'] == '1') {
			$SiteCloseMessage = $row['siteclosemessage'];
			$template = 'close.tpl';
		} elseif ($row['sitecloseflg'] == '2') {
			if ($row['siteclosefrom'] <= time() && time() <= $row['sitecloseto']) {
				$SiteCloseMessage = $row['siteclosemessage'];
				$template = 'close.tpl';
			}
		}

		$protocol = _USE_SSL_ ? 'https' : 'http';
		$NextUrl = getTopUrl($protocol);

		$this->oSmarty->assign('condition', OR_TOP_MENU);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('ShisetsuSentaku', $ShisetsuSentaku);
		$this->oSmarty->assign('SiteCloseMessage', $SiteCloseMessage);
		$this->oSmarty->assign('NEXT_URL', $NextUrl);
		$this->oSmarty->display($template);
	}
}
?>
