<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  fee_base.php
 */
class fee_base
{
	protected $con = null;
	protected $src = array();

	//
	// コンストラクタ
	//
	// object con
	// array src
	// member	localgovcode
	//		usedate
	//
	function __construct(&$con, &$src)
	{
		$this->con = $con;
		$this->src = &$src;
	}

	//
	// 料金計算
	//
	function calc_fee($fee, $taxRate, $genRate, $type=0)
	{
		$Fee = $fee*(100-$genRate)/100;
		$Fee = $this->truncate_fraction($Fee, $type);
		return floor($Fee*(100+$taxRate)/100);
	}

	//
	// 税金計算
	//
	function calc_tax($fee, $taxRate, $genRate, $type=0)
	{
		$Fee = $fee*(100-$genRate)/100;
		$Fee = $this->truncate_fraction($Fee, $type);
		return floor($Fee*$taxRate/100);
	}

	//
	// 税金計算(料金からの計算)
	//
	function calc_tax_from_fee($fee, $taxRate)
	{
		return $fee - ceil($fee*100/($taxRate+100));
	}

	//
	// 税率取得
	//
	function get_tax_rate()
	{
		$sql = "SELECT taxrate, taxcutkbn, appdatefrom FROM m_tax ";
		$sql.= "WHERE localgovcode = ? AND appdatefrom <= ? ";
		$sql.= "AND (limitday >= ? OR limitday = '') ";
		$sql.= "ORDER BY appdatefrom DESC";
		$aWhere = array($this->src['localgovcode'], $this->src['usedate'], $this->src['usedate']);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if ($row) {
			return $row['taxrate'];
		}
		return 0;
	}

	function truncate_fraction($value, $type)
	{
		switch (intval($type)) {
			case 1:
				return ceil($value);
			case 2:
				return round($value);
			case 3:
				return floor($value/10)*10;
			case 4:
				return ceil($value/10)*10;
			case 5:
				return round($value/10)*10;
			default:
				return floor($value);
		}
	}
}
?>
