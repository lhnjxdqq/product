<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['authority_id'])) {

    Utility::notice('authority_id is missing');
}

$data   = array(
    'authority_id'  => (int) $_GET['authority_id'],
    'delete_status' => Authority_DeleteStatus::DELETED,
);

if (Authority_Info::update($data)) {

    Utility::notice('删除权限成功', '/system/authority/index.php');
} else {

    Utility::notice('删除角色失败');
}