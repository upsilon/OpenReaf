<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  システム共通クラス
 *
 *  system_common.class.php
 */

class system_common
{
	protected $con = null;
	private $lcd = '';
	private	$JobName = array('Internet' => 'インターネット',
				'Mobile' => '携帯',
				'SmartPhone' => 'スマートフォン',
				'Kiosk' => 'キオスク端末',
				'Phone' => '電話',
				'iCollect' => 'i-コレクト',
				'LotBatch' => '抽選',
				'MailBatch' => '抽選結果メール',
				'CancelBatch' => '自動取消',
				'ResumeBatch' => '利用停止自動解除',
				'UpdateBatch' => '自動更新');

	//-------------------------------------------------------------------
	// コンストラクタ
	//-------------------------------------------------------------------
	function __construct(&$con)
	{
		$this->con = $con;
		$this->lcd = _CITY_CODE_;
	}

	//------------------------------------------------------------
	// 自治体コード取得
	//------------------------------------------------------------
	function getLocalGovCode()
	{
		return _CITY_CODE_;
	}

	//-------------------------------------------------------------------
	// HHiiss形式時間を配列で変換する
	//-------------------------------------------------------------------
	function make_time_array($time)
	{
		$aReturn = array();
		$aReturn['H'] = substr($time,0,2);
		$aReturn['i'] = substr($time,2,2);
		$aReturn['s'] = substr($time,4,2);
		return $aReturn;
	}

	//-------------------------------------------------------------------
	// 時間を「HH:ii」形式で返却する    
	//-------------------------------------------------------------------
	function getTimeView($time)
	{
		if (!$time) return '';
		$aTime = $this->make_time_array($time);
		return $aTime['H'].':'.$aTime['i'];
	}

	function timeFormat($time)
	{
		return sprintf('%d:%s', intval(substr($time, 0, 2)), substr($time, 2, 2));
	}

	//-------------------------------------------------------------------
	// 午前00時00分の形式で出力する
	//-------------------------------------------------------------------
	function getTimeJpView($time)
	{
		$aTime = $this->make_time_array($time);
		$hour = intval($aTime['H']);
		$return = '';
		if ($hour < 12) {
			$return = sprintf('午前 %2d時%s分', $hour, $aTime['i']);
		} else {
			$hour -= 12;
			$return = sprintf('午後 %2d時%s分', $hour, $aTime['i']);
		}
		return $return;
	}

	//-------------------------------------------------------------------
	// 日付を「YYYY/MM/DD (曜日)」 形式で返却する
	//-------------------------------------------------------------------
	function getDateView($date, $dow=true)
	{
		global $aWeekJ;

		if (!$date) return '';
		$tmp = strtotime($date);
		$w = date('w', $tmp);
		$W = $dow ? ' ('.$aWeekJ[$w].')' : '';
		return date('Y/m/d', $tmp).$W;
	}

	function get_date_view($time, $dow=true, $with_time=1)
	{
		global $aWeekJ;

		if ($time <= 0) return '';
		$w = date('w', $time);
		$W = $dow ? ' ('.$aWeekJ[$w].') ' : '';
		$t = '';
		switch ($with_time) {
			case 1:
			 	$t = date('H:i', $time);
				break;
			case 2:
				$t = date('H:i:s', $time);
				break;
		}
		return date('Y/m/d', $time).$W.$t;
	}

	//-------------------------------------------------------------------
	// 年号年月日を取得する「平成YY年M月D日(曜日)」の形式
	//-------------------------------------------------------------------
	function put_wareki_date($date, $weekFlag=false)
	{
		global $aWeekJ;

		if (!$date)  return '　　年　月　日';

		$tmp = strtotime($date);
		$w = date('w', $tmp);
		$Y = date('Y', $tmp);
		$str = $this->getNengouName($Y).$this->getNengouYear($Y)
		.sprintf('年%2s月%2s日', date('n', $tmp), date('j', $tmp));
		if ($weekFlag) $str .= ' ('.$aWeekJ[$w].')';

		return $str;
	}

	function date4lang($date, $lang='ja')
	{
		global $aWeekJ;

		if (!$date)  return '-';

		$tmp = strtotime($date);

		if ($lang == 'en') return date('D, jS M Y', $tmp);

		$w = date('w', $tmp);
		$Y = date('Y', $tmp);
		return $this->getNengouName($Y).$this->getNengouYear($Y)
		.sprintf('年%2s月%2s日', date('n', $tmp), date('j', $tmp))
		.' ('.$aWeekJ[$w].')';
	}

	//-------------------------------------------------------------------
	// 年号年を取得する
	//-------------------------------------------------------------------
	function getNengouYear($year)
	{
		// 平成
		if ($year > 1988) $year -= 1988;
		return  $year;
	}

	//-------------------------------------------------------------------
	// 年号名を取得する
	//-------------------------------------------------------------------
	function getNengouName($year)
	{
		$stNengou = '昭和';
		if ($year > 1988) $stNengou = '平成';
		return $stNengou;
	}

	//-------------------------------------------------------------------
	// 表示用年号を取得する
	//-------------------------------------------------------------------
	function getNengouView($year)
	{
		return $this->getNengouName($year).$this->getNengouYear($year);
	}

	//-------------------------------------------------------------------
	// 4ヶ月分の時間配列を取得する
	//-------------------------------------------------------------------
	function make_month_array($format, $n=4)
	{
		$curMonth = date('n');
		$curYear = date('Y');

		$monthArr = array();
		for ($i = 0; $i < $n; ++$i)
		{
			$tmp = mktime(0, 0, 0, $curMonth + $i, 1, $curYear);
			$ym = date('Ym', $tmp);
			$monthArr[$ym] = array(	'key' => $i,
						'Label' => date($format, $tmp));
		}
		return $monthArr;
	}

	//-------------------------------------------------------------------
	// システムパラメータ取得
	//-------------------------------------------------------------------
	function get_system_parameters()
	{
		$sql = 'SELECT * FROM m_systemparameter WHERE localgovcode=?';
		$aWhere = array($this->lcd);
		$res = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
		// 表示用時間セット
		$res['AMFromView'] = $this->getTimeView($res['amfrom']);
		$res['PMFromView'] = $this->getTimeView($res['pmfrom']);
		$res['NTFromView'] = $this->getTimeView($res['ntfrom']);
		$res['AMToView'] = $this->getTimeView($res['amto']);
		$res['PMToView'] = $this->getTimeView($res['pmto']);
		$res['NTToView'] = $this->getTimeView($res['ntto']);
		return $res;
	}

	//------------------------------------------------------------
	// コード名称マスタ情報取得
	//------------------------------------------------------------
	function get_codename_options($kbnName='YoyakuKbn')
	{
		$aWhere = array($this->lcd, $kbnName);
		$sql = 'SELECT code, codename, upddate
			FROM m_codename WHERE localgovcode=? AND codeid=?
			ORDER BY code, upddate';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 施設名取得
	//------------------------------------------------------------
	function get_shisetsu_name($scd)
	{
		$sql = 'SELECT shisetsuname FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=?';
		$aWhere = array($this->lcd, $scd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 施設名の配列を取得
	//------------------------------------------------------------
	function get_shisetsu_name_array()
	{
		$aWhere = array($this->lcd);
		$sql = 'SELECT shisetsucode, shisetsuname, shisetsuskbcode FROM m_shisetsu WHERE localgovcode=? ORDER BY shisetsuskbcode';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 施設情報取得
	//------------------------------------------------------------
	function get_shisetsu_data($scd)
	{
		$sql = 'SELECT * FROM m_shisetsu WHERE localgovcode=? AND shisetsucode=?';
		$aWhere = array($this->lcd, $scd);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 室場情報取得
	//------------------------------------------------------------
	function get_shitsujyo_info($scd, $rcd)
	{
		$sql = "SELECT c.shisetsuclasscode, c.shisetsuclassname,";
		$sql.= " s.shisetsuname, s.showdanjyoninzuflg, s.shinsaflg, s.fractionflg,";
		$sql.= " t.shitsujyoname, t.genmen, t.genapplyflg, t.extracharge, t.teiin, t.shitsujyoKbn";
		$sql.= " FROM m_shitsujyou t";
		$sql.= " JOIN m_shisetsu s";
		$sql.= " USING (localgovcode, shisetsucode)";
		$sql.= " JOIN m_shisetsuclass c";
		$sql.= " USING (localgovcode, shisetsuclasscode)";
		$sql.= " WHERE t.localgovcode=? AND t.shisetsucode=? AND t.shitsujyocode=?";

		$aWhere = array($this->lcd, $scd, $rcd);

		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 室場名を取得
	//------------------------------------------------------------
	function get_shitsujyo_name($scd, $rcd)
	{
		$sql = 'SELECT shitsujyoname FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?';
		$aWhere = array($this->lcd, $scd, $rcd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 室場名の配列を取得
	//------------------------------------------------------------
	function get_shitsujyo_name_array($scd, $without_fuzoku=false)
	{
		$aWhere = array($this->lcd, $scd);
		$sql = "SELECT shitsujyocode, shitsujyoname, shitsujyoskbcode FROM m_shitsujyou WHERE localgovcode=? AND shisetsucode=? AND shitsujyokbn<>'4'";
		if ($without_fuzoku) $sql.= " AND shitsujyokbn<>'3'";
		$sql.= ' ORDER BY shitsujyoskbcode';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 組合せ名取得
	//------------------------------------------------------------
	function get_combi_name($scd, $rcd, $cno)
	{
		$sql = 'SELECT DISTINCT combiname FROM m_mencombination WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=? AND combino=?';
		$aWhere = array($this->lcd, $scd, $rcd, $cno);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 組合せ名の配列を取得
	//------------------------------------------------------------
	function get_combi_name_array($scd='')
	{
		$aWhere = array($this->lcd);
		$sql = 'SELECT DISTINCT shisetsucode, shitsujyocode, combino, combiname FROM m_mencombination WHERE localgovcode=?';
		if ($scd != '') {
			$sql.= ' AND shisetsucode=?';
			array_push($aWhere, $scd);
		}
		$sql.= ' ORDER BY shisetsucode, shitsujyocode, combino';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]][$val[1]][$val[2]] = $val[3];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 組合せ番号から面コードを取得
	//------------------------------------------------------------
	function combino2mencode($scd, $rcd, $cno)
	{
		if ($cno == 0) return array('ZZ');

		$aWhere = array($this->lcd, $scd, $rcd, $cno);
		$sql = "SELECT mencode FROM m_mencombination
			WHERE localgovcode=? AND shisetsucode=?
			AND shitsujyocode=? AND combino=?";
		$res = $this->con->getAll($sql, $aWhere, DB_FETCHMODE_ASSOC);
		if (count($res) == 0) return array();

		$MenCode = array();
		foreach ($res as $val) $MenCode[] = $val['mencode'];
		unset($res);
		return $MenCode;
	}

	//------------------------------------------------------------
	// 抽選確定フラグの配列を取得
	//------------------------------------------------------------
	function get_fixflg_array($scd='')
	{
		$aWhere = array($this->lcd);
		$sql = 'SELECT shisetsucode, shitsujyocode, fixflg FROM m_yoyakuscheduleptn WHERE localgovcode=?';
		if ($scd != '') {
			$sql.= ' AND shisetsucode=?';
			array_push($aWhere, $scd);
		}
		$sql.= ' ORDER BY shisetsucode, shitsujyocode';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]][$val[1]] = $val[2];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 目的名取得
	//------------------------------------------------------------
	function get_purpose_name($pcd)
	{
		$sql = 'SELECT mokutekiname FROM m_mokuteki WHERE localgovcode=? AND mokutekicode=?';
		$aWhere = array($this->lcd, $pcd);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 利用目的の配列を取得
	//------------------------------------------------------------
	function get_purpose_name_array()
	{
		$aWhere = array($this->lcd);
		$sql = 'SELECT mokutekicode, mokutekiname';
		$sql.= ' FROM m_mokuteki WHERE localgovcode=?';
		$sql.= ' ORDER BY mokutekicode';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = array();
		foreach ($res as $val) $recs[$val[0]] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 減免情報取得
	//------------------------------------------------------------
	function get_genmen_info($id, $code)
	{
		$sql = 'SELECT koteigenname AS genname, rate FROM m_genmen
			WHERE localgovcode=? AND koteigencode=?';
		if ($id == 2) {
			$sql = 'SELECT singenname AS genname, rate FROM m_singenmen
				WHERE localgovcode=? AND singencode=?';
		}
		$aWhere = array($this->lcd, $code);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 割増情報取得
	//------------------------------------------------------------
	function get_extracharge_info($code)
	{
		$sql = 'SELECT extraname, rate FROM m_extracharge
			WHERE localgovcode=? AND extracode=?';
		$aWhere = array($this->lcd, $code);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 料金区分名取得
	//------------------------------------------------------------
	function get_UseKbn_name($code)
	{
		$sql = 'SELECT feekbnname FROM m_feekbn WHERE localgovcode=? AND feekbn=?';
		$aWhere = array($this->lcd, $code);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 予約区分名取得
	//------------------------------------------------------------
	function get_YoyakuKbn_name($code)
	{
		$sql = 'SELECT codename FROM m_codename WHERE localgovcode=? AND codeid=? AND code=?';
		$aWhere = array($this->lcd, 'YoyakuKbn', $code);
		return $this->con->getOne($sql, $aWhere);
	}

	//------------------------------------------------------------
	// 本予約区分名取得
	//------------------------------------------------------------
	function get_HonYoyakuKbn_name($code)
	{
		global $aHonYoyakuKbn;

		return isset($aHonYoyakuKbn[$code]) ? $aHonYoyakuKbn[$code] : '';
	}

	//------------------------------------------------------------
	// 本予約区分名取得
	//------------------------------------------------------------
	function get_ShinsaKbn_name($code)
	{
		global $aShinsaKbn;

		return isset($aShinsaKbn[$code]) ? $aShinsaKbn[$code] : '';
	}

	//------------------------------------------------------------
	// 支払期限メッセージ出力
	//------------------------------------------------------------
	function get_pay_day($scd, $rcd, $yoyakuNum)
	{
		$sql = "SELECT feepaylimtkbn, feepaylimtday FROM m_yoyakuscheduleptn
			WHERE localgovcode=? AND shisetsucode=? AND shitsujyocode=?";
		$aWhere = array($this->lcd, $scd, $rcd);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		$feePayLimitDay = intval($row['feepaylimtday']);
		$feePayLimitKbn = intval($row['feepaylimtkbn']);
		$msg = '';
		switch ($feePayLimitKbn) {
			case 1: $msg = ($feePayLimitDay == 0) ? 'ご利用日当日まで' : 'ご利用日の'.$feePayLimitDay.'日前まで'; break;
			case 2: $msg = 'ご利用日当日のご利用前'; break;
			case 3: $msg = 'ご利用日当日のご利用後'; break;
			case 4: $msg = ($feePayLimitDay == 0) ? 'ご利用日当日' : 'ご利用日から'.$feePayLimitDay.'日以内'; break;
			case 5:
			case 6:
				$sql = 'SELECT shisetsupaylimitdate FROM t_yoyaku WHERE localgovcode=? AND yoyakunum=?';
				$res = $this->con->getOne($sql, array($this->lcd, $yoyakuNum));
				if ($res) {
					$y = intval(substr($res, 0, 4));
					$m = intval(substr($res, 4, 2));
					$d = intval(substr($res, 6, 2));
					$msg = $y.'年'.$m.'月'.$d.'日まで';
				}
				break;
		}
		return $msg;
	}

       //------------------------------------------------------------
	// 職員名取得
	//------------------------------------------------------------
	function get_staff_name($id)
	{
		if (isset($this->JobName[$id])) return $this->JobName[$id];

		$sql = 'SELECT staffname FROM m_staff
			WHERE localgovcode=? AND staffid=?';
		$aWhere = array($this->lcd, $id);
		$tmpID = $this->con->getOne($sql, $aWhere);
		if ($tmpID == '') $tmpID = $id;

		return $tmpID;
	}

       //------------------------------------------------------------
	// 職員名の配列を取得
	//------------------------------------------------------------
	function get_staff_name_array()
	{
		$aWhere = array($this->lcd);
		$sql = 'SELECT staffid, staffname FROM m_staff
			WHERE localgovcode=? ORDER BY staffid';
		$res = $this->con->getAll($sql, $aWhere);
		$recs = $this->JobName;
		foreach ($res as $val) $recs[strval($val[0])] = $val[1];
		unset($res);
		return $recs;
	}

	//------------------------------------------------------------
	// 利用者情報取得
	//------------------------------------------------------------
	function get_user_data($uid)
	{
		$sql = 'SELECT * FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 利用者状態取得
	//------------------------------------------------------------
	function set_user_status($uid, $usedate, $update_flag=false)
	{
		$userjyoutaikbn_arr = array('未承認', '通常', '利用停止', '登録抹消', '不承');

		$pre_str = '* ';
		$pre_str.= $update_flag ? '変更' : '新規予約';
		$pre_str.= '不可 ';

		$sql = 'SELECT userid, userareakbn, namesei, nameseikana, usekbn,
			yoyakukyokaflg, userlimit, userjyoutaikbn, stoperasedate
			FROM m_user WHERE localgovcode=? AND userid=?';
		$aWhere = array(_CITY_CODE_, $uid);
		$row = $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);

		if (empty($row)) {
			$row = array();
			$row['userid'] = $uid;
			$row['userareakbn'] = '01';
			$row['namesei'] = '';
			$row['nameseikana'] = '';
			$row['usekbn'] = '';
			$row['userjyoutaikbn'] = '';
			$row['userjyoutai'] = $pre_str.'(該当する利用者IDはありません。)';
			$row['usecheckflag'] = '0';
		} elseif (empty($row['yoyakukyokaflg'])) {
			$row['userjyoutai'] = $pre_str.'(該当会員の属性より予約できません。)';
			$row['usecheckflag'] = '0';
		} elseif ($row['userjyoutaikbn'] == '0' || $row['userjyoutaikbn'] == '4') {
			$row['userjyoutai'] = $pre_str.'('.$userjyoutaikbn_arr[$row['userjyoutaikbn']].'のユーザーです。)';
			$row['usecheckflag'] = '0';
		} elseif ($row['userjyoutaikbn'] == '2' || $row['userjyoutaikbn'] == '3') {
			$status_label = $userjyoutaikbn_arr[$row['userjyoutaikbn']];
			$row['userjyoutai'] = '';
			$row['usecheckflag'] = '1';
			if ($row['stoperasedate'] <= date('Ymd')) {
				$row['userjyoutai'] = $pre_str.'('.$status_label.'のユーザーです。)';
				$row['usecheckflag'] = '0';
			} elseif ($usedate && $row['stoperasedate'] <= $usedate) {
				$row['userjyoutai'] = $pre_str.'('.$status_label.'のユーザーです。)';
				$row['usecheckflag'] = '0';
			}
		} elseif ($row['userlimit'] && $row['userlimit'] < date('Ymd')) {
			$row['userjyoutai'] = $pre_str.'(利用者登録期限切れです。)';
			$row['usecheckflag'] = '0';
		} elseif ($row['userlimit'] && $usedate && $row['userlimit'] < $usedate) {
			$row['userjyoutai'] = $pre_str.'(利用日が利用者登録期限を過ぎています。)';
			$row['usecheckflag'] = '0';
		} else {
			$row['userjyoutai'] = '';
			$row['usecheckflag'] = '1';
		}
		return $row;
	}

	//------------------------------------------------------------
	// 未登録者情報取得
	//------------------------------------------------------------
	function get_unregisted_user_info($YoyakuNum)
	{

		$sql = 'SELECT username AS unreg_name, address AS unreg_address,
			telno AS unreg_tel, contactno AS unreg_contact
			FROM t_unregister WHERE yoyakunum=?';
		$aWhere = array($YoyakuNum);
		return $this->con->getRow($sql, $aWhere, DB_FETCHMODE_ASSOC);
	}

	//------------------------------------------------------------
	// 未登録者名取得
	//------------------------------------------------------------
	function get_unregisted_user_name($YoyakuNum)
	{
		$sql = 'SELECT username FROM t_unregister WHERE yoyakunum=?';
		$aWhere = array($YoyakuNum);
		return $this->con->getOne($sql, $aWhere);
	}
}
?>
