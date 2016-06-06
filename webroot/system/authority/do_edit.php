<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$authorityId    = (int) $_POST['authority-id'];
$authorityName  = trim($_POST['authority-name']);
$authorityUrl   = trim($_POST['authority-url']);
$authorityDesc  = trim($_POST['authority-desc']);
$parentId       = (int) $_POST['parent-id'];

$data           = array(
    'authority_id'      => $authorityId,
    'authority_name'    => $authorityName,
    'authority_url'     => $authorityUrl,
    'authority_desc'    => $authorityDesc,
    'parent_id'         => $parentId,
);

if (Authority_Info::update($data)) {

    Utility::notice('编辑权限成功', '/system/authority/index.php');
} else {

    Utility::notice('编辑权限失败');
}