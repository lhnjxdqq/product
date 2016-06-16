<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['goods_id'], 'goods_id is missing', '/product/sku/index.php');

$data   = array(
    'goods_id'      => (int) $_GET['goods_id'],
    'delete_status' => Goods_DeleteStatus::DELETED,
);

if (Goods_Info::update($data)) {

    Utility::notice('删除成功', '/product/sku/index.php');
} else {

    Utility::notice('删除失败');
}