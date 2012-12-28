<?php
/*
 *  Copyright 2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  RSS 2.0 お知らせ出力
 *
 *  rss_infoAction.class.php
 */

class rss_infoAction extends Action
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		header('Content-type: text/xml;charset=utf-8'); 

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;

		$rss = $dom->createElement('rss');
		$rss->setAttribute('version', '2.0');
		$parnode = $dom->appendChild($rss);

		$channel = $dom->createElement('channel');
		$parnode = $parnode->appendChild($channel);

		$title = $dom->createElement('title', _SYSTEM_NAME_);
		$parnode->appendChild($title);
		$link = $dom->createElement('link', getTopUrl());
		$parnode->appendChild($link);
		$desc = $dom->createElement('description', _SYSTEM_NAME_.'からのお知らせ');
		$parnode->appendChild($desc);
		$lang = $dom->createElement('language', 'ja');
		$parnode->appendChild($lang);
		$generator = $dom->createElement('generator', 'OpenReaf');
		$parnode->appendChild($generator);
		$pub_date = $dom->createElement('pubDate', date('r'));
		$parnode->appendChild($pub_date);

		$sql = "SELECT m.upkikanfrom, m.title, m.memo, m.url,
			m.prioritykbn, m.upddate, m.updtime,
			m.shisetsucode, s.shisetsuname
			FROM t_potalmemo m
			LEFT JOIN m_shisetsu s USING (localgovcode, shisetsucode)
			WHERE m.localgovcode=? AND m.upkikanfrom<=? AND m.upkikanto>=?
			AND (m.disptermflg='0' OR m.disptermflg='2')
			ORDER BY prioritykbn DESC, upkikanfrom DESC, upddate DESC, updtime DESC";
		$recs = $this->con->getAll($sql, array(_CITY_CODE_, date('Ymd'), date('Ymd')), DB_FETCHMODE_ASSOC);

		foreach ($recs as $key => $val)
		{
			$update = strtotime($val['upddate'].$val['updtime']);
			$item_node = $dom->createElement('item');
			$item = $parnode->appendChild($item_node);
			$item_title = $dom->createElement('title', $val['title']);
			$item->appendChild($item_title);
			$item_link = $dom->createElement('link', getTopUrl().'?op=top&amp;time='.$update);
			$item->appendChild($item_link);
			$item_desc = $dom->createElement('description', strip_tags($val['memo']));
			$item->appendChild($item_desc);
			$tmp = $val['shisetsucode'] == '000' ? '全施設' : $val['shisetsuname'];
			$item_category = $dom->createElement('category', $tmp);
			$item->appendChild($item_category);
			$tmp = date('r', $update);
			$item_date = $dom->createElement('pubDate', $tmp);
			$item->appendChild($item_date);
		}

		echo $dom->saveXML();
	}
}
?>
