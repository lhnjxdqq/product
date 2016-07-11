<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';


$produceOrderId = (int) $_GET['produce_order_id'];
$productIdList  = array();
if (isset($_GET['product_id'])) {

    $productIdList      = (array) intval($_GET['product_id']);
}

if (isset($_GET['multi_product_id'])) {

    $productIdString    = trim($_GET['multi_product_id']);
    $productIdList      = explode(',', $productIdString);
}

if (!$produceOrderId || empty($productIdList)) {

    Utility::notice('参数错误');
}

$delete = Produce_Order_Product_Info::deleteByMultiProductId($produceOrderId, $productIdList);
if ($delete) {

    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}