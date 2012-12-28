<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者登録通知書
 *
 *  usr_03_01_pdfAction.class.php
 */
session_cache_limiter('none');

define('MBFPDF_BASE_PATH', OPENREAF_ROOT_PATH.'/app/class/mbfpdf/');
define('FPDF_FONTPATH', MBFPDF_BASE_PATH.'font/');

require MBFPDF_BASE_PATH.'mbfpdf.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class usr_03_01_pdfAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$oSC = new system_common($this->con);

		$aSys = $oSC->get_system_parameters();
		$aUser = $oSC->get_user_data($_REQUEST['UserID']);
		$SystemDate = utf2sjis($oSC->put_wareki_date(date('Ymd')));
		$NewApplyDate = utf2sjis($oSC->put_wareki_date($aUser['newapplydate']));
		$issuer = utf2sjis($aSys['localgovname'].'長  '.$aSys['mayorname']);

		header('Content-Disposition: attachment; filename="利用者登録通知書.pdf"');
		header('Content-Type: application/pdf');

		$pdf = new MBFPDF('P', 'mm', 'A4');

		$pdf->AddMBFont(MINCHO, 'SJIS');
		$pdf->AddMBFont(GOTHIC, 'SJIS');

		$pdf->SetMargins(20, 25, 25); // Left, Top, Right
		$pdf->SetAutoPageBreak(false);

		$pdf->AddPage();

		$pdf->SetFont(GOTHIC, '', 16);
		$value = utf2sjis(_SYSTEM_NAME_);
		$pdf->Cell(0, 10, $value, 0, 1, 'C');
		$value = utf2sjis("利用者登録通知書");
		$pdf->Cell(0, 10, $value, 0, 1, 'C');

		$pdf->SetFontSize(12);
		$pdf->text(160, 50, $SystemDate);

		// 団体名の文字は多いときの改行表示処理
		$nameLength = mb_strlen($aUser['namesei'],"UTF-8");
		$nameCount = ceil($nameLength/32);
		$fontSize = 14;
		if($nameCount > 1) $fontSize -= $nameCount * 2;
		$pdf->SetFontSize($fontSize);

		$nameStr = "";
		for($i = 0; $i<$nameCount; ++$i)
		{
			$nameStr = mb_substr($aUser['namesei'],$i*32,(1+$i)*32,"UTF-8");
			$nameStr = utf2sjis($nameStr);
			if($i == ($nameCount-1)) {
				$nameStr .= utf2sjis(" 様");
				$pdf->text(30, 62, $nameStr);
			} else {
				$pdf->text(30, 58, $nameStr);
			}
		}

		$pdf->SetFontSize(12);
		$pdf->SetY($pdf->GetY()+20);
		$pdf->Cell(150, 10, $issuer, 0, 1, 'R');

		$pdf->SetY($pdf->GetY()+14);
		$pdf->SetFont(MINCHO, 'B', 12);
		$value = utf2sjis('次のとおり、'._SYSTEM_NAME_.'の利用者として登録しました。');
		$pdf->Cell(0, 5, $value, 0, 1);

		$pdf->SetFont(GOTHIC, '', 12);
		$value = utf2sjis('１ 利用者ＩＤ等');
		$pdf->text(15, 110, $value);

		$pdf->SetXY($pdf->GetX()+45, $pdf->GetY()+16);
		$pdf->SetFontSize(14);
		$value = utf2sjis('利用者ＩＤ ');
		$pdf->Cell(30, 10, $value, 'LT', 0, 'C');
		$pdf->SetFont(GOTHIC, 'B', 14);
		$pdf->Cell(60, 16, $aUser['userid'], 'LTR', 1, 'C');
		$pdf->SetXY($pdf->GetX()+45, $pdf->GetY()-8);
		$pdf->SetFont(GOTHIC, '', 14);
		$value = utf2sjis('（登録番号）');
		$pdf->Cell(30, 6, $value, 'L', 1, 'C');
		$pdf->SetX($pdf->GetX()+45);
		$value = utf2sjis("パスワード");
		$pdf->Cell(30, 16, $value, 'LTB', 0, 'C');
		$pdf->Cell(60, 16, $aUser['pwd'], 1, 1, 'C');

		$pdf->SetY($pdf->GetY()+5);
		$pdf->SetFont(MINCHO, '', 12);
		$value = utf2sjis("※予約システム利用の際は、必ず「利用者ＩＤ」と「パスワード」が必要となります。　また、窓口・電話等での施設予約に関するお問い合わせの際にも、「利用者ＩＤ」を\n　お知らせください。");
		$pdf->Write(5, $value);

		$pdf->SetFont(GOTHIC, '', 12);
		$value = utf2sjis('２ 登録年月日');
		$pdf->text(15, 170, $value);
		$pdf->text(80, 170, $NewApplyDate);

		$value = utf2sjis('３ パスワードについて');
		$pdf->text(15, 185, $value);

		$pdf->Ln(35);
		$pdf->SetFont(MINCHO, '', 12);
		$value = '本通知書のパスワードは仮パスワードです。必ずご本人によるパスワードの変更を行ってください。パソコンから下記のURLにアクセスしてパスワード変更を行うことができます。';
		$pdf->Write(5, utf2sjis($value));
		$pdf->Ln(10);
		$pdf->Write(5, $aSys['topmenuurl']);
		$pdf->Ln(10);
		$value = "上記URLにアクセス後、以下のように進みます。\n";
		$value.= "1.「マイページ」ボタンをクリック\n";
		$value.= "2.「ログイン」画面で上記の利用者IDとパスワードを使ってログイン\n";
		$value.= "3.「利用者メニュー」画面の「利用者情報の変更」ボタンをクリック\n";
		$value.= "4.「利用者情報照会」画面の「パスワード変更」ボタンをクリック\n";
		$value.= "5.「パスワード変更」画面でパスワード変更";
		$pdf->Write(5, utf2sjis($value));

		$pdf->SetFontSize(8);
		$pdf->text(176, 290, date('Y/m/d H:i:s'));

		$pdf->Output();
	}
}

function utf2sjis($val)
{
	return mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
}
?>
