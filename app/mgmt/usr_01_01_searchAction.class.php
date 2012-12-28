<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  利用者検索
 *
 *  usr_01_01_searchAction.class.php
 *  usr_01_01.tpl
 */
session_cache_limiter('none');

define('P_I', 'usr_01_01');
define('MBFPDF_BASE_PATH', OPENREAF_ROOT_PATH.'/app/class/mbfpdf/');
define('FPDF_FONTPATH', MBFPDF_BASE_PATH.'font/');

require MBFPDF_BASE_PATH.'mbfpdf.php';
require OPENREAF_ROOT_PATH.'/app/include/user.php';
require OPENREAF_ROOT_PATH.'/app/class/system_common.class.php';

class pdfContent extends MBFPDF
{
	function __construct($orientation='P', $unit='mm', $format='A4')
	{
		parent::__construct($orientation, $unit, $format);
	}

	function Header()
	{
		$this->SetFillColor(200, 200, 255); // 塗りつぶしの色
		$this->SetFont(GOTHIC, 'BU', 16);
		$value = utf2sjis('   利用者一覧表   ');
		$this->Cell(0, 10, $value, 0, 1, "C", 1);

		$this->Cell(1, 9, '', 0, 1);

		$this->SetFont(GOTHIC, '', 9);
		$this->Text(254, 18, date('Y/m/d H:i:s'));
	}

	function Footer()
	{
		$this->SetFont(GOTHIC, '', 9);
		$this->SetXY(-24, -16);
		$this->Cell(0, 16, 'Page '.$this->PageNo().' / {nb}');
	}
}

class usr_01_01_searchAction extends adminAction
{
	private $oSC = null;
	private $searchLimit = 5000;

	function __construct()
	{
		parent::__construct();

		$this->oSC = new system_common($this->con);
	}

	function execute()
	{
		global $userjyoutaikbn_arr;

		$message = '';
		$p = array();
		$res = array();

		$this->set_header_info();

		if (isset($_GET['back'])) {
			if (isset($_SESSION[P_I])) $p = $_SESSION[P_I];
		} elseif (empty($_POST)) {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
		} else {
			if (isset($_SESSION[P_I])) unset($_SESSION[P_I]);
			$p = $_POST;
		}

		$mode = isset($p['mode']) ? $p['mode'] : '';

		if ($mode != '') {
			if (!$this->checkInput($p)) {
				$message = '検索条件を入力してください。';
			} else {
				$p['mode'] = 'search';
				$_SESSION[P_I] = $p;
				$res = $this->get_db_info($p);
				$num = count($res);
				if ($this->searchLimit < $num) {
					$message = '検索結果が'.number_format($num).'件ありました。<br>表示できないため'.number_format($this->searchLimit).'件以下になるように検索条件を絞り込んでください。';
					$res = array();
				}
			}
		}

		if ($mode == 'pdf') {
			$res = $this->remake_data($res);
			output_pdf($res);
		} elseif ($mode == 'csv') {
			$res = $this->remake_data($res);
			$file_name = 'user-'.date('Ymd').'.csv';
			output_csv($file_name, $res);
		} else {
			$aUserJyoutaKbn = array();
			foreach ($userjyoutaikbn_arr as $val => $label)
			{
				$key = sprintf('%02d', $val);
				$aUserJyoutaiKbn[$key] = $label;
			}
			$aKbnData = $this->oSC->get_codename_options('KojinDanKbn');
			$aShisetsu = $this->oPrivilege->get_shisetsu_list();
			$aMokuteki = $this->oSC->get_purpose_name_array();
			$aBusho = $this->oPrivilege->get_busho_options();

			$this->oSmarty->assign('aKbnData', $aKbnData);
			$this->oSmarty->assign('aShisetsu', $aShisetsu);
			$this->oSmarty->assign('aMokuteki', $aMokuteki);
			$this->oSmarty->assign('aBusho', $aBusho);
			$this->oSmarty->assign('aUserJyoutaiKbn', $aUserJyoutaiKbn);
			$this->oSmarty->assign('p', $p);
			$this->oSmarty->assign('results', $res);
			$this->oSmarty->assign('message', $message);
			$this->oSmarty->display('usr_01_01.tpl');
		}
	}

	function checkInput(&$p)
	{
		if ($p['UserID'] == ''
			&& $p['UserIDTo'] == ''
			&& $p['KojinDanKbn'] == ''
			&& $p['HeadName'] == ''
			&& $p['Name'] == ''
			&& $p['MailAdr'] == ''
			&& $p['TelNo1'] == ''
			&& $p['TelNo2'] == ''
			&& $p['TelNo3'] == ''
			&& $p['ShisetsuCode'] == ''
			&& $p['MokutekiCode'] == '00'
			&& $p['TourokuBushoCode'] == ''
			&& $p['UserJyoutaiKbn'] == ''
			&& $p['UserLimitStart'] == ''
			&& $p['UserLimitEnd'] == ''
			&& $p['FirstApplyStart'] == ''
			&& $p['FirstApplyEnd'] == ''
		) return false;

		return true;
	}

	function get_db_info(&$p)
	{
		$sql = "SELECT u.* FROM m_user u";

		$orderBy = " ORDER BY userid";

		$where = " WHERE u.localgovcode='"._CITY_CODE_."' ";

		//利用者ID
		if (strlen($p['UserID']) > 0 && strlen($p['UserIDTo']) == 0) {
			if (isset($p['PartialMatchFlg'])) {
				$where .= "AND u.userid LIKE '{$p['UserID']}%' ";
			} else {
				$where .= "AND u.userid = '{$p['UserID']}' ";
			}
		} elseif (strlen($p['UserID']) > 0 && strlen($p['UserIDTo']) > 0) {

			$where .= "AND u.userid BETWEEN '{$p['UserID']}' AND '{$p['UserIDTo']}' ";
		} elseif (strlen($p['UserID']) == 0 && strlen($p['UserIDTo']) > 0) {
			$where .= "AND u.userid = '{$p['UserIDTo']}' ";
		}
		//登録区分
		if (strlen($p['KojinDanKbn'])>0) {
			$where .= "AND u.kojindankbn = '{$p['KojinDanKbn']}' ";
		}
		//代表者名
		if (strlen($p['HeadName']) > 0) {
			$where .= "AND (u.headnamesei LIKE '%".$p['HeadName']."%' ";
			$where .= "OR u.headnameseikana LIKE '%".$p['HeadName']."%') ";
		}
		//利用者名
		if (strlen($p['Name']) > 0) {
			$where .= "AND (u.namesei LIKE '%".$p['Name']."%' ";
			$where .= "OR u.nameseikana LIKE '%".$p['Name']."%') ";
		}
		//メールアドレス
		if (strlen($p['MailAdr']) > 0) {
			$where .= "AND u.mailadr LIKE '%".$p['MailAdr']."%' ";
		}
		//電話番号
		if (strlen($p['TelNo1']) > 0 || strlen($p['TelNo2']) > 0 || strlen($p['TelNo3']) > 0) {
			$where .= "AND ((u.telno11 = '".$p['TelNo1']."' AND u.telno12 = '".$p['TelNo2']."' AND u.telno13 = '".$p['TelNo3']."') OR (u.telno21 = '".$p['TelNo1']."' AND u.telno22 = '".$p['TelNo2']."' AND u.telno23 = '".$p['TelNo3']."')) ";
		}
		// 施設利用権限
		if (strlen($p['ShisetsuCode']) > 0) {
			$where.= "AND u.shisetsu LIKE '%{$p['ShisetsuCode']}%' " ;
		}
		// 利用目的
		if (intval($p['MokutekiCode']) > 0) {
			$where.= "AND u.purpose LIKE '%{$p['MokutekiCode']}%' " ;
		}
		// 登録部署
		if (strlen($p['TourokuBushoCode']) > 0) {
			$where.= "AND u.tourokubushocode = '{$p['TourokuBushoCode']}' " ;
		}
		// 登録状態
		if (strlen($p['UserJyoutaiKbn']) > 0) {
			$UserJyoutaiKbn = intval($p['UserJyoutaiKbn']);
			$where.= "AND u.userjyoutaikbn = '{$UserJyoutaiKbn}' " ;
		}
		// 登録期限
		if (preg_match('/^[0-9]{8}$/', $p['UserLimitStart'])
			&& preg_match('/^[0-9]{8}$/', $p['UserLimitEnd'])) {
			$where .= "AND u.userlimit BETWEEN '{$p['UserLimitStart']}' AND '{$p['UserLimitEnd']}' ";
		} elseif (preg_match('/^[0-9]{8}$/', $p['UserLimitStart'])) {
			$where .= "AND u.userlimit >= '{$p['UserLimitStart']}' ";
		} elseif (preg_match('/^[0-9]{8}$/', $p['UserLimitEnd'])) {
			$where .= "AND u.userlimit <= '{$p['UserLimitEnd']}' ";
		}
		// 申請日
		if (preg_match('/^[0-9]{8}$/', $p['FirstApplyStart'])
			&& preg_match('/^[0-9]{8}$/', $p['FirstApplyEnd'])) {
			$where .= "AND u.firstapplydate BETWEEN '{$p['FirstApplyStart']}' AND '{$p['FirstApplyEnd']}' ";
		} elseif (preg_match('/^[0-9]{8}$/', $p['FirstApplyStart'])) {
			$where .= "AND u.firstapplydate >= '{$p['FirstApplyStart']}' ";
		} elseif (preg_match('/^[0-9]{8}$/', $p['FirstApplyEnd'])) {
			$where .= "AND u.firstapplydate <= '{$p['FirstApplyEnd']}' ";
		}

		$sql.= $where.$orderBy;
		return $this->con->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	}

	function get_user_genmens()
	{
		$sql = 'SELECT u.userid, g.koteigenname
			FROM m_usrgenmen u
			JOIN m_genmen g
			USING (localgovcode, koteigencode)
			WHERE u.localgovcode=?
			ORDER BY userid ASC, koteigencode ASC';

		$res = $this->con->getAll($sql, array(_CITY_CODE_));
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		return $recs;
	}

	function remake_data(&$res)
	{
		global $userjyoutaikbn_arr, $nengoukbn_arr;

		$aKbnData = $this->oSC->get_codename_options('KojinDanKbn');
		$aBusho = $this->oPrivilege->get_busho_options();
		$aGenmen = $this->get_user_genmens();
		$aStaff = $this->oSC->get_staff_name_array();

		$recs = array();
		foreach ($res as $i => $val)
		{
			$userid = $val['userid'];
			$recs[$i]['UserID'] = $val['userid'];
			$recs[$i]['NameSeiKana'] = $val['nameseikana'];
			$recs[$i]['NameSei'] = $val['namesei'];
			$recs[$i]['HeadNameSeiKana'] = $val['headnameseikana'];
			$recs[$i]['HeadNameSei'] = $val['headnamesei'];
			// 代表者誕生日
			$recs[$i]["Bday"] = $nengoukbn_arr[intval($val['nengoukbn'])];
			//   『年』
			if ($val["bdayyear"] != '') {
				$recs[$i]["Bday"] .= $val["bdayyear"]."年";
			} 
			//   『月』
			if ($val["bdaymonth"] != '') {
				$recs[$i]["Bday"] .= $val["bdaymonth"]."月";
			} 
			//   『日』
			if ($val["bdayday"] != '') {
				$recs[$i]["Bday"] .= $val["bdayday"]."日";
			} 
			// 減免状態
			$recs[$i]["Genmen"] = "なし";
			if (isset($aGenmen[$userid]) == true) {
				$recs[$i]["Genmen"] = $aGenmen[$userid] ;
			}
			// 登録状態
			$recs[$i]["UserJyoutaiKbnName"] = $userjyoutaikbn_arr[$val["userjyoutaikbn"]];
			if (($val['userjyoutaikbn'] == '0' || $val['userjyoutaikbn'] == '4') && $val['temporaryid'] == '') {
				$recs[$i]["UserJyoutaiKbnName"] = '本人未確認';
			}
			$recs[$i]['StopEraseJiyu'] = $val['stoperasejiyu'];
			$recs[$i]['HyoujiMei'] = $val['hyoujimei'];
			// 登録区分(名称)
			$recs[$i]["KojinDanKbnName"] = $aKbnData[$val["kojindankbn"]];
			// 連絡先
			// 郵便番号
			$recs[$i]["PostNo"] = $val["postno1"]."-".$val["postno2"] ;
			if ($recs[$i]["PostNo"] == "-") {
				$recs[$i]["PostNo"] = '';
			}
			// 住所
			$recs[$i]["Adr"] = trim(trim($val["adr1"]." ".$val["adr2"]), "　") ;
			// 電話番号１
			$recs[$i]["TelNo1"] = $val["telno11"]."-".$val["telno12"]."-".$val["telno13"];
			if ($recs[$i]["TelNo1"] == "--") {
				$recs[$i]["TelNo1"] = '';
			}
			// 電話番号２
			$recs[$i]["TelNo2"] = $val["telno21"]."-".$val["telno22"]."-".$val["telno23"];
			if ($recs[$i]["TelNo2"] == "--" ) {
				$recs[$i]["TelNo2"] = '';
			}
			// FAX番号
			$recs[$i]["FAXNo"] = $val["faxno1"]."-".$val["faxno2"]."-".$val["faxno3"];
			if ($recs[$i]["FAXNo"] == "--") {
				$recs[$i]["FAXNo"] = '';
			}
			$recs[$i]['MailAdr'] = $val['mailadr'];
			// 申請日
			$recs[$i]["FirstApplyDate"] = '';
			if ($val["firstapplydate"] != '') {
				$recs[$i]["FirstApplyDate"] = $this->oSC->getDateView($val['firstapplydate'], false);
			}
			// 登録日
			$recs[$i]["FirstEntryDate"] = '';
			if ($val["firstentrydate"] != '') {
				$recs[$i]["FirstEntryDate"] = $this->oSC->getDateView($val['firstentrydate'], false);
			}
			// 登録部署 TourokuBushoName
			$recs[$i]["TourokuBushoName"] = '';
			if (isset($aBusho[$val['tourokubushocode']])) {
				$recs[$i]["TourokuBushoName"] = $aBusho[$val['tourokubushocode']];
			}
			// 有効期限
			$recs[$i]['UserLimit'] = '未設定';
			if ($val['userlimit'] != '') {
				$recs[$i]['UserLimit'] = intval($val['userlimit']) > 20500000 ? 'なし' : $this->oSC->getDateView($val['userlimit'], false);
			}
			$recs[$i]["UpdDate"] = $this->oSC->getDateView($val['upddate'], false);
			// 登録者 UpdName
			$recs[$i]["UpdName"] = isset($aStaff[$val['updid']]) ? $aStaff[$val['updid']] : $val['updid'];
		}
		return $recs;
	}
}

//-----------------------------------------------------------------------------
// CSV出力
//-----------------------------------------------------------------------------
function output_csv($name, $data)
{
	$out = '"利用者ID","申請者ヨミ","申請者","代表者ヨミ","代表者",'
		.'"誕生日","減免状態","登録状態","停止/抹消事由","表示名称",'
		.'"登録区分","郵便番号","住所","電話番号１","電話番号２",'
		.'"FAX番号","メールアドレス","申請日","登録日",'
		.'"登録部署","有効期限","最終更新日","登録者",'."\n";

	if ($data) {
		foreach ($data as $row => $val)
		{
			if ($val) {
				foreach ($val as $cell)
				{
					 $out .= '"'.str_replace('"', '""', $cell).'",';
				}
			}
			$out .= "\n";
		}
	}

	header("Content-type: application/octet-stream; name={$name}");
	header("Content-disposition: attachment; filename={$name}");
	echo utf2sjis(str_replace("\n", "\r\n", $out));
}

//------------------------------------------------------------------------------
// PDF出力
//------------------------------------------------------------------------------
function output_pdf($aryData)
{
	header('Content-Disposition: attachment; filename="利用者一覧表.pdf"');
	header('Content-Type: application/pdf');

	$pdf = new pdfContent('L', 'mm', 'A4');
	$pdf->AddMBFont(GOTHIC, 'SJIS');
	$pdf->SetAutoPageBreak(true, 10);
	$pdf->AliasNbPages();

	$pdf->AddPage();

	// 一行の高さ
	$height = 3.9 ;

	foreach ($aryData as $row)
	{
		//-------------------------------------------------------------
		// １行目
		//-------------------------------------------------------------
		// 申請者タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('申請者');
		$pdf->Cell(24, $height * 2 , $val, "LTRB", 0, "C" , 1);

		// 申請者カナ
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['NameSeiKana']);
		$pdf->Cell(108, $height, $val, "LTR" , 0, "L", 1);

		// 利用者IDタイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('利用者ID');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 利用者ID  UserID
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = $row['UserID'];
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// 表示名称タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('表示名称');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 表示名称 HyoujiMei
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['HyoujiMei']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ２行目
		//-------------------------------------------------------------
		// 空欄
		$pdf->Cell(24, $height, '', 0, 0, "L", 0);

		// 申請者
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['NameSei']);
		$pdf->Cell(108, $height, $val, "LRB" , 0, "L", 1);

		// 利用目的 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		//$val = utf2sjis('利用目的');
		//$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);
		$pdf->Cell(24, $height, '', "LTRB", 0, "C", 1);

		// 利用目的
		$val = '';
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		//$val = utf2sjis( $val );
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// 登録区分タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('登録区分');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 登録区分 KojinDanKbnName
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['KojinDanKbnName']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ３行目
		//-------------------------------------------------------------
		// 代表者 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('代表者');
		$pdf->Cell(24, $height * 3, $val, "LTRB", 0, "C", 1);

		// 代表者カナ
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['HeadNameSeiKana']);
		$pdf->Cell(108, $height, $val, "LTR", 0, "L", 1);

		// 連絡先 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('連絡先');
		$pdf->Cell(24, $height * 3, $val, "LTRB", 0, "C", 1);

		// 郵便番号 PostNo
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = $row['PostNo'];
		$pdf->Cell(108, $height, $val, "LTR", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ４行目
		//-------------------------------------------------------------
		// 代表者 タイトル の下の空欄（一つ目）
		$pdf->Cell(24, $height, "" , 0, 0, "L", 0);

		// 代表者
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['HeadNameSei']);
		$pdf->Cell(108, $height, $val, "LR", 0, "L", 1);

		// 連絡先 タイトル の下の空欄（一つ目）
		$pdf->Cell(24, $height, "" , 0, 0, "L", 0);

		// 住所
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['Adr']);
		$pdf->Cell(108, $height, $val, "LR", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ５行目
		//-------------------------------------------------------------
		// 代表者 タイトル の下の空欄（２つ目）
		$pdf->Cell(24, $height, '', 0, 0, "L", 0);

		// 代表者の誕生日 
		$val = $row['Bday'];
		if (strlen($val) > 0) $val .= ' 生'; 
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($val);
		$pdf->Cell(108, $height, $val, "LRB", 0, "L", 1);

		// 連絡先 タイトル の下の空欄（２つ目）
		$pdf->Cell(24, $height, '', 0, 0, "L", 0);

		// 電話・メールアドレス
		$val = '電話:'.$row['TelNo1'].'	メール:'.$row['MailAdr'] ;
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($val);
		$pdf->Cell(108, $height, $val, "LRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ６行目
		//-------------------------------------------------------------
		// 減免情報 タイトル
		$val = "減免情報";
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis( $val );
		$pdf->Cell(24, $height * 2, $val, "LTRB", 0, "C", 1);

		// 減免情報 Genmen
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['Genmen']);
		$pdf->Cell(108, $height * 2, $val, "LTR", 0, "L", 1);

		// 学割適用 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		//$val = utf2sjis('学割適用');
		//$pdf->Cell(24, $height , $val, "LTRB", 0, "C", 1);
		$pdf->Cell(24, $height , '', "LTRB", 0, "C", 1);

		// 学割適用
		$val = '';
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		//$val = utf2sjis($val);
		$pdf->Cell(108, $height, '', "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ７行目
		//-------------------------------------------------------------
		// 減免情報 タイトル の下の空欄（１つだけ）
		$pdf->Cell(24, $height, '', 0, 0, "L", 0);

		// 減免情報 タイトル の下の空欄（１つだけ）
		$pdf->Cell(108, $height * 2, "" , 0, 0, "L", 0);

		// 学割適用 の下のセル枠あり空欄
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$pdf->Cell(24, $height , '', "LTRB", 0, "C", 1);

		// 学割適用タイトル の下のセル枠あり空欄
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$pdf->Cell(108, $height, '', "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ８行目
		//-------------------------------------------------------------
		// 登録状態タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('登録状態');
		$pdf->Cell(24, $height , $val, "LTRB", 0, "C" , 1);

		// 登録状態
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['UserJyoutaiKbnName']);
		$pdf->Cell(108, $height, $val, "LTRB", 0, "L", 1);

		// 申請日 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('申請日');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 申請日
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['FirstApplyDate']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// 有効期限タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('有効期限');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 有効期限 UserLimit
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['UserLimit']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// ９行目
		//-------------------------------------------------------------
		// 停止/抹消事由タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('停止/抹消事由');
		$pdf->Cell(24, $height * 2 , $val, "LTRB", 0, "L" , 1);

		// 停止/抹消事由
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['StopEraseJiyu']);
		$pdf->Cell(108, $height * 2, $val, "LTRB", 0, "L", 1);

		// 登録日 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('登録日');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 登録日
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['FirstEntryDate']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// 登録日 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('最終更新日');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 登録日
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['UpdDate']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		//-------------------------------------------------------------
		// １０行目
		//-------------------------------------------------------------
		// 空欄
		$pdf->Cell(132, $height, '', 0, 0, "L", 0);

		// 登録部署 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('登録部署');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 登録部署
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['TourokuBushoName']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// 登録者 タイトル
		$pdf->SetFillColor(200, 200, 200); // 塗りつぶしの色
		$val = utf2sjis('登録者');
		$pdf->Cell(24, $height, $val, "LTRB", 0, "C", 1);

		// 登録者
		$pdf->SetFillColor(255, 255, 255); // 塗りつぶしの色
		$val = utf2sjis($row['UpdName']);
		$pdf->Cell(42, $height, $val, "LTRB", 0, "L", 1);

		// １行終わり。
		$pdf->Ln();

		// レコード間の隙間
		$pdf->Cell(1, 2, '');
		$pdf->Ln();
	}

	$pdf->Output();
}

function utf2sjis($str)
{
	return mb_convert_encoding($str, "SJIS-win", "UTF-8");
}
?>
