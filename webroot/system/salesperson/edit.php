<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['salesperson_id'])) {

    Utility::notice('销售员ID不存在');
}

$salespersonId     = (int) $_GET['salesperson_id'];
$salespersonInfo   = Salesperson_Info::getById($salespersonId);
$listUserInfo      = ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1));

if (!$salespersonInfo) {

    Utility::notice('销售员不存在');
}

$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listUserInfo', $listUserInfo);
$template->assign('salespersonInfo', $salespersonInfo);
$template->display('system/salesperson/edit.tpl');