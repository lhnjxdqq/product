<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$productIdList      = array();

if (isset($_GET['product_id'])) {

    $productIdList  = (array) $_GET['product_id'];
} elseif (isset($_GET['multi_product_id'])) {

    $productIdList  = explode(',', $_GET['multi_product_id']);
}

if (Product_Info::setDeleteStatusByMultiProductId($productIdList, Product_DeleteStatus::DELETED)) {

    // 是否删除相关的SKU 和SPU-SKU关系
    Common_Product::deleteByMultiProductId($productIdList);
    Utility::notice('删除商品成功');
} else {

    Utility::notice('删除商品失败');
}