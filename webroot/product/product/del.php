<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data = array(
    'product_id'    => (int) $_GET['product_id'],
    'delete_status' => Product_DeleteStatus::DELETED,
);

if (Product_Info::update($data)) {

    Utility::notice('删除商品成功');
} else {

    Utility::notice('删除商品失败');
}