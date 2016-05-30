<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['role_id'])) {

    Utility::notice('role_id is missing');
}

$roleId     = (int) $_GET['role_id'];
$roleInfo   = Role_Info::getById($roleId);

$data['roleInfo']   = $roleInfo;
$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/role/edit.tpl');