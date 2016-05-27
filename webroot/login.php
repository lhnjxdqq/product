<?php
require_once dirname(__FILE__) . '/../init.inc.php';

$data['action'] = '/do_login.php';

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('common/login.tpl');