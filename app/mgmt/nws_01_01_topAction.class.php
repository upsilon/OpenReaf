<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  nws_01_01_topAction.class.php
 *  nws_01_01.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class nws_01_01_topAction extends adminAction
{
	private $err = array();
	private $PageRow = 5;
	private $aPriority = array('普通', '高い', '重要');
	private $aDispTerm = array('制限なし', 'トップページ', 'RSS');

	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$message = '';
		$success = 0;
		$para = array();
		$PrimaryKey = '';

		$this->set_header_info();

		$oSC = new system_common($this->con);

		$para['dateFrom'] = time();
		$para['dateTo'] = time();

		if (isset($_POST['insertBtn'])) {
			$message = $this->check_input_data($_POST);
			if ($message == '') {
				$dataset = $this->oDB->make_base_dataset($_POST, 't_potalmemo');
				$dataset['memo'] = htmlspecialchars_decode($dataset['memo'], ENT_QUOTES);
				$dataset['upkikanfrom'] = date('Ymd', strtotime($_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay']));
				$dataset['upkikanto'] = date('Ymd', strtotime($_POST['ToYear'].$_POST['ToMonth'].$_POST['ToDay']));
				$dataset['upddate'] = date('Ymd');
				$dataset['updtime'] = date('His');
				$dataset['updid'] = $_SESSION['userid'];

				$rc = 0;
				if (empty($_POST['PrimaryKey'])) {
					$sql = 'SELECT MAX(seqno) FROM t_potalmemo
						WHERE localgovcode=? AND shisetsucode=? AND DATE(tourokudate)=DATE(NOW())';
					$aWhere = array(_CITY_CODE_, $dataset['shisetsucode']);
					$curNo = $this->con->getOne($sql, $aWhere);
					$dataset['seqno'] = sprintf('%02d', intval($curNo) + 1);
					$dataset['localgovcode'] = _CITY_CODE_;
					$dataset['tourokudate'] = date('Ymd');
					$dataset['tourokutime'] = date('His');
					$dataset['staffid'] = $_SESSION['userid'];
					$rc = $this->oDB->insert('t_potalmemo', $dataset);
				} else {
					$pkey = explode('|', $_POST['PrimaryKey']);
					$where = "localgovcode='".$pkey[0]
						."' AND shisetsucode='".$pkey[1]
						."' AND tourokudate='".$pkey[2]
						."' AND seqno='".$pkey[3]."'";
					$rc = $this->oDB->update('t_potalmemo', $dataset, $where);
				}
				if ($rc < 0) {
					$message = '登録できませんでした。';
					$success = -1;
				} else {
					$message = '正常に登録しました。';
					$success = 1;
				}
			} else {
				$success = -1;
			}
			if ($success < 0) {
				$para = $_POST;
				$para['dateFrom'] = strtotime($_POST['FromYear'].$_POST['FromMonth'].$_POST['FromDay']);
				$para['dateTo'] = strtotime($_POST['ToYear'].$_POST['ToMonth'].$_POST['ToDay']);
			}
		} elseif (isset($_POST['deleteBtn'])) {
			if (isset($_POST['pAryKey'])) {
				$sql = 'DELETE FROM t_potalmemo WHERE localgovcode=? AND shisetsucode=? AND tourokudate=? AND seqno=?';
				foreach ($_POST['pAryKey'] as $val)
				{
					$pkey = explode('@', $val);
					$aWhere = array(_CITY_CODE_, $pkey[0], $pkey[1], $pkey[2]);
					$this->con->query($sql, $aWhere);
				}

			} else {
				$message = 'お知らせが選択されていません。';
				$success = 1;
			}
		} elseif (isset($_POST['editBtn'])) {
			if (isset($_POST['pAryKey'])) {
				list($scd, $tcd, $seq) = explode('@', $_POST['pAryKey'][0]);
				$sql = "SELECT * FROM t_potalmemo WHERE localgovcode=? AND shisetsucode=? AND tourokudate=? AND seqno=?";
				$aWhere = array(_CITY_CODE_, $scd, $tcd, $seq);
				$para = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
				$para['dateFrom'] = strtotime($para['upkikanfrom']);
				$para['dateTo'] = strtotime($para['upkikanto']);
				$PrimaryKey = $para['localgovcode'].'|'.$para['shisetsucode'].'|'.$para['tourokudate'].'|'.$para['seqno'];
			} else {
				$message = 'お知らせが選択されていません。';
				$success = 1;
			}
		}

		$PageNo = isset($_GET['PageNo']) ? intval($_GET['PageNo']):0;
		$Offset = $PageNo * $this->PageRow;

		$sql = "SELECT m.*, s.staffname FROM t_potalmemo m
			JOIN m_staff s
			ON s.staffid = m.staffid AND s.localgovcode = m.localgovcode
			WHERE m.localgovcode = ?
			AND DATE(s.appdatefrom)<=DATE(NOW())
			AND (s.haishidate>? OR s.haishidate IS NULL OR s.haishidate='')
			ORDER BY upkikanfrom DESC, upddate DESC, updtime DESC";
		$aWhere = array(_CITY_CODE_, date('Ymd'));
		$res = $this->con->getAll($sql, $aWhere);
		$total = count($res);
		$PageNoPrev = $PageNo - 1;
		$PageNoNext = $PageNo + 1;
		if ($total <= ($PageNoNext * $this->PageRow)) $PageNoNext = -1;

		$sql.= " LIMIT ? OFFSET ?";
		array_push($aWhere, $this->PageRow, $Offset);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		foreach ($res as $key => $val)
		{
			$res[$key]['PriorityName'] = $this->aPriority[$val['prioritykbn']];
			$res[$key]['DispTermName'] = $this->aDispTerm[$val['disptermflg']];
			$res[$key]['TourokuDateView'] = $oSC->getDateView($val['tourokudate'], false);
			$res[$key]['TourokuTimeView'] = $oSC->getTimeView($val['tourokutime']);
			$res[$key]['UpKikanFromView'] = $oSC->getDateView($val['upkikanfrom']);
			$res[$key]['UpKikanToView'] = $oSC->getDateView($val['upkikanto']);
		}

		$aShisetsu = $this->oPrivilege->get_shisetsu_list();

		$this->oSmarty->assign('res', $res);
		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('err', $this->get_error());
		$this->oSmarty->assign('aShisetsu', $aShisetsu);
		$this->oSmarty->assign('aDispTerm', $this->aDispTerm);
		$this->oSmarty->assign('aPriority', $this->aPriority);
		$this->oSmarty->assign('PrimaryKey', $PrimaryKey);
		$this->oSmarty->assign('PageRowLimit', $this->PageRow);
		$this->oSmarty->assign('PageNo', $PageNo);
		$this->oSmarty->assign('PageNoPrev', $PageNoPrev);
		$this->oSmarty->assign('PageNoNext', $PageNoNext);
		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('success', $success);
		$this->oSmarty->assign('op', 'nws_01_01_top');
		$this->oSmarty->display('nws_01_01.tpl');
	}

	function get_error()
	{
		return $this->err;
	}

	function check_input_data(&$req)
	{
		$msg = '';

		if (strlen($req['title']) < 1) {
			$msg .= '見出しを入力してください。<br>';
			$this->err['Title'] = 'class="error"';
		} elseif (mb_strlen($req['title'], 'UTF-8') > 100) {
			$msg .= '見出しは100文字以内でを入力してください。<br>';
			$this->err['Title'] = 'class="error"';
		}
		if (strlen($req['memo']) > 1120) {
			$msg .= '内容は1120文字以内でを入力してください。<br>';
			$this->err['Memo'] = 'class="error"';
		}
		return $msg;
	}
}
?>
