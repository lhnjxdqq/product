<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['role_id'])) {

    Utility::notice('role_id is missing');
}

$roleId         = (int) $_GET['role_id'];
$listAuthority  = Authority_Info::listByCondition(array('delete_status'=>Authority_DeleteStatus::NORMAL));
$groupAuthority = ArrayUtility::groupByField($listAuthority, 'parent_id');
$roleAuthority  = Role_AuthorityRelationship::getByRoleId($roleId);
$roleAuthority  = ArrayUtility::listField($roleAuthority, 'authority_id');

$data['groupAuthority'] = $groupAuthority;
$data['roleAuthority']  = $roleAuthority;
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/role/assign_authority.tpl');