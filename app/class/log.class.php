<?php
/*
 *  Copyright 2008-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  log.class.php
 */

class log
{
	private $logdir;
	private $logfilename = 'access_log';
	
	function __construct($mode='mgmt')
	{
		$this->logdir = OPENREAF_ROOT_PATH.'/var/log/'.$mode;
	}

	function getLogDir()
	{
		return $this->logdir;
	}

	function setLogDir($logdir)
	{
		$this->logdir = $logdir;
	}

	function setLogFile($logfile)
	{
		$this->logfilename = $logfile;
	}

	function getLogFileList()
	{
		$file_arr = array(0 => "ファイルを指定して下さい");

		if (is_dir($this->logdir)) {
			if ($dh = @opendir($this->logdir)) {
				while (($file = readdir($dh)) !== false)
				{
					if ($file != "." && $file != "..") {
						array_push($file_arr, $file);
					}
				}
				closedir($dh);
			}
		}
		return $file_arr;
	}

	function  setLog($message)
	{
		$fh = null;
		$now = date('Y-m-d H:i:s');
		$sep = "\r\n";
		$t = ' ';

		$fname = $this->logdir.'/'.$this->logfilename;
		if (!$fh = fopen($fname, 'a+')) {
			echo "Can't open the file: ".$fname;
			return;
		}
		flock($fh, 2);
		fputs($fh, $now.$t.$message.$sep);
		fclose($fh);
	}
	
	function getLog($file)
	{
		$contents = '';
		$filename = $this->logdir."/".$file;

		if (is_file($filename)) {
			if (!$handle = fopen($filename,"r")) {
				echo "Can't open the file: ".$filename;
				return;
			}
			$size = filesize($filename);
			if ($size > 0) {
				$contents = fread($handle, $size);
			}
			fclose($handle);
		}
		return nl2br($contents);
	}
}
?>
