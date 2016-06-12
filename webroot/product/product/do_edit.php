<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$productId      = (int) $_POST['product-id'];
$productName    = trim($_POST['product-name']);
$supplierId     = (int) $_POST['supplier-id'];
$sourceCode     = trim($_POST['source-code']);
$productCost    = sprintf('%.2f', $_POST['product-cost']);
$productRemark  = trim($_POST['product-remark']);
$imageIdList    = $_POST['product-image'];

if (empty($productName)) {

    Utility::notice('产品名称不能为空');
}

if ($supplierId == 0) {

    Utility::notice('请选择供应商ID');
}

if (empty($sourceCode)) {

    Utility::notice('请填写买款ID');
}

if (empty($productCost)) {

    Utility::notice('请填写进货工费');
}

$files  = $_FILES['product-image'];
foreach ($files['tmp_name'] as $stream) {
    if ($stream) {
        $imageKey = AliyunOSS::getInstance('images-product')->create($stream);
        $start = strpos($imageKey, '/') + 1;
        $length = strpos($imageKey, '.') - $start;
        $imageId = substr($imageKey, $start, $length);
        $imageIdList[] = $imageId;
    }
}

$sourceInfo = Source_Info::listByCondition(array(
    'source_code'   => $sourceCode,
    'supplier_id'   => $supplierId,
));
$sourceId   = $sourceInfo ? current($sourceInfo)['source_id'] : Source_Info::create(array(
    'source_code'   => $sourceCode,
    'supplier_id'   => $supplierId,
));

$data   = array(
    'product_id'        => $productId,
    'product_name'      => $productName,
    'product_cost'      => $productCost,
    'source_id'         => $sourceId,
    'product_remark'    => $productRemark,
);

if (Product_Info::update($data)) {
    
    if ($imageIdList) {
        Product_Images_RelationShip::deleteById($productId);
        foreach ($imageIdList as $imageId) {
            Product_Images_RelationShip::create(array(
                'product_id' => $productId,
                'image_key' => $imageId,
            ));
        }
    }
    Utility::notice('编辑产品成功', '/product/product/index.php');
} else {

    Utility::notice('编辑产品失败');
}