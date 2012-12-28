<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  職員一覧
 *
 *  stf_01_01_topAction.class.php
 *  stf_01_01.tpl
 */

class stf_01_01_topAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$bushoCode = $this->oPrivilege->get_busho_options();

		$tourokuKbn = array(	"0"=>"",
					"1"=>"施設担当者",
					"2"=>"施設管理者",
					"3"=>"システム管理者"
					);

		// 一覧取得
		$sql = "SELECT staffid, staffnum, staffname, bushocode,
			tourokukbn, appdatefrom, haishidate
			FROM m_staff ORDER BY staffid asc";
		$res = $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);

		foreach ($res as $i => $row)
		{
			$res[$i]['bushocode'] = $bushoCode[$row['bushocode']];
			$res[$i]['tourokukbn'] = $tourokuKbn[$row['tourokukbn']];
			$res[$i]['DisableForP'] = 0;
			$res[$i]['DisableForH'] = 0;
				
			// 先に登録区分による判定
			if ($_SESSION['usertype'] < $row['tourokukbn']) {
				$res[$i]['DisableForP'] = 1;
			}
				
			// 廃止は優先的に
			if ($row['haishidate'] && $row['haishidate'] <= date('Ymd')) {
				$res[$i]['DisableForH'] = 1;
			}
		}

		$this->oSmarty->assign('results', $res);
		$this->oSmarty->display('stf_01_01.tpl');
	}
}
?>
