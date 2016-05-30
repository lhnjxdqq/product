<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['role_id'])) {

    Utility::notice('role_id is missing');
}

$data   = array(
    'role_id'       => (int) $_GET['role_id'],
    'delete_status' => Role_DeleteStatus::DELETED,
);

if (Role_Info::update($data)) {

    Utility::notice('删除角色成功', '/system/role/index.php');
} else {

    Utility::notice('删除角色失败');
}