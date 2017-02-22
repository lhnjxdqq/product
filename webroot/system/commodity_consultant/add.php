<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']   = Menu_Info::getMainMenu();
$listUserInfo      = ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1));

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listUserInfo', $listUserInfo);
$template->display('system/commodity_consultant/add.tpl');