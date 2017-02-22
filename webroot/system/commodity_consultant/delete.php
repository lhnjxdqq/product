<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['commodity_consultant_id'])) {

    Utility::notice('商品顾问Id不能为空');
}

$data   = array(
    'commodity_consultant_id'   => (int) $_GET['commodity_consultant_id'],
    'delete_status'         	=> DeleteStatus::DELETED,
);

Commodity_Consultant_Info::update($data);
Utility::notice('删除商品顾问成功');
