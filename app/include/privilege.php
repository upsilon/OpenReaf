<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  privilege.php
 */

$priv_label = array('1' => 'stf', '2' => 'usr', '3' => 'fcl',
			'4' => 'rsv', '5' => 'fee', '6' => 'use');

function get_privilege(&$res)
{
	global $priv_label;

	$recs = array();

	foreach ($priv_label as $key => $val)
	{
		$recs[$val] = 'FORBIDDEN';
		if (intval($res['kengencode'.$key]) > 0) {
			$recs[$val] = $res['kengencode'.$key];
		}
	}
	return $recs;
}

function get_privilege_ldap(&$res)
{
	global $priv_label;

	$recs = array();

	foreach ($priv_label as $key => $val)
	{
		$recs[$val] = 'FORBIDDEN';
		if (intval($res['kengencode'.$key][0]) > 0) {
			$recs[$val] = $res['kengencode'.$key][0];
		}
	}
	return $recs;
}
