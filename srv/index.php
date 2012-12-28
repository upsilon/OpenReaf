<?php
/*
 *  Copyright 2010-2012 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  index.php
 */
require 'language.php';
require '../app/define/common.php';
require OPENREAF_ROOT_PATH.'/app/include/common.php';
require OPENREAF_ROOT_PATH.'/app/include/client.php';

$kind = 'pc';

$agent_kind = check_user_agent();

switch ($agent_kind) {
	case 1:
		$kind = 'mobile';
		ini_set('session.use_trans_sid', '1');
		mb_http_output('SJIS');
		ob_start('mb_output_handler');
		define('_TermClass_', 'Mobile');
		break;
	case 2:
		$kind = 'smart';
		define('_TermClass_', 'SmartPhone');
		break;
	default:
		define('_TermClass_', 'Internet');
		break;
}

require OPENREAF_ROOT_PATH.'/app/define/'.$kind.'.php';
require OPENREAF_ROOT_PATH.'/app/class/Action.class.php';

$op = get_request_var('op');

$class_path = OPENREAF_ROOT_PATH.'/app/'.$kind.'/';
$class_name = preg_replace("/[\/ \t\n\r\f]/", '', $op).'Action';
$class_file = $class_path.$class_name.'.class.php';

if (!file_exists($class_file)) $class_name = 'topAction';

require $class_path.$class_name.'.class.php';

$oAction = new $class_name($kind);
$oAction->execute();

?>
