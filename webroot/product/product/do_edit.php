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

$productInfo    = Product_Info::getById($productId);
$goodsId        = (int) $productInfo['goods_id'];
if (Product_Info::update($data)) {

    if(sprintf('%.2f',$productInfo['product_cost']) != sprintf('%.2f',$data['product_cost'])){
        
        Cost_Update_Log_Info::create(array(
            'product_id'        => $productId,
            'cost'              => sprintf('%.2f',$data['product_cost']),
            'handle_user_id'    => $_SESSION['user_id'],
            'update_means'      => Cost_Update_Log_UpdateMeans::MANUAL,
        ));
    }
    // 更新商品 成本工费和基础销售工费
    $goodsCost  = Goods_Info::getGoodsCost($goodsId);
    $goodsData  = array_merge(array('goods_id'=>$goodsId), $goodsCost);
    Goods_Info::update($goodsData);
    // 更新图片关系
    Product_Images_RelationShip::deleteById($productId);
    if ($imageIdList) {
        foreach ($imageIdList as $imageId) {
            Product_Images_RelationShip::create(array(
                'product_id' => $productId,
                'image_key' => $imageId,
            ));
        }
    }
    // 推送SKU更新数据到选货工具
    Goods_Push::updatePushGoodsData($goodsId);
    Utility::notice('编辑产品成功', '/product/product/index.php');
} else {

    Utility::notice('编辑产品失败');
}