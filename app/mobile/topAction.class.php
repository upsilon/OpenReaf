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

		$template = 'top.tpl';
		$SiteCloseMessage ='';
		if ($row['sitecloseflg'] == '1') {
			$SiteCloseMessage = strip_tags($row['siteclosemessage'], '<br>');
			$template = 'close.tpl';
		} elseif ($row['sitecloseflg'] == '2') {
			if ($row['siteclosefrom'] <= time() && time() <= $row['sitecloseto']) {
				$SiteCloseMessage = strip_tags($row['siteclosemessage'], '<br>');
				$template = 'close.tpl';
			}
		}
		$KensakuJyouken = 'jyouken';

		$protocol = _USE_SSL_ ? 'https' : 'http';
		$NextUrl = getTopUrl($protocol);
		$TopMenuUrl = _MULTILINGUAL_ ? $row['topmenuurl'] : '';

		$this->oSmarty->assign('condition', OR_TOP_MENU);
		$this->oSmarty->assign('ShisetsuSentaku', $ShisetsuSentaku);
		$this->oSmarty->assign('KensakuJyouken', $KensakuJyouken);
		$this->oSmarty->assign('SiteCloseMessage', $SiteCloseMessage);
		$this->oSmarty->assign('TopMenuURL', $TopMenuUrl);
		$this->oSmarty->assign('NEXT_URL', $NextUrl);
		$this->oSmarty->display($template);
	}
}
?>
