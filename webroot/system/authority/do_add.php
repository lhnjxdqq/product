<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$parentId       = (int) $_POST['parent-id'];
$authorityName  = trim($_POST['authority-name']);
$authorityUrl   = trim($_POST['authority-url']);
$authorityDesc  = trim($_POST['authority-desc']);

if (empty($_POST['authority-name'])) {

    Utility::notice('权限名称不能为空');
}

$data   = array(
    'authority_name'    => $authorityName,
    'authority_desc'    => $authorityDesc,
    'parent_id'         => $parentId,
);
if (!empty($authorityUrl)) {

    $data['authority_url']  = $authorityUrl;
}

if ($authorityInfo = Authority_Info::getByUrl($data['authority_url'])) {

    if ($authorityInfo['delete_status'] == Authority_DeleteStatus::DELETED) {

        $data['authority_id']   = $authorityInfo['authority_id'];
        $data['delete_status']  = Authority_DeleteStatus::NORMAL;
        Authority_Info::update($data);
        Utility::notice('添加权限成功', '/authority/index.php');
    } else {

        Utility::notice('权限URL已存在');
    }
}

if (Authority_Info::create($data)) {

    Utility::notice('添加权限成功', '/system/authority/index.php');
} else {

    Utility::notice('添加权限失败');
}