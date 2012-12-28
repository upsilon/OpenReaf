<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  収納状態管理
 *
 *  receipt_status.class.php
 */

class receipt_status
{
	private $YoyakuNum = '';	// 予約番号
	private $HonYoyakuKbn = '';
	private $ShisetsuFee = 0;	// 施設料金(調整額を含む)
	private $BillingFee = 0;
	private $CancelFee = 0;
	private $ReceiptFee = 0;
	private $ReceiptData = array();
	private $isReceipt = 0;
	private $con = null;
	private $lcd = '';

	/********************************************************************
	*
	* コンストラクタ
	*
	* @param
	* $con		:	DBコネクション
	* $YoyakuNum	:	予約番号
	* $HonYoyakuKbn	:	本予約区分
	* $ShisetsuFee	:	施設使用料
	*
	*******************************************************************/
	function __construct(&$con, $YoyakuNum, $HonYoyakuKbn, $ShisetsuFee=0)
	{
		$this->con = $con;
		$this->lcd = _CITY_CODE_;
		$this->YoyakuNum = $YoyakuNum;
		$this->HonYoyakuKbn = $HonYoyakuKbn;
		$this->ShisetsuFee = $ShisetsuFee;
		$this->BillingFee = $ShisetsuFee;

		if ($HonYoyakuKbn == '02') {
			$this->ReceiptData = $this->getReceiptData();
		}
		$this->isReceipt = count($this->ReceiptData);
		if ($this->isReceipt) {
			$this->CancelFee = $this->ReceiptData[7];
			$this->ReceiptFee = $this->ReceiptData[0];
		}
	}

	/********************************************************************
	*
	* 予約料金受付テーブルより収納済金額情報を取得
	*
	* @return	array	収納済金額情報
	*
	*******************************************************************/
	function getReceiptData()
	{
		$sql = 'SELECT cash+chg+ticket+
				kouzafurikomi+others+jyutou,
				cash, chg, ticket, kouzafurikomi,
				others, jyutou, cancelfee,
				receptdate, uketime, receptid, receptplace
			FROM t_yoyakufeeuketsuke
			WHERE localgovcode=? AND yoyakunum=?';
		$where = array($this->lcd, $this->YoyakuNum);
		return $this->con->getRow($sql, $where);
	}

	/********************************************************************
	*
	* 収納済金額を取得
	*
	* @return	array	収納済金額とその内訳
	*
	*******************************************************************/
	function getReceiptFee()
	{
		if ($this->isReceipt) {
			return $this->ReceiptData;
		} else {
			return  array(0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '');
		}
	}

	/********************************************************************
	*
	* 収納済金額合計を取得
	*
	* @return	int	収納済金額
	*
	*******************************************************************/
	function getShunouzumiFee()
	{
		return $this->ReceiptFee;
	}

	/********************************************************************
	*
	* 指定された予約の収納状態を取得
	*
	* @return	mix
	* integer	0 :	-
	* 		1 :	未収納
	* 		2 :	無料
	* 		3 :	一部入金
	* 		4 :	完納
	* 		5 :	超過(還付)
	* 		6 :	還付済
	* 		7 :	充当
	* 		8 :	還付なし
	*
	*******************************************************************/
	function getReceiptStatus($isCancel=false, $sumToo=false)
	{
		$result = array();

		if ($this->HonYoyakuKbn != '02') {
			if ($this->BillingFee == 0) {
				$result = array(2, 0); //無料
			} else {
				if ($isCancel) {
					$result = array(0, 0); //-
				} else {
					$result = array(1, 0); //未収納
				}
			}
			return $sumToo ? $result : $result[0];
		}

		$billingFee = $this->BillingFee;
		if ($this->CancelFee > 0) {
			$billingFee = $this->CancelFee;
		}

		// 納付額超過
		if ($this->ReceiptFee > $billingFee) {
			$result = $this->checkKanpuStatus($isCancel);
		}
		// 納付額不足
		elseif ($this->ReceiptFee < $billingFee) {
			if ($this->ReceiptFee == 0) {
				if ($isCancel && $this->CancelFee == 0) {
					$result = array(0, 0); //-
				} else {
					$result = array(1, 0); //未収納
				}
			} else {
				if ($isCancel && $this->CancelFee == 0) {
					$result = $this->checkKanpuStatus($isCancel);
				} else {
					$result = array(3, $this->ReceiptFee); //一部入金
				}
			}
		}
		// 納付額が施設使用料金に等しい
		else {
			if ($this->BillingFee == 0) {
				$result = array(2, $this->ReceiptFee); //無料
			} else {
				if ($isCancel && $this->CancelFee == 0) {
					$result = $this->checkKanpuStatus($isCancel);
				} else {
					$result = array(4, $this->ReceiptFee); //完納
				}
			}
		}
		return $sumToo ? $result : $result[0];
	}

	/********************************************************************
	*
	* 収納金額の金種を出力する。
	*
	* @return	str	金種
	*
	*******************************************************************/
	function getReceiptMethod($kinshuCode)
	{
		$num = 0;
		$method = '';

		if ($this->isReceipt == 0) {
			return '';
		}
		if ($this->ReceiptData[1] != 0) {
			$method = $kinshuCode['01'];
			++$num;
		}
		if ($this->ReceiptData[2] != 0) {
			$method = $kinshuCode['02'];
			++$num;
		}
		if ($this->ReceiptData[3] != 0) {
			$method = $kinshuCode['03'];
			++$num;
		}
		if ($this->ReceiptData[4] != 0) {
			$method = $kinshuCode['04'];
			++$num;
		}
		if ($this->ReceiptData[5] != 0) {
			$method = $kinshuCode['05'];
			++$num;
		}
		if ($this->ReceiptData[6] != 0) {
			$method = $kinshuCode['06'];
			++$num;
		}
		if ($num > 1) {
			$method .= '他';
		}
		return $method;
	}

	/********************************************************************
	*
	* 完納ステータステーブルのデータ件数を取得する
	*
	* @return	int	該当件数
	*
	*******************************************************************/
	function getT_YoyakuFeeKannouStatusCount()
	{
		$sql = 'SELECT count(*) FROM t_yoyakufeekannoustatus
			WHERE localgovcode=? AND yoyakunum=?';
		$aWhere = array($this->lcd, $this->YoyakuNum);
		return $this->con->getOne($sql,$aWhere);
	}

	/********************************************************************
	*
	* 還付額を差し引いた収納金額を取得する
	*
	* @return	int	合計金額
	*
	*******************************************************************/
	function getT_YoyakuFeeUketsukeSumFee()
	{
		list($status, $fee) = $this->getKanpuJyutou();
		return $this->ReceiptFee - $fee;
	}

	/********************************************************************
	*
	* 還付済と充当を判別し、還付額を算出する。
	*
	* @return	array
	*		[0]int 	0 : 該当なし
	* 			1 : 還付済
	* 			2 : 充当
	*		[1]int 	金額
	*
	*******************************************************************/
	function getKanpuJyutou()
	{
		$sql = 'SELECT status, SUM(fee) AS sumfee
			FROM t_yoyakukanpujyutou
			WHERE localgovcode=? AND yoyakunum=? AND cancelflg=0
			GROUP BY status';
		$aWhere = array($this->lcd, $this->YoyakuNum);
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		$count = count($res);
		if ($count == 1) {
			return array($res[0]['status'], $res[0]['sumfee']);
		} elseif ($count > 1) {
			$fee = 0;
			foreach ($res as $val)
			{
				$fee += $val['sumfee'];
			}
			return array(1, $fee);
		} else {
			return array(0, 0);
		}
	}

	/********************************************************************
	*
	* 還付率を取得する
	*
	* @return	還付率
	*
	*******************************************************************/
	function getCancelRate()
	{
		$sql = 'SELECT c.rate FROM h_yoyaku y
			LEFT JOIN m_canceljiyucode c
			ON y.localgovcode=c.localgovcode
			AND y.canceljiyucode=c.cancelcode
			WHERE y.localgovcode=? AND y.yoyakunum=?';
		$aWhere = array($this->lcd, $this->YoyakuNum);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if ($row) {
			$rate = $row['rate'] == '' ? 100 : $row['rate'];
			return $rate;
		}
		return 100;
	}

	/********************************************************************
	*
	* 還付状態を判定する
	*
	* @return	array
	*		[0]	還付状態
	*		[1]	還付額を引いた収納金額
	*
	*******************************************************************/
	function checkKanpuStatus($isCancel)
	{
		// 完納ステータステーブルに存在した場合は還付なし
		if ($this->getT_YoyakuFeeKannouStatusCount()) {
			return array(8, $this->ReceiptFee); //還付なし
		}

		// 還付・充当を判別
		list($kjFlg, $kanpuJyutouFee) = $this->getKanpuJyutou();
		if ($kjFlg > 0) {
			$code = 6; //還付済
			if ($kjFlg == 2) $code = 7; //充当

			$sumFee = $this->ReceiptFee - $kanpuJyutouFee;
			$kanpuFee = $this->ReceiptFee - $this->ShisetsuFee;
			if ($isCancel) {
				$rate = $this->getCancelRate();
				$kanpuFee += intval($this->ShisetsuFee*$rate/100);
			}

			if ($kanpuFee == $kanpuJyutouFee) {
				return array($code, $sumFee);
			} else {
				return array(5, $sumFee); //超過(還付)
			}
		}
		return array(5, $this->ReceiptFee); //超過(還付)
	}
}
?>
