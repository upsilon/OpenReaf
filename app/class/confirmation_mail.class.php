<?php
/*
 *  Copyright 2010-2012 OpenReaf Project Team
 *  License GPLv2
 *
 *  confirmation_mail.class.php
 */
require OPENREAF_ROOT_PATH.'/app/class/smtp.class.php';

class confirmation_mail
{
	private $con;
	private $type;
	private $cancel;
	private $lcd = _CITY_CODE_;
	private $sysinfo = array();

	function __construct(&$con, $type, $cancel)
	{
		$this->con = $con;

		$this->type = "予約";
		if ($type == 1) {
			$this->type = "抽選";
		}
		$this->cancel = '';
		if ($cancel != 0) {
			$this->cancel = "取消";
		}

		$this->sysinfo = $this->get_system_mail_info();
	}

	function get_system_info()
	{
		return $this->sysinfo;
	}

	function make_body_data(&$dataset)
	{
		$rec = array();
		$rec = $dataset;
		$rec['LocalGovName'] = $this->sysinfo['localgovname'];
		$rec['TopMenuURL'] = $this->sysinfo['topmenuurl'];

		$sql = "SELECT b.bushoname, a.telno21, a.telno22, a.telno23
			FROM m_shisetsu a
			LEFT JOIN m_busho b
			ON a.localgovcode=b.localgovcode AND a.rangebusyocode=b.bushocode
			WHERE a.localgovcode=? AND a.shisetsucode=?";
		$where = array($this->lcd, $dataset['shisetsucode']);
		$row = $this->con->getRow($sql, $where, DB_FETCHMODE_ASSOC);
		$rec['BushoName'] = '';
		$rec['ShisetsuTel'] = '';
		if (!empty($row)) {
			$rec['BushoName'] = $row['bushoname'];
			if ($row['telno23'] != '') {
				$rec['ShisetsuTel'] = $row['telno21'].'-'.$row['telno22'].'-'.$row['telno23'];
			}
		}

		$rec['type'] = $this->type;
		$rec['cancel'] = $this->cancel;

		return $rec;
	}

	function make_subject()
	{
		return '施設予約システムの'.$this->type.'申し込み'.$this->cancel.'のお知らせ';
	}

	function send_mail($dest, $subject, $body, $type)
	{
		$host = '';
		$port = 0;
		$userName = '';
		$passWord = '';
		$from = '';
		$bcc = '';
		$fromname = '';
		$logdir = OPENREAF_ROOT_PATH.'/var/log/'.$type;
		if (!empty($this->sysinfo)) {
			$host = $this->sysinfo['mailhost'];
			$port = $this->sysinfo['mailhostport'];
			$userName = $this->sysinfo['mailhostuserid'];
			$passWord = $this->sysinfo['mailhostuserpass'];
			$from = $this->sysinfo['mailfromaddr'];
			$bcc = $this->sysinfo['mailbccaddr'];
			$fromname = $this->sysinfo['mailfromname'];
		}
		if ($host == '' || $port == 0) {
			return false;
		}

		$mail = new Email($host, $port, $logdir);
		if ($userName != '') {
			if (!$mail->Login($userName, $passWord)) return false;
		}
		if (!$mail->setTo($dest)) return false;

		$mail->setFromName($fromname);
		$mail->setFrom($from);
		if ($bcc) {
			$mail->setBCC($bcc);
		}
		$mail->setSubject($subject);
		$mail->setText($body);
		$mail->send();

		return $mail->Quit();
	}

	function get_mail_info($UserID)
	{
		$sql = "SELECT namesei, mailsendflg, mailadr FROM m_user WHERE localgovcode=? AND userid=?";
		$where = array($this->lcd, $UserID);
		return $this->con->getRow($sql, $where, DB_FETCHMODE_ASSOC);
	}

	function get_system_mail_info()
	{
		$sql = "SELECT localgovname,topmenuurl,
			mailhost,mailhostport,mailhostuserid,
			mailhostuserpass,mailfromaddr,mailfromname,mailbccaddr
			FROM m_systemparameter WHERE localgovcode=?";
		return $this->con->getRow($sql, array($this->lcd), DB_FETCHMODE_ASSOC);
	}
}
?>
