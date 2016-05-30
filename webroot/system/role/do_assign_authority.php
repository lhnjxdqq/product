<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$roleId         = (int) $_POST['role-id'];
$authorityIds   = (array) $_POST['authority-id'];

if (empty($authorityIds)) {

    Utility::notice('请选择权限');
}

Role_AuthorityRelationship::delByRoleId($roleId);
foreach ($authorityIds as $authorityId) {

    $data   = array(
        'role_id'       => $roleId,
        'authority_id'  => $authorityId,
    );
    Role_AuthorityRelationship::create($data);
}

Utility::notice('分配权限成功', '/system/role/index.php');