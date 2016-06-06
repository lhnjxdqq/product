<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$roleName   = trim($_POST['role-name']);
$roleDesc   = trim($_POST['role-desc']);

if (empty($roleName)) {

    Utility::notice('角色名称不能为空');
}

$data       = array(
    'role_name' => $roleName,
    'role_desc' => $roleDesc,
);

if ($roleInfo = Role_Info::getByName($roleName)) {

    if ($roleInfo['delete_status'] == Role_DeleteStatus::NORMAL) {

        Utility::notice('角色名称已存在');
    } else {

        $data['role_id']        = $roleInfo['role_id'];
        $data['delete_status']  = Role_DeleteStatus::NORMAL;
        Role_Info::update($data);
        Utility::notice('添加角色成功', '/system/role/index.php');
    }
}

if (Role_Info::create($data)) {

    Utility::notice('添加角色成功', '/system/role/index.php');
} else {

    Utility::notice('添加角色失败');
}