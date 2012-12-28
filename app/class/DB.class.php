<?php
/*
 *  Copyright 2008-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  DBUtil.class.php
 */
require_once("DB.php");

class DBUtil
{
	private $dsn = array();
	private $cn = null;

	function __construct()
	{
		$this->dsn = $GLOBALS['_OPENREAF_DSN_'];
	}

	//-----------------------------------------------------------------
	// DB接続関数
	//-----------------------------------------------------------------
	function connect()
	{
		if (!is_null($this->cn)) {
			return $this->cn;
		}
		$this->cn = DB::connect($this->dsn);
		if (DB::isError($this->cn)) {
			die('Database connection error');
		}
		if (strncasecmp($this->dsn['phptype'], 'mysql', 5) == 0) {
			//文字化け対策
			$this->cn->query('SET CHARACTER SET utf8');
		}

		return $this->cn;
	}

	function getCon()
	{
		return $this->cn;
	}

	function get_type()
	{
		return $this->dsn['phptype'];
	}

	//-----------------------------------------------------------------
	// DBDisconnect関数
	//-----------------------------------------------------------------
	function disconnect()
	{
		if (!is_null($this->cn)) {
			$this->cn->disconnect();
			$this->cn = null;
		}
	}

	//-----------------------------------------------------------------
	// テーブル挿入用連想配列に共通のキーを持つデータを選ぶ
	// 共通でないキーは削除
	// @param
	//		array $field_value
	// 		array $TableName
	//-----------------------------------------------------------------
	function make_base_dataset($field_value, $TableName)
	{
		$sql = 'DESC '.$TableName;
		if ($this->dsn['phptype'] == 'pgsql') {
			$sql = "SELECT column_name FROM information_schema.columns WHERE table_name='".$TableName."' ORDER BY ordinal_position";
		}
		$col = $this->cn->getCol($sql);

		$ret = array();
		foreach ($col as $colname) {
			foreach ($field_value as $key => $value) {
				if ($key == $colname) {
					$ret[$key] = $value === '' ? NULL : $value;
				}
			}
		}
		return $ret;
	}

	//-----------------------------------------------------------------
	// エラーIDを取得する
	//-----------------------------------------------------------------
	function get_error_code($rs=null)
	{
		if ($this->cn->isError($rs)) return $rs->getCode();
		else return true;
	}

	//--------------------------------------------------------
	// エラーをチェックし、最後の返却値を返す
	//--------------------------------------------------------	
	function check_error($rs)
	{
		$e_code = $this->get_error_code($rs);
		if ($e_code < 0) {
			if ($e_code == -3 || $e_code == -5) {
				return $e_code;
			} else {
				$stErr = 'DB_ERROR : code=['.$e_code.'] '.$rs->getMessage();
				die($stErr);
			}
		}
		return $rs;
	}

	//--------------------------------------------------------
	// インサート
	//--------------------------------------------------------
	function insert($table_name, &$dataset)
	{
		$rs = $this->cn->autoExecute($table_name, $dataset, DB_AUTOQUERY_INSERT);
		$code = $this->check_error($rs);
		if ($code < 0) return $code;
		return true;
	}

	//--------------------------------------------------------
	// アップデート
	//--------------------------------------------------------
	function update($table_name, &$dataset, $where)
	{
		$rs = $this->cn->autoExecute($table_name, $dataset, DB_AUTOQUERY_UPDATE, $where);
		$code = $this->check_error($rs);
		if ($code < 0) return $code;
		return true;
	}
}
?>
