<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['user_id'])) {

    Utility::notice('user_id is missing');
}

$userId     = (int) $_GET['user_id'];
$userInfo   = User_Info::getById($userId);

if (!$userInfo) {

    Utility::notice('用户不存在');
}

$data['userInfo']   = $userInfo;
$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/user/edit.tpl');