<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$productId      = (int) $_GET['product_id'];
$productInfo    = Product_Info::getById($productId);
if ($productInfo['online_status'] == Product_OnlineStatus::OFFLINE) {

    Utility::notice('下架状态的产品不允许编辑');
}
$productInfo['sourceInfo']      = Source_Info::getById($productInfo['source_id']);
$productInfo['supplierInfo']    = Supplier_Info::getById($productInfo['sourceInfo']['supplier_id']);
$productInfo['goodsInfo']       = Goods_Info::getById($productInfo['goods_id']);
$productInfo['styleInfo']       = Style_Info::getById($productInfo['goodsInfo']['style_id']);
$productInfo['categoryInfo']    = Category_Info::getByCategoryId($productInfo['goodsInfo']['category_id']);

if(strstr($_SERVER['HTTP_REFERER'],'/product/index.php')){
	
	$_SESSION['page_product']   = $_SERVER['HTTP_REFERER'];
}

// 规格 规格值查询
$specValueList  = Goods_Spec_Value_RelationShip::getByGoodsId($productInfo['goodsInfo']['goods_id']);
$listSpecInfo   = Spec_Info::getByMulitId(ArrayUtility::listField($specValueList, 'spec_id'));
$listSpecValue  = Spec_Value_Info::getByMulitId(ArrayUtility::listField($specValueList, 'spec_value_id'));
$mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$mapSpecValue   = ArrayUtility::indexByField($listSpecValue, 'spec_value_id');
foreach ($specValueList as $key => $specValue) {

    $specValueList[$key]['spec_name']       = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
    $specValueList[$key]['spec_unit']       = $mapSpecInfo[$specValue['spec_id']]['spec_unit'];
    $specValueList[$key]['spec_value_data'] = $mapSpecValue[$specValue['spec_value_id']]['spec_value_data'];
}

$listSupplier   = Supplier_Info::listAll();
$listSupplier   = ArrayUtility::searchBy($listSupplier, array('delete_status'=>Supplier_DeleteStatus::NORMAL));
$mapSupplier    = ArrayUtility::indexByField($listSupplier, 'supplier_id');

// 图片
$listImages     = Product_Images_RelationShip::getById($productId);

if ($listImages) {

    $info = Sort_Image::sortImage($listImages);

    foreach ($info as $key => $item) {

        $listImages[$key]['image_url']  = AliyunOSS::getInstance('images-product')->url($item['image_key']);
    }
}

$data['productInfo']    = $productInfo;
$data['listImages']     = $listImages;
$data['mapSupplier']    = $mapSupplier;
$data['specValueList']  = $specValueList;
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/product/edit.tpl');