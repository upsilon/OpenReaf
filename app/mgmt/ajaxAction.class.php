<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  AJAX用コマンド処理
 *
 *  ajaxAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class ajaxAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$cmd = get_request_var('cmd');

		header('Content-type: text/xml');

		$dom = new DOMDocument('1.0', 'UTF-8');

		$node = $dom->createElement('data');
		$parnode = $dom->appendChild($node);

		if ($cmd == 'zipcode') {
			$sql = "SELECT address FROM m_zip WHERE code=?";
			$res = $this->con->getOne($sql, array($_GET['code']));
			$error = '0';
			if ($res == '') {
				$res = '該当するデータがありません。';
				$error = '1';
			}
			$node = $dom->createElement('address', $res);
			$newnode = $parnode->appendChild($node);
			$newnode->setAttribute('error', $error);

		} elseif ($cmd == 'user') {
			$upd = isset($_GET['upd']) ? true : false;

			$oSC = new system_common($this->con);

			$res = $oSC->set_user_status($_GET['id'], $_GET['date'], $upd);
			$node = $dom->createElement('UserID', $res['userid']);
			$node1 = $parnode->appendChild($node);
			$node1->setAttribute('UseKbn', $res['usekbn']);
			$node = $dom->createElement('NameSei', $res['namesei']);
			$node1 = $parnode->appendChild($node);
			$node = $dom->createElement('NameSeiKana', $res['nameseikana']);
			$node1 = $parnode->appendChild($node);
			$node = $dom->createElement('Status', $res['userjyoutai']);
			$node1 = $parnode->appendChild($node);
			$node1->setAttribute('UserJyoutaiKbn', $res['userjyoutaikbn']);
			$node1->setAttribute('useCheckFlag', $res['usecheckflag']);
		}
		echo $dom->saveXML();
	}
}
?>
