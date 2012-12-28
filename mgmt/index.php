<?php
/*
 *  Copyright 2010-2011 ZiWAVE Co., Ltd.
 *  License GPLv2
 *
 *  index.php
 */
require '../app/define/common.php';
require OPENREAF_ROOT_PATH.'/app/define/mgmt.php';
require OPENREAF_ROOT_PATH.'/app/include/common.php';
require OPENREAF_ROOT_PATH.'/app/class/adminAction.class.php';

$op = get_request_var('op');

$class_path = OPENREAF_ROOT_PATH.'/app/mgmt/';
$class_name = preg_replace("/[\/ \t\n\r\f]/", '', $op).'Action';
$class_file = $class_path.$class_name.'.class.php';

if (!file_exists($class_file)) $class_name = 'loginAction';

require $class_path.$class_name.'.class.php';

$oAction = new $class_name();
$oAction->execute();

?>
