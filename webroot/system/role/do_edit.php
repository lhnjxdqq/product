<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (empty($_POST['role-id']) || empty($_POST['role-name'])) {

    Utility::notice('必要参数不能为空');
}

$roleId     = (int) $_POST['role-id'];
$roleName   = trim($_POST['role-name']);
$roleDesc   = trim($_POST['role-desc']);

$data       = array(
    'role_id'   => $roleId,
    'role_name' => $roleName,
    'role_desc' => $roleDesc,
);

if (Role_Info::update($data)) {

    Utility::notice('编辑角色成功', '/system/role/index.php');
} else {

    Utility::notice('编辑角色失败');
}