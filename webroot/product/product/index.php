<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;

// 排序
$sortBy     = isset($_GET['sortby']) ? $_GET['sortby'] : 'product_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortBy => $direction,
);

// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countProduct   = Product_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countProduct,
    PageList::OPT_URL       => '/product/product/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$condition['delete_status'] = Product_DeleteStatus::NORMAL;

$listProduct            = Product_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$listGoodsId            = ArrayUtility::listField($listProduct, 'goods_id');
$listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo           = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

$listProductId          = ArrayUtility::listField($listProduct, 'product_id');
$listProductImages      = Product_Images_RelationShip::getByMultiId($listProductId);
$mapProductImage        = array();
if ($listProductImages) {
    $indexProductImage      = ArrayUtility::indexByField($listProductImages, 'product_id', 'image_key');
    foreach ($indexProductImage as $productId => $imageKey) {
        $mapProductImage[$productId] = AliyunOSS::getInstance('images-product')->url($imageKey);
    }
}

$listSourceId           = ArrayUtility::listField($listProduct, 'source_id');
$listSourceInfo         = Source_Info::getByMultiId($listSourceId);
$mapSourceInfo          = ArrayUtility::indexByField($listSourceInfo, 'source_id');

$listSupplierId         = ArrayUtility::listField($listSourceInfo, 'supplier_id');
$listSupplierInfo       = Supplier_Info::getByMultiId($listSupplierId);
$mapSupplierInfo        = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

// 查询当前列表所有产品所属商品的规格和规格值
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$listGoodsSpecId        = ArrayUtility::listField($listGoodsSpecValue, 'spec_id');
$listGoodsSpecInfo      = Spec_Info::getByMulitId($listGoodsSpecId);
$mapGoodsSpecInfo       = ArrayUtility::indexByField($listGoodsSpecInfo, 'spec_id');
$listGoodsSpecValueId   = ArrayUtility::listField($listGoodsSpecValue, 'spec_value_id');
$listGoodsSpecValueData = Spec_Value_Info::getByMulitId($listGoodsSpecValueId);
$mapGoodsSpecValueData  = ArrayUtility::indexByField($listGoodsSpecValueData, 'spec_value_id');
foreach ($listGoodsSpecValue as $key => $goodsSpecValue) {

    $listGoodsSpecValue[$key]['spec_name']          = $mapGoodsSpecInfo[$goodsSpecValue['spec_id']]['spec_name'];
    $listGoodsSpecValue[$key]['spec_unit']          = $mapGoodsSpecInfo[$goodsSpecValue['spec_id']]['spec_unit'];
    $listGoodsSpecValue[$key]['spec_value_data']    = $mapGoodsSpecValueData[$goodsSpecValue['spec_value_id']]['spec_value_data'];
}

$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');
$mapGoodsSpecValue      = array();
foreach ($groupGoodsSpecValue as $goodsId => $goodsSpecValueList) {

    $mapGoodsSpecValue[$goodsId]  = ArrayUtility::indexByField($goodsSpecValueList, 'spec_name');
}

$listCategory           = Category_Info::listAll();
$listCategory           = ArrayUtility::searchBy($listCategory, array('delete_status'=>Category_DeleteStatus::NORMAL));
$mapCategory            = ArrayUtility::indexByField($listCategory, 'category_id', 'category_name');

$data['listProduct']        = $listProduct;
$data['mapProductImage']    = $mapProductImage;
$data['mapGoodsInfo']       = $mapGoodsInfo;
$data['mapSourceInfo']      = $mapSourceInfo;
$data['mapSupplierInfo']    = $mapSupplierInfo;
$data['mapCategory']        = $mapCategory;
$data['mapGoodsSpecValue']  = $mapGoodsSpecValue;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/product/index.tpl');