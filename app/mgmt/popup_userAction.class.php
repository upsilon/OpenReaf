<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  popup_userAction.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class popup_userAction extends adminAction
{
	private $searchLimit = 5000;

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$recs = array();

		if (isset($_POST['searchBtn'])) {
			$recs = $this->get_db_info($_POST);
			$num = count($recs);
			if ($num > $this->searchLimit) {
				$message = '検索結果が'.number_format($num).'件ありました。<br>表示できないため'.number_format($this->searchLimit).'件以下になるように検索条件を絞り込んでください。';
				$recs = array();
			}
		}

		$oSC = new system_common($this->con);
		$aMokuteki = $oSC->get_purpose_name_array();
		$aKbn = $oSC->get_codename_options('KojinDanKbn');

		$this->oSmarty->assign('aMokuteki', $aMokuteki);
		$this->oSmarty->assign('aKbn', $aKbn);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->display('co_user.tpl');
	}

	function get_db_info(&$p)
	{
		$sql = 'SELECT u.userid,
			u.userjyoutaikbn, u.namesei, u.nameseikana,
			u.userlimit, u.stoperasedate, u.yoyakukyokaflg
			FROM m_user u';

		$orderBy = ' ORDER BY userid';

		$where = " WHERE u.localgovcode='"._CITY_CODE_."' ";
		$where.= "AND u.userjyoutaikbn<>'0' AND u.userjyoutaikbn<>'4' ";

		//利用者ID
		if (strlen($p['UserID']) > 0) {
			if (isset($p['PartialMatchFlg'])) {
				$where.= "AND u.userid LIKE '{$p['UserID']}%' ";
			} else {
				$where.= "AND u.userid = '{$p['UserID']}' ";
			}
		}
		//登録区分
		if (strlen($p['KojinDanKbn'])>0) {
			$where.= "AND u.kojindankbn = '{$p['KojinDanKbn']}' ";
		}
		//氏名
		if (strlen($p['Name']) > 0) {
			$where.= "AND (u.namesei LIKE '%".$p['Name']."%' ";
			$where.= "OR u.nameseikana LIKE '%".$p['Name']."%') ";
		}
		//住所
		if (strlen($p['Address']) > 0) {
			$where.= "AND (u.adr1 LIKE '%{$p['Address']}%' ";
			$where.= "OR u.adr2 LIKE '%{$p['Address']}%') ";
		}
		//電話番号
		if (strlen($p['TelNo1']) > 0 || strlen($p['TelNo2']) > 0 || strlen($p['TelNo3']) > 0) {
			$where.= "AND (u.telno11 LIKE '%".$p['TelNo1']."%' OR u.telno12 LIKE '%".$p['TelNo2']."%' OR u.telno13 LIKE '%".$p['TelNo3']."%' OR u.telno21 LIKE '%".$p['TelNo1']."%' OR u.telno22 LIKE '%".$p['TelNo2']."%' OR u.telno23 LIKE '%".$p['TelNo3']."%') ";
		}
		// 利用目的
		if (intval($p['MokutekiCode']) > 0) {
			$where.= "AND u.purpose LIKE '%{$p['MokutekiCode']}%' " ;
		}
		$sql.= ' '.$where . $orderBy;
		return $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	}
}
?>
