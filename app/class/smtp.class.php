<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  smtp.class.php
 */

class Email
{
	private $SMTP_PORT = 587;
	private $mailTo = '';
	private $mailCC = '';
	private $mailBCC = '';
	private $mailFrom = '';
	private $mailSubject = '';
	private $mailText = '';
	private $mailHTML = '';
	private $mailAttachments = '';
	private $CRLF = "\r\n";
	private $charset = "ISO-2022-JP";
	private $host = '';
	private $port = 0;
	private $smtp_conn = null;
	private $error = null;
	private $debug = 0;
	private $mailFromName = '';
	private $logfile = '';

	function __construct($host, $port, $logdir='')
	{
		$this->logfile = "../log/mail_".date('Ym').".log";
		$this->host = $host;
		$this->port = $port;

		if ($logdir != '') $this->setLogDir($logdir);

		if ($this->Connect($this->host, $this->port)) $this->Hello();
	}

	function setLogDir($dir)
	{
		$this->logfile = $dir.'/'.basename($this->logfile);
	}

	function Connect($host, $port=0, $tval=30)
	{
		$this->error = null;
		if ($this->connected()) {
			$this->error =array("error" => "Already connected to a server");
			if ($this->debug == 1) $this->write_log($this->error["error"]);
			return false;
		}
		if (empty($port)) {
			$port = $this->SMTP_PORT;
		}

		$this->smtp_conn = fsockopen($host, $port, $errno, $errstr, $tval);
		if (empty($this->smtp_conn)) {
			$this->error = array(
				"error" => "Failed to connect to server",
				"errno" => $errno,
				"errstr" => $errstr);
			$this->write_log($this->error["error"]." errno=".$errno." msg=".$errstr);
			return false;
		}
		$announce = $this->get_lines();
		return true;
	}

	function Connected()
	{
		if (!empty($this->smtp_conn)) {
			$sock_status = socket_get_status($this->smtp_conn);
			if ($sock_status["eof"]) {
				$this->Close();
				return false;
			}
			return true;
		}
		return false;
	}

	function setTo($inAddress)
	{
		$address = preg_replace('/'.$this->CRLF.'/', '', $inAddress);
		$addressArray = explode(',', $address);
		foreach ($addressArray as $val) {
			if ($this->checkEmail($val) == false) {
				$this->write_log('Invalid destination address : '.$val);
				return false;
			}
		}
		$this->mailTo = $address;
		return true;
	}

	function setCC($inAddress)
	{
		$address = preg_replace('/'.$this->CRLF.'/', '', $inAddress);
		$addressArray = explode(',', $address);
		foreach ($addressArray as $val) {
			if ($this->checkEmail($val) == false) {
				$this->write_log('Invalid CC address : '.$val);
				return false;
			}
		}
		$this->mailCC = $address;
		return true;
	}

	function setBCC($inAddress)
	{
		$address = preg_replace('/'.$this->CRLF.'/', '', $inAddress);
		$addressArray = explode(',', $address);
		foreach ($addressArray as $val) {
			if ($this->checkEmail($val) == false) {
				$this->write_log('Invalid BCC address : '.$val);
				return false;
			}
		}
		$this->mailBCC = $address;
		return true;
	}

	function setFrom($inAddress)
	{
		if ($this->checkEmail($inAddress)) {
			$this->mailFrom = preg_replace('/'.$this->CRLF.'/', '', $inAddress);
			return true;
		}
		return false;
	}

	function setFromName($inAddress)
	{
		$this->mailFromName = $inAddress;
		if (strlen(trim($inAddress)) > 0) {
			$this->mailFromName = preg_replace('/'.$this->CRLF.'/', '', $this->mailFromName);
			$this->mailFromName = $this->encodeJIS(trim($this->mailFromName));
			$this->mailFromName = $this->encode64($this->mailFromName);
		}
	}

	function setSubject($inSubject)
	{
		if (strlen(trim($inSubject)) > 0) {
			$this->mailSubject = preg_replace('/'.$this->CRLF.'/', '', $inSubject);
			$this->mailSubject = $this->encodeJIS(trim($this->mailSubject));
			$this->mailSubject = $this->encode64($this->mailSubject);
			return true;
		}
		return false;
	}

	function setText($inText)
	{
		if (strlen(trim($inText)) > 0) {
			$this->mailText = $inText;
			return true;
		}
		return false;
	}

	function setHTML($inHTML)
	{
		if (strlen(trim($inHTML)) > 0) {
			$this->mailHTML = $inHTML;
			return true;
		}
		return false;
	}

	function setAttachments($inAttachments)
	{
		if (strlen(trim($inAttachments)) > 0) {
			$this->mailAttachments = $inAttachments;
			return true;
		}
		return false;
	}

	function checkEmail($inAddress)
	{
		return (preg_match("/^[^@ ]+@([a-zA-Z0-9-]+.)+([a-zA-Z0-9-]{2}|net|com|gov|mil|org|edu|int)$/", $inAddress));
	}

	function getRandomBoundary($offset = 0)
	{
		srand(time()+$offset);
		return ( "----".(md5(rand())));
	}

	function getContentType($inFileName)
	{
		$inFileName = basename($inFileName);
		if (strrchr($inFileName, ".") == false) {
			return "application/octet-stream";
		}
		$extension = strtolower(strrchr($inFileName, "."));
		switch($extension)
		{
			case ".gif": return "image/gif";
			case ".gz": return "application/x-gzip";
			case ".htm": return "text/html";
			case ".html": return "text/html";
			case ".jpg": return "image/jpeg";
			case ".tar": return "application/x-tar";
			case ".txt": return "text/plain";
			case ".zip": return "application/zip";
			default: return "application/octet-stream";
		}
		return "application/octet-stream";
	}

	function formatTextHeader()
	{
		$outHeader = "Content-Type: text/plain; charset=".$this->charset." ".$this->CRLF;
		$outHeader.= "Content-Transfer-Encoding: 7bit".$this->CRLF.$this->CRLF;
		return $outHeader;
	}

	function formatHTMLHeader()
	{
		$outHeader = "Content-Type: text/html; charset=".$this->charset." ".$this->CRLF.$this->CRLF;
		return $outHeader;
	}

	function formatAttachmentHeader($inFileLocation)
	{
		$outHeader = '';
		$contentType = $this->getContentType($inFileLocation);
		if (preg_match("/text/", $contentType)) {
			$outHeader .= "Content-Type: ".$contentType. ";".$this->CRLF;
			$outHeader .= ' name="'.basename($inFileLocation). '"'. $this->CRLF;
			$outHeader .= "Content-Transfer-Encoding: 7bit".$this->CRLF;
			$outHeader .= "Content-Disposition: attachment;".$this->CRLF;
			$outHeader .= ' filename="'.basename($inFileLocation). '"'. $this->CRLF.$this->CRLF;
			$textFile = fopen($inFileLocation, "r");
			while(!feof($textFile)) {
				$outHeader .= fgets($textFile,1000);
			}
			$outHeader .= $this->CRLF;
		} else {
			$outHeader .= "Content-Type: ".$contentType. ";".$this->CRLF;
			$outHeader .= ' name="'.basename($inFileLocation). '"'. $this->CRLF;
			$outHeader .= "Content-Transfer-Encoding: base64".$this->CRLF;
			$outHeader .= "Content-Disposition: attachment;".$this->CRLF;
			$outHeader .= ' filename="'.basename($inFileLocation). '"'. $this->CRLF.$this->CRLF;
			exec("uuencode -m $inFileLocation nothing_out", $returnArray);
			$num = count($returnArray);
			for ($i=1; $i<$num; ++$i)
			{
				$outHeader .= $returnArray[$i]. $this->CRLF;
			}
		}
		return $outHeader;
	}

	function send()
	{
		$mailHeader = '';
		if ($this->mailTo != '') {
			$mailHeader .= "To: ".$this->mailTo.''.$this->CRLF;
		} else {
			return false;
		}
		if ($this->mailCC != '') {
			$mailHeader .= "Cc: ".$this->mailCC.''.$this->CRLF;
		}
		if ($this->mailFrom != '') {
			$mailHeader .= "From: ".$this->mailFromName."<".$this->mailFrom.">".$this->CRLF;
			$mailHeader .= "Return-Path: ".$this->mailFrom.''.$this->CRLF;
		}
		if ($this->mailSubject != '') {
			$mailHeader .= "Subject: ".$this->mailSubject.''.$this->CRLF;
		}
		$mailHeader .= "Date: ".date('r').''.$this->CRLF;

		if ($this->mailText != '' && $this->mailHTML == '' && $this->mailAttachments == '') {
			$mailHeader .= $this->formatTextHeader();
			$mailHeader .= $this->mailText;
			return $this->SendMail($mailHeader);
		} elseif (($this->mailText != '' || $this->mailHTML != '') && $this->mailAttachments == '') {
			$bodyBoundary = $this->getRandomBoundary();

			$mailHeader .= "MIME-Version: 1.0".$this->CRLF;
			$mailHeader .= "Content-Type: multipart/alternative;".$this->CRLF;
			$mailHeader .= ' boundary="'.$bodyBoundary. '"';
			$mailHeader .= $this->CRLF.$this->CRLF.$this->CRLF;

			$mailHeader .= "--".$bodyBoundary. $this->CRLF;
			$mailHeader .= $this->formatTextHeader();
			$mailHeader .= $this->mailText. $this->CRLF;
			$mailHeader .= "--".$bodyBoundary. $this->CRLF;

			$mailHeader .= $this->formatHTMLHeader();
			$mailHeader .= $this->mailHTML. $this->CRLF;
			$mailHeader .= $this->CRLF."--".$bodyBoundary. "--";

			return $this->SendMail($mailHeader);
		} elseif ($this->mailText != '' && $this->mailHTML != '' && $this->mailAttachments != '') {
			$attachmentBoundary = $this->getRandomBoundary();
			$mailHeader .= "Content-Type: multipart/mixed;".$this->CRLF;
			$mailHeader .= ' boundary="'.$attachmentBoundary. '"'. $this->CRLF.$this->CRLF;
			$mailHeader .= "This is a multi-part message in MIME format.".$this->CRLF;
			$mailHeader .= "--".$attachmentBoundary. $this->CRLF;
			$bodyBoundary = $this->getRandomBoundary(1);
			$mailHeader .= "MIME-Version: 1.0".$this->CRLF;
			$mailHeader .= "Content-Type: multipart/alternative;".$this->CRLF;
			$mailHeader .= ' boundary="'.$bodyBoundary. '"';
			$mailHeader .= $this->CRLF.$this->CRLF.$this->CRLF;
			$mailHeader .= "--".$bodyBoundary. $this->CRLF;
			$mailHeader .= $this->formatTextHeader();
			$mailHeader .= $this->mailText. $this->CRLF;
			$mailHeader .= "--".$bodyBoundary. $this->CRLF;
			$mailHeader .= $this->formatHTMLHeader();
			$mailHeader .= $this->mailHTML. $this->CRLF;
			$mailHeader .= $this->CRLF."--".$bodyBoundary. "--";

			$attachmentArray = explode(',', $this->mailAttachments);
			foreach ($attachmentArray as $val) {
				$mailHeader .= $this->formatAttachmentHeader($val);
			}
			$mailHeader .= "--".$attachmentBoundary. "--";
			return $this->SendMail($mailHeader);
		}
		return false;
	}

	function get_lines()
	{
		$data = '';
		while($str = fgets($this->smtp_conn, 515)) {
			$data .= $str;
			if (substr($str, 3, 1) == ' ') break;
		}
		return $data;
	}

	function Hello()
	{
		$this->error = null;

		if (empty($this->host)) {
			$host = "localhost";
		}
		fputs($this->smtp_conn,"HELO ".$this->host.$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 250) {
			$this->error = array(
				"error" => "HELO not accepted from server",
				"smtp_code" => $code,
				"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}
		return true;
	}

	function MailFrom($from)
	{
		$this->error = null;

		$from = preg_replace('/'.$this->CRLF.'/', '', $from);

		fputs($this->smtp_conn,"MAIL FROM:"."<$from>".$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 250) {
			$this->error =array(
				"error" => "MAIL not accepted from server",
				"smtp_code" => $code,
				"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}
		return true;
	}

	function Recipient($to, $flag)
	{
		$this->error = null;

		$to = preg_replace('/'.$this->CRLF.'/', '', $to);

		fputs($this->smtp_conn,"RCPT ".$flag.":"."<$to>".$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 250 && $code != 251) {
			$this->error = array("error" => "RCPT not accepted from server",
					"smtp_code" => $code,
					"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]." to=$to flag=$flag rply=$rply");
			return false;
		}
		return true;
	}

	function Reset()
	{
		$this->error = null;

		fputs($this->smtp_conn,"RSET".$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 250) {
			$this->error = array("error" => "RSET failed",
					"smtp_code" => $code,
					"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}
		return true;
	}

	function Login($username, $password)
	{
		$this->error = null;

		if (!$this->connected()) {
			$this->error = array("error" => "Called Login() without being connected");
			if ($this->debug == 1) $this->write_log($this->error["error"]);
			return false;
		}

		fputs($this->smtp_conn,"AUTH LOGIN".$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 334) {
			$this->error = array("error" => "UserName Prompt failed",
					"smtp_code" => $code,
					"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}

		fputs($this->smtp_conn, base64_encode($username).$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 334) {
			$this->error = array("error" => "Password Prompt failed",
					"smtp_code" => $code,
					"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}

		fputs($this->smtp_conn, base64_encode($password).$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 235) {
			$this->error = array("error" => "Authorize failed",
					"smtp_code" => $code,
					"smtp_msg" => substr($rply, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			return false;
		}
		return true;
	}

	function Data($msg_data)
	{
		$this->error = null;

		fputs($this->smtp_conn, 'DATA'.$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 354) {
			$this->error = array('error' => 'DATA command not accepted from server',
						'smtp_code' => $code,
						'smtp_msg' => substr($rply, 4));
			$this->write_log($this->error['error'].' code='.$this->error['smtp_code'].' msg='.$this->error['smtp_msg'].' to=['.$this->mailTo.']');
			return false;
		}
		$msg_data = str_replace("\r\n", "\n", $msg_data);
		$msg_data = str_replace("\r", "\n", $msg_data);
		$lines = explode("\n", $msg_data);

		foreach ($lines as $line) {
			$line_out = $line;
			if ($line_out == '.') $line_out = '.'.$line_out;
			fputs($this->smtp_conn, $line_out.$this->CRLF);
		}

		fputs($this->smtp_conn, $this->CRLF.'.'.$this->CRLF);
		$rply = $this->get_lines();
		$code = substr($rply, 0, 3);
		if ($code != 250) {
			$this->error = array('error' => 'DATA command not accepted from server',
						'smtp_code' => $code,
						'smtp_msg' => substr($rply, 4));
			$this->write_log($this->error['error'].' code='.$this->error['smtp_code'].' msg='.$this->error['smtp_msg'].' to=['.$this->mailTo.']');
			return false;
		}
		return true;
	}

	function SendMail($mailHeader='')
	{
		if (!$this->connected()) {
			$this->error = array('error' => 'Called Data() without being connected');
			if ($this->debug == 1) $this->write_log($this->error['error']);
			return false;
		}
		$this->Reset();
		$this->MailFrom($this->mailFrom);

		$addressArray = explode(',', $this->mailTo);
		foreach ($addressArray as $val) $this->Recipient($val, 'TO');

		if ($this->mailCC != '') {
			$addressArray = explode(',', $this->mailCC);
			foreach ($addressArray as $val) $this->Recipient($val, 'TO');
		}
		if ($this->mailBCC != '') {
			$addressArray = explode(',', $this->mailBCC);
			foreach ($addressArray as $val) $this->Recipient($val, 'TO');
		}

		$mailHeader = $this->encodeJIS(trim($mailHeader));
		$this->Data($mailHeader);

		 return  true;
	}

	function Close()
	{
		if (!empty($this->smtp_conn)) {
			fclose($this->smtp_conn);
			$this->smtp_conn = 0;
		}
	}

	function Quit($close_on_error=true)
	{
		$this->error = null;
		if (!$this->connected()) {
			$this->error = array("error" => "Called Quit() without being connected");
			if ($this->debug == 1) $this->write_log($this->error["error"]);
			return false;
		}

		fputs($this->smtp_conn,"quit".$this->CRLF);

		$byemsg = $this->get_lines();
		$rval = true;
		$e = null;
		$code = substr($byemsg, 0, 3);
		if ($code != 221) {
			# use e as a tmp var cause Close will overwrite $this->error
			$e = array("error" => "SMTP server rejected quit command",
				 "smtp_code" => $code,
				 "smtp_msg" => substr($byemsg, 4));
			$this->write_log($this->error["error"]." code=".$this->error["smtp_code"]." msg=".$this->error["smtp_msg"]);
			$rval = false;
		}
		if (empty($e) || $close_on_error) {
			$this->Close();
		}
		return $rval;
	}

	function write_log($message)
	{
		$fileexists = false;
		if (file_exists($this->logfile)) $fileexists = true;

		error_log("\n".date("Y-m-d H:i:s")."\t".$message, 3, $this->logfile);
 		if (!$fileexists) chmod ($this->logfile, 0644);
	}

	function encode64($str)
	{
		return "=?".$this->charset."?B?".base64_encode($str)."?=";
	}

	function encodeJIS($str)
	{
		return mb_convert_encoding($str, $this->charset, 'UTF-8');
	}

	function setHost($host)
	{
		if (strlen(trim($host)) > 0) {
			$this->host = $host;
			return true;
		}
		return false;
	}

	function setPort($port)
	{
		$this->port = intval($port);
		return true;
	}
}
?>
