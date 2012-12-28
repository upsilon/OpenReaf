<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  還付・充当
 *
 *  rsv_02_06_exemptionAction.class.php
 *  rsv_02_06.tpl
 */
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/receipt_status.class.php';
require OPENREAF_ROOT_PATH.'/app/class/fee_base.class.php';

class rsv_02_06_exemptionAction extends adminAction
{
	private $oSC = null;

	function __construct()
	{
		parent::__construct();
		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		global $aKinshu;

		$message = '';

		$this->set_header_info();

		$YoyakuNum = '';
		if (isset($_GET['YoyakuNum'])) {
			$YoyakuNum = $_GET['YoyakuNum'];
		} elseif (isset($_POST['srcYoyaku'])) {
			$YoyakuNum = $_POST['srcYoyaku'];
		}
		$cancel_flag = isset($_REQUEST['delFlg']) ? true : false;

		$mode = isset($_POST['mode']) ? intval($_POST['mode']) : 0;
		switch ($mode) {
			case 1:
			case 2:
				$message = $this->validate($_POST);
				if ($message == '') {
					$message = "正常に受け付けました。\n";
					if (!$this->insert_kanpujyutou($_POST)) {
						$message = "受け付けできませんでした。\n";
					}
				}
				break;
			case 3:
				$this->insert_fee_kannou_status($YoyakuNum);
				$message = "正常に更新しました。\n";
				break;
			case 4:
			case 5:
				$message = "正常に受け付けました。\n";
				if ($this->update_kanpujyutou($YoyakuNum, $_POST['UketsukeNo'])) {
					$this->delete_fee_kannou_status($YoyakuNum);
				} else {
					$message = "受け付けできませんでした。\n";
				}
				break;
			case 6:
				$this->delete_fee_kannou_status($YoyakuNum);
				$message = "正常に更新しました。\n";
				break;
		}

		$rec = $this->get_reserve_data($YoyakuNum, $cancel_flag);

		$kannouStatusFlg = 0;
		if ($this->check_kannou($YoyakuNum)) $kannouStatusFlg = 1;

		$aKanpu = $this->get_kanpujyutou_record($YoyakuNum);
		$akinshu = $aKinshu;
		unset($akinshu['06']);

		$this->oSmarty->assign('message', $message);
		$this->oSmarty->assign('rec', $rec);
		$this->oSmarty->assign('req', $_REQUEST);
		$this->oSmarty->assign('kannouStatusFlg', $kannouStatusFlg);
		$this->oSmarty->assign('aKanpu', $aKanpu);
		$this->oSmarty->assign('aKinshu', $akinshu);
		$this->oSmarty->assign('returnUrl', 'index.php?op=rsv_01_01_search&back=1');
		$this->oSmarty->display('rsv_02_06.tpl');
	}

	function get_reserve_data($YoyakuNum, $cancel=false)
	{
		global $aPayKbn;

		$rec = array();

		$yoyakutable = 't_yoyaku';
		$feetable = 't_yoyakufeeshinsei';
		if ($cancel) {
			$yoyakutable = 'h_yoyaku';
			$feetable = 'h_fee';
		}
		$sql = 'SELECT f.localgovcode, f.usedate,
			f.basefee, f.shisetsufee, f.suuryo,
			s.fractionflg, u.userid
			FROM '.$feetable.' f
			JOIN m_shisetsu s USING (localgovcode, shisetsucode)
			JOIN t_yoyakufeeuketsuke u USING (localgovcode, yoyakunum)
			WHERE u.localgovcode=? AND u.yoyakunum=?';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$rec = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$rec['YoyakuNum'] = $YoyakuNum;
		$rec['BaseShisetsuFeeView'] = number_format($rec['basefee']);
		$rec['ShisetsuFeeView'] = number_format($rec['shisetsufee']);
		$rec['SumFee'] = number_format($rec['suuryo']);

		// 収納状態と収納合計額を取得
		$oRS = new receipt_status($this->con, $YoyakuNum, '02', $rec['suuryo']);
		list($ReceiptStatus, $rec['ReceiptFee'])
						= $oRS->getReceiptStatus($cancel, true);
		$rec['ReceiptStatus'] = $aPayKbn[$ReceiptStatus];
		$rec['ReceiptSumFeeView'] = number_format($rec['ReceiptFee']);

		$rec['KanpuFee'] = $rec['ReceiptFee'] - $rec['suuryo'];
		$KanpuRate = 100;
		if ($cancel) {
			$sql = 'SELECT c.rate FROM m_canceljiyucode c
				JOIN '.$yoyakutable.' y ON c.cancelcode=y.canceljiyucode
				WHERE y.localgovcode=? AND y.yoyakunum=?';
			$rate = $this->con->getOne($sql, $aWhere);
			$KanpuRate = $rate == '' ? 100 : intval($rate);
			$oFB = new fee_base($this->con, $rec);
			$rec['KanpuFee'] += $rec['suuryo'] - $oFB->calc_fee($rec['suuryo'], 0, $KanpuRate, $rec['fractionflg']);
		}
		$rec['KanpuRate'] = $KanpuRate;
		$rec['KanpuFeeView'] = number_format($rec['KanpuFee']);
		return $rec;
	}

	//------------------------------------------------------------
	// 還付・充当履歴を取得
	//------------------------------------------------------------
	function get_kanpujyutou_record($YoyakuNum)
	{
		$aStaff = $this->oSC->get_staff_name_array();

		$sql = 'SELECT * FROM t_yoyakukanpujyutou WHERE localgovcode=? AND yoyakunum=? ORDER BY uketsukeno';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$recs = $this->con->getAll($sql,$aWhere,DB_FETCHMODE_ASSOC);

		foreach($recs as $key => $val)
		{
			$recs[$key]['FeeView'] = number_format($val['fee']);
			$aDate = explode('-', $val['kanpujyutoudate']);
			$recs[$key]['KanpuJyutouDateView'] = $this->oSC->getDateView($aDate[0].$aDate[1].$aDate[2]);
			$recs[$key]['ReceiptStaffName'] = isset($aStaff[$val['receiptstaffid']]) ? $aStaff[$val['receiptstaffid']] : $val['receiptstaffid'];
			$recs[$key]['CancelStaffName'] = isset($aStaff[$val['cancelstaffid']]) ? $aStaff[$val['cancelstaffid']] : $val['cancelstaffid'];
		}
		return $recs;
	}

	//------------------------------------------------------------
	// データのチェック
	//------------------------------------------------------------
	function validate(&$req)
	{
		$msg = '';
		if (!is_numeric($req['KanpuFee'])) {
			$msg = "還付・充当額は半角数字で入力してください。\n";
		}
		if (!checkdate($req['RegMonth'],$req['RegDay'],$req['RegYear'])) {
			$msg.= "還付・充当日の指定が不適切です。\n";
		}
		return $msg;
	}

	//------------------------------------------------------------
	// 還付・充当取消
	//------------------------------------------------------------
	function update_kanpujyutou($YoyakuNum, $UketsukeNo)
	{
		$aData = array();
		$aData['cancelflg'] = 1;
		$aData['canceldatetime'] = date('Y-m-d H:i:s');
		$aData['cancelstaffid'] = $_SESSION['userid'];

		$where = "localgovcode='"._CITY_CODE_
			."' AND yoyakunum='".$YoyakuNum
			."' AND uketsukeno='".$UketsukeNo."'";
		$rc = $this->oDB->update('t_yoyakukanpujyutou', $aData, $where);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	//------------------------------------------------------------
	// 完納ステータステーブルにインサート
	//------------------------------------------------------------
	function insert_fee_kannou_status($YoyakuNum)
	{
		$aData = array();
		$aData['localgovcode'] = _CITY_CODE_;
		$aData['yoyakunum'] = $YoyakuNum;
		$aData['upddatetime'] = date('Y-m-d H:i:s');
		$aData['updid'] = $_SESSION['userid'];
		$this->oDB->insert('t_yoyakufeekannoustatus', $aData);
	}

	//------------------------------------------------------------
	// 完納ステータステーブル削除
	//------------------------------------------------------------
	function delete_fee_kannou_status($YoyakuNum)
	{
		$sql = 'DELETE FROM t_yoyakufeekannoustatus
			WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$this->con->query($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 予約還付・充当テーブルに登録する
	//------------------------------------------------------------
	function insert_kanpujyutou(&$req)
	{
		$aData = array();
		$aData['localgovcode'] = _CITY_CODE_;
		$aData['yoyakunum'] = $req['srcYoyaku'];
		$aData['uketsukeno'] = $this->get_next_uketsukeNo($req['srcYoyaku']);
		$aData['status'] = $req['mode'];
		$aData['fee'] = $req['KanpuFee'];
		$aData['kinshucode'] = $req['KinshuCode'];
		$aData['cancelflg'] = 0;
		$aData['kanpujyutoudate'] = $req['RegYear'].$req['RegMonth'].$req['RegDay'];
		$aData['receiptdatetime'] = date('Y-m-d H:i:s');
		$aData['receiptstaffid'] = $_SESSION['userid'];

		$rc = $this->oDB->insert('t_yoyakukanpujyutou', $aData);
		if ($rc < 0) {
			return false;
		}
		return true;
	}

	//------------------------------------------------------------
	// 受付NOの採番 ( 受付できない判定も行う)
	//------------------------------------------------------------
	function get_next_uketsukeNo($YoyakuNum)
	{
		// 還付・充当テーブルに存在確認
		$sql = 'SELECT cancelflg, uketsukeno FROM t_yoyakukanpujyutou
			WHERE localgovcode=? AND yoyakunum=? ORDER BY uketsukeno DESC Limit 1';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		// データがなければ１を採番
		if (count($res) == 0) return 1;

		return $res['uketsukeno']+1;
	}

	function check_kannou($YoyakuNum)
	{
		$sql = 'SELECT COUNT(*) FROM t_yoyakufeekannoustatus
			WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array(_CITY_CODE_, $YoyakuNum);
		$res = $this->con->getOne($sql, $aWhere);
		if ($res > 0) {
			return true;
		}
		return false;
	}
}
?>
