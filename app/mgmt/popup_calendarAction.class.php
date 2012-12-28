<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  popup_calendarAction.class.php
 *  co_calendar.tpl
 */

class popup_calendarAction extends adminAction
{
	function __construct()
	{
		parent::__construct();
	}

	function execute()
	{
		$para = array();
		$para['y'] = empty($_GET['y']) ? date('Y') : $_GET['y'];
		$para['m'] = empty($_GET['m']) ? date('m') : $_GET['m'];
		$para['name'] = $_GET['name'];
		$para['ver'] = $_GET['ver'];

		$pmonth = mktime(0, 0, 0, $para['m']-1, 1, $para['y']);
		$nmonth = mktime(0, 0, 0, $para['m']+1, 1, $para['y']);

		$para['prev'] = 'y='.date('Y', $pmonth).'&m='.date('n', $pmonth).'&d=1'.'&name='.$para['name'].'&ver='.$para['ver'];
		$para['next'] = 'y='.date('Y', $nmonth).'&m='.date('n', $nmonth).'&d=1'.'&name='.$para['name'].'&ver='.$para['ver'];

		$firstDate = mktime(0, 0, 0, $para['m'], 1, $para['y']);
		$lastDay = date('t', $firstDate);

		$recs = array();
		$j = date('w', $firstDate);
		for ($i = 0; $i < $j; ++$i) {
			$recs[$i] = 0;
		}
		for ($i = 1; $i <= $lastDay; ++$i) {
			$recs[$j] = $i;
			++$j;
		}
		$tmp = $j%7;
		if ($tmp != 0) {
			for ($i = $tmp; $i < 7; ++$i) {
				$recs[$j] = 0;
				++$j;
			}
		}

		$this->oSmarty->assign('para', $para);
		$this->oSmarty->assign('recs', $recs);
		$this->oSmarty->display('co_calendar.tpl');
	}
}
?>
