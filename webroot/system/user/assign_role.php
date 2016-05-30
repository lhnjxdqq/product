<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['user_id'])) {

    Utility::notice('user_id is missing');
}

$uerId      = (int) $_GET['user_id'];
$listRole   = Role_Info::listByCondition(array('delete_status'=>Role_DeleteStatus::NORMAL));
$userRole   = User_RoleRelationship::getByUserId($uerId);
$userRole   = ArrayUtility::listField($userRole, 'role_id');

$data['listRole']   = $listRole;
$data['userRole']   = $userRole;
$data['mainMenu']   = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/user/assign_role.tpl');