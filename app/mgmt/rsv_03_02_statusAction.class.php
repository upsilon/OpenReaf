<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  空き状況表示
 *
 *  rsv_03_02_statusAction.class.php
 *  rsv_03_02.tpl
 */
session_cache_limiter('none');

define('P_I', 'rsv_01_02');
define('P2_I', 'rsv_03_02');
define('MBFPDF_BASE_PATH', OPENREAF_ROOT_PATH.'/app/class/mbfpdf/');
define('FPDF_FONTPATH', MBFPDF_BASE_PATH.'font/');

require MBFPDF_BASE_PATH.'mbfpdf.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';
require OPENREAF_ROOT_PATH.'/app/class/reserve_status.class.php';

class pdfContent extends MBFPDF
{
	function __construct($orientation='P', $unit='mm', $format='A4')
	{
		parent::__construct($orientation, $unit, $format);
	}

	function Footer()
	{
		$this->SetFont(PMINCHO, '', 10);
		$value = utf2sjis($this->PageNo().' ページ');
		$this->Text(274, 200, $value);
		$this->Text(256, 206, date('Y/m/d H:i:s'));
	}
}

class rsv_03_02_statusAction extends adminAction
{
	private $oSC = null;
	private $time_area_flag = array(1, 1, 1);

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		$this->set_header_info();

		$scd = $_GET['scd'];
		$usedate = $_GET['date'];

		$this->time_area_flag = array(1, 1, 1);
		if (isset($_SESSION[P_I]['TimeArea'])) {
			$this->time_area_flag = array(0, 0, 0);
			foreach ($_SESSION[P_I]['TimeArea'] as $val)
			{
				$this->time_area_flag[$val] = 1;
			}
		}

		$aSystem = $this->oSC->get_system_parameters();

		$priSql = $this->oPrivilege->getStaffShitsujyoSql();
		$aWhere = array_merge(array(_CITY_CODE_, $scd), $priSql[1]);

		$sql = "SELECT shitsujyocode, shitsujyoname, teiin, shitsujyoskbcode";
		$sql.= " FROM m_shitsujyou WHERE localgovcode=?";
		$sql.= " AND shisetsucode=? AND shitsujyokbn<'3'";
		$sql.= ' AND '.$priSql[0];
		$sql.= ' ORDER BY shitsujyoskbcode, shitsujyocode';
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$recs = array();

		$sql = 'SELECT c.localgovcode, c.shisetsucode, c.shitsujyocode, m.mencode, c.combino, c.combiname, m.teiin, c.combiskbno';
		$sql.= ' FROM m_mencombination c';
		$sql.= ' JOIN m_men m';
		$sql.= ' USING(localgovcode, shisetsucode, shitsujyocode, mencode)';
		$sql.= ' WHERE c.localgovcode=? AND c.shisetsucode=? AND c.shitsujyocode=?';
		$sql.= ' ORDER BY combiskbno, combino, mencode';
		foreach ($res as $key => $val)
		{
			$res2 = $this->con->getAll($sql, array(_CITY_CODE_, $scd, $val['shitsujyocode']), DB_FETCHMODE_ASSOC);
			if (count($res2) == 0) {
				$row = array(
						'localgovcode' => _CITY_CODE_,
						'shisetsucode' => $scd,
						'shitsujyocode' => $val['shitsujyocode'],
						'shitsujyoname' => $val['shitsujyoname'],
						'mencode' => 'ZZ',
						'combino' => 0,
						'combiname' => '',
						'teiin' => $val['teiin']
						);
				$recs[$row['shitsujyocode']][$row['combino']] = $row;
				continue;
			}
			$LastCombiNo = -1;
			foreach ($res2 as $row)
			{
				if ($row['combino'] == $LastCombiNo) {
					$recs[$row['shitsujyocode']][$row['combino']]['mencode'] .= '-'.$row['mencode'];
					$recs[$row['shitsujyocode']][$row['combino']]['teiin'] += $row['teiin'];
					continue;
				}
				$recs[$row['shitsujyocode']][$row['combino']] = $row;
				$recs[$row['shitsujyocode']][$row['combino']]['shitsujyoname'] = $val['shitsujyoname'];
				$LastCombiNo = $row['combino'];
			}
			unset($res2);
		}
		unset($res);
		foreach ($recs as $key1 => $val1)
		{
			$count = 0;
			foreach ($val1 as $key2 => $val2)
			{
				++$count;
				if ($count == count($val1)) {
					if ($count == 1) $count = 'simple';
					else $count = 'last';
				}
				$this->set_reserve_status($recs[$key1][$key2], $usedate, $aSystem);
				$recs[$key1][$key2]['UseDate'] = $usedate;
				$recs[$key1][$key2]['lineCount'] = $count;
			}
		}

		$ShisetsuName = $this->oSC->get_shisetsu_name($scd);
		$SelectDate = $this->oSC->getDateView($usedate);

		if (isset($_REQUEST['pdf']))
		{
			output_pdf($recs, $ShisetsuName, $SelectDate, $this->time_area_flag);
		} else {
			$this->oSmarty->assign('searchMode', $_SESSION[P_I]['searchMode']);
			$this->oSmarty->assign('showShisetsuName', $ShisetsuName);
			$this->oSmarty->assign('showSelectDate', $SelectDate);
			$this->oSmarty->assign('reserve_band_active', $this->time_area_flag);
			$this->oSmarty->assign('recs', $recs);
			$this->oSmarty->assign('req', $_GET);
			$this->oSmarty->display('rsv_03_02.tpl');
		}
	}

	function set_reserve_status(&$aTmp, $UseDate, &$aSys)
	{
		$mcd = explode('-', $aTmp['mencode']);

		$oRES = new reserve_status($this->con, _CITY_CODE_, $aTmp['shisetsucode'], $aTmp['shitsujyocode'], $mcd, false);

		$aTimeKoma = $oRES->get_time_schedule_ptn($UseDate, _PRIVILEGE_TIME_);
		$oRES->get_reserved_user($aTimeKoma, $UseDate);
		$aAPN = array('am' => 0, 'pm' => 0, 'nt' => 0, 'all' => 0);
		foreach ($aTimeKoma as $key => $koma)
		{
			$komaFrom = substr($koma['From'], 0, 4);
			$aTimeKoma[$key]['apnFlg'] = 0;
			if ($aSys['amfrom'] <= $komaFrom && $komaFrom <= $aSys['amto']) {
				if ($this->time_area_flag[0]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['am'];
			}
			if ($aSys['pmfrom'] <= $komaFrom && $komaFrom <= $aSys['pmto']) {
				if ($this->time_area_flag[1]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['pm'];
			}
			if ($aSys['ntfrom'] <= $komaFrom && $komaFrom <= $aSys['ntto']) {
				if ($this->time_area_flag[2]) $aTimeKoma[$key]['apnFlg'] = 1;
				++$aAPN['nt'];
			}
			++$aAPN['all'];
			$aTimeKoma[$key]['FromView'] = substr($koma['From'], 0, 2).':'.substr($koma['From'], 2, 2);
			$aTimeKoma[$key]['ToView'] = substr($koma['To'], 0, 2).':'.substr($koma['To'], 2, 2);
			$aTimeKoma[$key]['set'] = 0;
		}
		$aTmp['AMFromView'] = $aSys['AMFromView'];
		$aTmp['AMToView'] = $aSys['AMToView'];
		$aTmp['PMFromView'] = $aSys['PMFromView'];
		$aTmp['PMToView'] = $aSys['PMToView'];
		$aTmp['NTFromView'] = $aSys['NTFromView'];
		$aTmp['NTToView'] = $aSys['NTToView'];
		$aTmp['aAPN'] = $aAPN;
		$aTmp['komaCount'] = $aAPN['all'] + 2;
		$aTmp['Komasu'] = $aAPN['all'];
		$aTmp['aTimeKoma'] = $aTimeKoma;
	}
}

function output_pdf(&$recs, $ShisetsuName, $SelectDate, $reserve_band_active)
{
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="室場予約状況一覧.pdf"');

	$pdf = new pdfContent('L', 'mm', 'A4');
	$pdf->AddMBFont(PMINCHO, 'SJIS');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();

	$height = 5;
	$totalWidth = 277;
	$setNameWidth = 30;
	$setMenNumWidth = 10;
	$page_height = 0;

	//pageHead blank-line
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont(PMINCHO, 'B', 15);

	//title
	$pdf->SetFillColor(255, 255, 255);
	$value = utf2sjis('室場予約状況一覧');
	$pdf->Cell($totalWidth, $height*2, $value, 0, 0, 'C', 1);
	$pdf->Ln();
	$page_height = $height*2;

	$pdf->SetFont(PMINCHO, '', 12);
	//showShisetsuName & showSelectDate
	$value = utf2sjis("施設名称: ".$ShisetsuName);
	$pdf->Cell(137, $height, $value, 0, 0, 'L', 1);
	$value = utf2sjis("日付：".$SelectDate);
	$pdf->Cell(140, $height, $value, 0, 0, 'R', 1);
	$pdf->Ln();
	$page_height += $height;

	$pdf->SetFont(PMINCHO, '', 11);
	//table begin
	foreach($recs as $key0 => $val0)
	{
		if ($page_height > 170) {
			$pdf->AddPage();
			$page_height = 0;
		}

		foreach($val0 as $key1 => $val1)
		{
			//the noon-zone
			if ($val1['lineCount']=='1' || $val1['lineCount']=='simple') {
				$totalTimeCount = count($val1['aTimeKoma']);
				$timeZoneWidth = ($totalWidth-$setNameWidth-$setMenNumWidth)/$totalTimeCount;

				if (!_ROOM_STATUS_ALL_DAY_) {
					$pdf->Ln();
					$page_height += $height;
					$pdf->SetFillColor(0, 0, 210);
					$pdf->SetTextColor(255,255,255);
					$pdf->Cell($setNameWidth,$height,"","LTRB",0,'C',1);
					$pdf->Cell($setMenNumWidth,$height,"","LTRB",0,'C',1);
					if ($val1['aAPN']['am']) {
						if ($reserve_band_active[0]) {
							$value = utf2sjis("午前");
							$pdf->Cell(($timeZoneWidth*$val1['aAPN']['am']),$height,$value,"LTRB",0,'C',1);
						}
					}
					if ($val1['aAPN']['pm']) {
						if ($reserve_band_active[1]) {
							$value = utf2sjis("午後");
							$pdf->Cell(($timeZoneWidth*$val1['aAPN']['pm']),$height,$value,"LTRB",0,'C',1);
						}
					}
					if ($val1['aAPN']['nt']) {
						if ($reserve_band_active[2]) {
							$value = utf2sjis("夜間");
							$pdf->Cell(($timeZoneWidth*$val1['aAPN']['nt']),$height,$value,"LTRB",0,'C',1);
						}
					}
				}
				$pdf->Ln();
				$page_height += $height;

				$pdf->SetFillColor(190, 190, 255);
				$pdf->SetTextColor(0,0,0);

				$showShitsujyouName = utf2sjis($val1['shitsujyoname']);
				$useShitsujyouName = $showShitsujyouName;
				$subShitsujyouName = '';
				$useHeight = $height;
				$border = 'LTRB';

				if (mb_strlen($showShitsujyouName,'SJIS') >= 8) {
					$useShitsujyouName = mb_substr($showShitsujyouName,0,7,'SJIS');
					$subShitsujyouName = mb_substr($showShitsujyouName,7,mb_strlen($showShitsujyouName,'SJIS')-7,'SJIS');
					$useHeight = $height*2;
					$border = 'LTR';
				}
				$pdf->Cell($setNameWidth,$height,$useShitsujyouName,$border,0,'C',1);

				if ($subShitsujyouName != '') {
					$x=$pdf->GetX();
					$y=$pdf->GetY();
					$pdf->SetXY($x-$setNameWidth, $y+$height);
					$pdf->Cell($setNameWidth,$height,$subShitsujyouName,"LRB",0,'C',1);
					$pdf->SetXY($x, $y);
				}

				$showMenNumTitle=utf2sjis("定員");
				$pdf->Cell($setMenNumWidth,$useHeight,$showMenNumTitle,"LTRB",0,'C',1);

				foreach($val1['aTimeKoma'] as $timeKey => $timeVal)
				{
					if ($timeVal['apnFlg']) {
						$value = utf2sjis($timeVal['FromView']."～");
						$pdf->Cell($timeZoneWidth,$useHeight,$value,"LTRB",0,'C',1);
					}
				}
				$pdf->Ln();
				$page_height += $useHeight;
			}

			$pdf->SetFillColor(255, 255, 255);
			// line1 start 
			//menName
			$showMenName = $val1['combiname'];
			if ($showMenName == '') $showMenName = '全面';
			$showMenName = utf2sjis($showMenName);
			$useMenName = $showMenName;
			$subMenName = '';
			$useHeight = $height*2;
			$border = 'LTRB';
			if (mb_strlen($showMenName,'SJIS') >= 8) {
				$useMenName = mb_substr($showMenName,0,7,'SJIS');
				$subMenName = mb_substr($showMenName,7,mb_strlen($showMenName,'SJIS')-7,'SJIS');
				$useHeight = $height;
				$border = 'LTR';
			}
			$pdf->Cell($setNameWidth,$useHeight,$useMenName,$border,0,'C',1);
			
			//menNum
			$value = utf2sjis($val1['teiin']);
			$pdf->Cell($setMenNumWidth,$height*2,$value,"LTRB",0,'C',1);

			//timeZone
			$totalTimeCount = count($val1['aTimeKoma']);
			$timeZoneWidth=($totalWidth-$setNameWidth-$setMenNumWidth)/$totalTimeCount;
			foreach($val1['aTimeKoma'] as $timeKey => $timeVal)
			{
				if ($timeVal['apnFlg']) {
					$value = '';
					$recs[$key0][$key1]['aTimeKoma'][$timeKey]['showSecondLine']="0";
					$useHeight=$height*2;
					$border="LTRB";
					if ($timeVal['set']) {
						$value = '申し込む';
					} elseif ($timeVal['reserved']) {
						$value = $timeVal['Mark'];
					} else {
						$value = "○";
					}
					$value = utf2sjis($value);
					$pdf->Cell($timeZoneWidth,$useHeight,$value,$border,0,'C',1);
				}
			}
			$pdf->Cell(1,$height,"",0,0,'C',0);
			$pdf->Ln();
			$page_height += $useHeight;
			// line1 end 

			// line2 start
			if ($subMenName != '') {
				$showFlag=1;
				$border="LRB";
			} else {
				$showFlag=0;
				$border=0;
			}
			$pdf->Cell($setNameWidth,$height,$subMenName,$border,0,'C',$showFlag);

			$pdf->Cell($setMenNumWidth,$height,"",0,0,'C',0);

			foreach($val1['aTimeKoma'] as $timeKey => $timeVal)
			{
				if ($timeVal['apnFlg']) {
					if ($recs[$key0][$key1]['aTimeKoma'][$timeKey]['showSecondLine']=="1") {
						$subShow=$recs[$key0][$key1]['aTimeKoma'][$timeKey]['showSecondContent'];
						$subShow=utf2sjis($subShow);
						$pdf->Cell($timeZoneWidth,$height,$subShow,"LRB",0,'C',1);
					} else {
						$pdf->Cell($timeZoneWidth,$height,"",0,0,'C',0);
					}
				}
			}
			$pdf->Cell(1,$height,"",0,0,'C',0);
			$pdf->Ln();
			$page_height += $height;
			// line2 end

			//time-part-intro line
			if (!_ROOM_STATUS_ALL_DAY_) {
				if ($val1['lineCount']=='last' || $val1['lineCount']=='simple') {
					$pdf->SetFillColor(255, 223, 239);
					$value = '※';
					$value.= '  午前  '.$val1['AMFromView']."-".$val1['AMToView'];
					$value.= '  午後  '.$val1['PMFromView']."-".$val1['PMToView'];
					$value.= '  夜間  '.$val1['NTFromView']."-".$val1['NTToView'];
					$value = utf2sjis($value);
					$pdf->Cell($totalWidth,$height,$value,"LTRB",0,'L',1);
					$pdf->Ln();
					$page_height += $height;
					$pdf->SetFillColor(255, 255, 255);
				}
			}
		}
	}

	$pdf->Output();
}

function utf2sjis($str)
{
	return mb_convert_encoding($str, 'SJIS-win', 'UTF-8');
}
?>
