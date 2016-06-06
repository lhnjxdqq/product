<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$userId     = (int) $_POST['user-id'];
$roleIds    = (array) $_POST['role-id'];

if (empty($roleIds)) {

    Utility::notice('请选择角色');
}

User_RoleRelationship::delByUserId($userId);
foreach ($roleIds as $roleId) {

    $data   = array(
        'user_id'   => $userId,
        'role_id'   => $roleId,
    );
    User_RoleRelationship::create($data);
}

Utility::notice('分配角色成功', '/system/user/index.php');