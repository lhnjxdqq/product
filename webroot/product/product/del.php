<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

//$multiProductId  = isset($_GET['product_id']) ? (array) $_GET['product_id'] : array();
//$multiProductId  = isset($_GET['product_id']) ? explode(',', $_GET['multi_product_id']) : array();

$productIdList      = array();

if (isset($_GET['product_id'])) {

    $productIdList  = (array) $_GET['product_id'];
} elseif (isset($_GET['multi_product_id'])) {

    $productIdList  = explode(',', $_GET['multi_product_id']);
}

if (Product_Info::setDeleteStatusByMultiProductId($productIdList, Product_DeleteStatus::DELETED)) {

    Utility::notice('删除商品成功');
} else {

    Utility::notice('删除商品失败');
}