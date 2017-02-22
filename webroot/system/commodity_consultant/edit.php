<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['commodity_consultant_id'])) {

    Utility::notice('商品顾问ID不存在');
}

$commodityConsultantId     = (int) $_GET['commodity_consultant_id'];
$commodityConsultantInfo   = Commodity_Consultant_Info::getById($commodityConsultantId);
$listUserInfo      = ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1));

if (empty($commodityConsultantInfo)) {

    Utility::notice('商品顾问不存在');
}

$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listUserInfo', $listUserInfo);
$template->assign('commodityConsultantInfo', $commodityConsultantInfo);
$template->display('system/commodity_consultant/edit.tpl');