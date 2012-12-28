<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  郵便番号データアップロード
 *
 *  dat_01_02_zipAction.class.php
 *  dat_01_02.tpl
 */

class dat_01_02_zipAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$this->set_header_info();

		$messages = array();

		if (isset($_FILES['uploadfile'])) {	
			$fname = $_FILES["uploadfile"]['name'];

			$i = 0;
			$j = 0;
			$handle = fopen($_FILES['uploadfile']['tmp_name'],"r");
			if ($handle) {
				if (!isset($_POST['append'])) {
					$this->con->query('DELETE FROM m_zip');
				}

				while ($data = fgetcsv($handle, 256, ','))
				{
					++$i;
					$data[2] = iconv("SHIFT-JIS", "UTF-8", $data[2]);
					$data[6] = iconv("SHIFT-JIS", "UTF-8", $data[6]);
					$data[7] = iconv("SHIFT-JIS", "UTF-8", $data[7]);
					$data[8] = iconv("SHIFT-JIS", "UTF-8", $data[8]);
					$str = '以下に掲載がない場合';

					if ($data[8] == $str) {
						$address = $data[6].$data[7];
					} else {
						$address = $data[6].$data[7].$data[8];
					}
					$aTemp = array('code' => $data[2],
							'address' => $address);
					$rs = $this->con->autoExecute('m_zip', $aTemp, DB_AUTOQUERY_INSERT);
					if (PEAR::isError($rs)) {
						$messages[] = "エラー：".$i."行目 ". $rs->getmessage();
					} else {
						++$j;
					}
				}
				fclose($handle);
			}
			$messages[] = $i.'件中'.$j.'件のデータを登録しました。';
			$messages[] = 'ファイル名:&nbsp;'.$fname;
			$messages[] = date('Y-m-d H:i:s');
		}
		$this->oSmarty->assign('messages', $messages);
		$this->oSmarty->assign('append', true);
		$this->oSmarty->assign('page_title', '郵便番号データアップロード');
		$this->oSmarty->assign('op', 'dat_01_02_zip');
		$this->oSmarty->display('dat_01_02.tpl');
	}
}
?>
