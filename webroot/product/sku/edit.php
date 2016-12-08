<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['goods_id'], 'goods_id is missing', '/product/sku/index.php');

if(strstr($_SERVER['HTTP_REFERER'],'/sku/index.php')){
	
	$_SESSION['page_product']   = $_SERVER['HTTP_REFERER'];
}

$listSpecInfo       = Spec_Info::listAll();
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo  = Spec_Value_Info::listAll();
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
$listStyleInfo      = Style_Info::listAll();
$mapStyleInfo       = ArrayUtility::groupByField($listStyleInfo, 'parent_id');

$goodsId            = (int) $_GET['goods_id'];
$goodsInfo          = Goods_Info::getById($goodsId);
if ($goodsInfo['online_status'] == Goods_OnlineStatus::OFFLINE) {

    Utility::notice('下架状态的SKU不允许编辑');
}
$categoryInfo       = Category_Info::getByCategoryId($goodsInfo['category_id']);
$goodsStyleInfo     = Style_Info::getById($goodsInfo['style_id']);
$goodsTypeSpecValue = Goods_Type_Spec_Value_Relationship::getSpecValueByGoodsTypeId($goodsInfo['goods_type_id']);
$mapTypeSpecValue   = ArrayUtility::groupByField($goodsTypeSpecValue, 'spec_id', 'spec_value_id');
$listGoodsSpecValue = Goods_Spec_Value_RelationShip::getByGoodsId($goodsId);
$mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'spec_id', 'spec_value_id');

$listProductInfo    = Product_Info::getByGoodsId($goodsId);
$listProductId      = ArrayUtility::listField($listProductInfo, 'product_id');
$listSourceId       = ArrayUtility::listField($listProductInfo, 'source_id');
$listSourceInfo     = Source_Info::getByMultiId($listSourceId);
$mapSourceInfo      = ArrayUtility::indexByField($listSourceInfo, 'source_id');
$listSupplierId     = ArrayUtility::listField($listSourceInfo, 'supplier_id');
$listSupplierInfo   = Supplier_Info::getByMultiId($listSupplierId);
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$listProductImages  = Product_Images_RelationShip::getByMultiId($listProductId);
$groupProductIdImages  = ArrayUtility::groupByField($listProductImages,'product_id');

foreach ($mapSourceInfo as &$sourceInfo) {

    $supplierId = $sourceInfo['supplier_id'];
    $sourceInfo['supplier_code']    = $mapSupplierInfo[$supplierId]['supplier_code'];
}

foreach ($listProductInfo as &$productInfo) {

    $sourceId   = $productInfo['source_id'];
    $productId  = $productInfo['product_id'];

    if(!empty($groupProductIdImages[$productId])){
     
        $firstImageInfo = ArrayUtility::searchBy($groupProductIdImages[$productId],array('is_first_picture' => 1));   
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info = current($firstImageInfo);
        $productInfo['image_url'] = AliyunOSS::getInstance('images-product')->url($info['image_key']);     
    }else{
     
        $info = Sort_Image::sortImage($groupProductIdImages[$productId]);
        $productInfo['image_url']   = $info[0]['image_key'] ? AliyunOSS::getInstance('images-product')->url($info[0]['image_key']) : '';
    }
    $productInfo['source_code']     = $mapSourceInfo[$sourceId]['source_code'];
    $productInfo['supplier_code']   = $mapSourceInfo[$sourceId]['supplier_code'];
}

$listGoodsImages            = Goods_Images_RelationShip::getByGoodsId($goodsId);
$sortListGoodsImages        = Sort_Image::sortImage($listGoodsImages);

if(!empty($sortListGoodsImages)){
 
    foreach ($sortListGoodsImages as &$goodsImage) {

        $imageKey                   = $goodsImage['image_key'];
        $goodsImage['image_url']    = AliyunOSS::getInstance('images-sku')->url($imageKey);
    }   
}

$goodsInfo['category_name'] = $categoryInfo['category_name'];

$data['mapSpecInfo']        = $mapSpecInfo;
$data['mapSpecValueInfo']   = $mapSpecValueInfo;
$data['mapStyleInfo']       = $mapStyleInfo;
$data['goodsStyleInfo']     = $goodsStyleInfo;
$data['mapTypeSpecValue']   = $mapTypeSpecValue;
$data['mapGoodsSpecValue']  = $mapGoodsSpecValue;
$data['goodsInfo']          = $goodsInfo;
$data['listProductInfo']    = $listProductInfo;
$data['listGoodsImages']    = $sortListGoodsImages;
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/sku/edit.tpl');