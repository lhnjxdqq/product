<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['goods_id'], 'goods_id is missing', '/product/sku/index.php');

$listSpecInfo       = Spec_Info::listAll();
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo  = Spec_Value_Info::listAll();
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

$goodsId            = (int) $_GET['goods_id'];
$goodsInfo          = Goods_Info::getById($goodsId);
$categoryInfo       = Category_Info::getByCategoryId($goodsInfo['category_id']);
$listGoodsSpecValue = Goods_Spec_Value_RelationShip::getByGoodsId($goodsId);
foreach ($listGoodsSpecValue as $specValue) {

    $specId         = $specValue['spec_id'];
    $specValueId    = $specValue['spec_value_id'];
    $specName       = $mapSpecInfo[$specId]['spec_name'];
    $specUnit       = $mapSpecInfo[$specId]['spec_unit'];
    $specValueData  = $mapSpecValueInfo[$specValueId]['spec_value_data'];
    switch ($specName) {
        case '主料材质' :
            $goodsInfo['material']  = $specValueData . $specUnit;
        break;
        case '规格尺寸' :
            $goodsInfo['size']      = $specValueData . $specUnit;
        break;
        case '规格重量' :
            $goodsInfo['weight']    = $specValueData . $specUnit;
        break;
        case '颜色' :
            $goodsInfo['color']     = $specValueData . $specUnit;
        break;
    }
}

$listProductInfo            = Product_Info::getByGoodsId($goodsId);
$listProductId              = ArrayUtility::listField($listProductInfo, 'product_id');
$listSourceId               = ArrayUtility::listField($listProductInfo, 'source_id');
$listSourceInfo             = Source_Info::getByMultiId($listSourceId);
$mapSourceInfo              = ArrayUtility::indexByField($listSourceInfo, 'source_id');
$listSupplierId             = ArrayUtility::listField($listSourceInfo, 'supplier_id');
$listSupplierInfo           = Supplier_Info::getByMultiId($listSupplierId);
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$listProductImages          = Product_Images_RelationShip::getByMultiId($listProductId);
$mapProductImages           = ArrayUtility::indexByField($listProductImages, 'product_id');
foreach ($mapSourceInfo as &$sourceInfo) {

    $supplierId = $sourceInfo['supplier_id'];
    $sourceInfo['supplier_code']    = $mapSupplierInfo[$supplierId]['supplier_code'];
}
foreach ($listProductInfo as &$productInfo) {

    $sourceId   = $productInfo['source_id'];
    $productId  = $productInfo['product_id'];
    $imageKey   = $mapProductImages[$productId]['image_key'];
    $productInfo['source_code']     = $mapSourceInfo[$sourceId]['source_code'];
    $productInfo['supplier_code']   = $mapSourceInfo[$sourceId]['supplier_code'];
    $productInfo['image_url']       = $imageKey ? AliyunOSS::getInstance('images-product')->url($imageKey) : '';
}

$listGoodsImages            = Goods_Images_RelationShip::getByGoodsId($goodsId);
foreach ($listGoodsImages as &$goodsImage) {

    $imageKey                   = $goodsImage['image_key'];
    $goodsImage['image_url']    = AliyunOSS::getInstance('images-sku')->url($imageKey);
}

$goodsInfo['category_name'] = $categoryInfo['category_name'];

$data['goodsInfo']          = $goodsInfo;
$data['listProductInfo']    = $listProductInfo;
$data['listGoodsImages']    = $listGoodsImages;
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/sku/edit.tpl');