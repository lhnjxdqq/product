<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/user/add.tpl');