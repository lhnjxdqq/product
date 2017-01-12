<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$userId                 = (int) $_SESSION['user_id'];
$condition              = $_GET;
$condition['delete_status'] = Goods_DeleteStatus::NORMAL;

// 判断上下架状态
if ( !empty($condition['online_status']) ) {
    
    if ( ($condition['online_status'] != 1) && ($condition['online_status'] != 2) ) {
        Utility::notice('上下架状态不对,请重试');
    }
}


$page                   = new PageList(array(
    PageList::OPT_TOTAL     => isset($condition['category_id'])
                               ? Search_Sku::countByCondition($condition)
                               : Goods_List::countByCondition($condition),
    PageList::OPT_URL       => '/product/sku/index.php',
    PageList::OPT_PERPAGE   => 100,
));

$listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
$groupStyleInfo             = ArrayUtility::groupByField($listStyleInfo, 'parent_id');

$weightSpecInfo             = Spec_Info::getByAlias('weight');
$listWeightSpecValue        = Goods_Type_Spec_Value_Relationship::getBySpecId($weightSpecInfo['spec_id']);
$listWeightSpecValueId      = array_unique(ArrayUtility::listField($listWeightSpecValue, 'spec_value_id'));
$listWeightSpecValueInfo    = Spec_Value_Info::getByMulitId($listWeightSpecValueId);
$mapWeightSpecValueInfo     = ArrayUtility::indexByField($listWeightSpecValueInfo, 'spec_value_id');

$sizeSpecInfo               = Spec_Info::getByAlias('size');
$listSizeSpecValue          = Goods_Type_Spec_Value_Relationship::getBySpecId($sizeSpecInfo['spec_id']);
$listSizeSpecValueId        = array_unique(ArrayUtility::listField($listSizeSpecValue, 'spec_value_id'));
$listSizeSpecValueInfo      = Spec_Value_Info::getByMulitId($listSizeSpecValueId);
$mapSizeSpecValueInfo       = ArrayUtility::indexByField($listSizeSpecValueInfo, 'spec_value_id');

$colorSpecInfo              = Spec_Info::getByAlias('color');
$listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
$listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

$materialSpecInfo           = Spec_Info::getByAlias('material');
$listMaterialSpecValue      = Goods_Type_Spec_Value_Relationship::getBySpecId($materialSpecInfo['spec_id']);
$listMaterialSpecValueId    = array_unique(ArrayUtility::listField($listMaterialSpecValue, 'spec_value_id'));
$listMaterialSpecValueInfo  = Spec_Value_Info::getByMulitId($listMaterialSpecValueId);
$mapMaterialSpecValueInfo   = ArrayUtility::indexByField($listMaterialSpecValueInfo, 'spec_value_id');
$listGoodsInfo              = array();
$listGoodsInfo              = isset($condition['category_id'])
                              ? Search_Sku::listByCondition($condition, array(), $page->getOffset(), 100)
                              : Goods_List::listByCondition($condition, array(), $page->getOffset(), 100);
$listGoodsId                = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
$groupGoodsIdImages         = ArrayUtility::groupByField($listGoodsImages, 'goods_id');
$listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
$groupGoodsProductInfo      = array();
$groupGoodsProductInfo      = ArrayUtility::groupByField($listGoodsProductInfo, 'goods_id');
$mapGoodsProductMinCost     = array();
foreach ($groupGoodsProductInfo as $goodsId => $goodsProductList) {

    $goodsProductList   = ArrayUtility::sortMultiArrayByField($goodsProductList, 'product_cost');
    $goodsProductInfo   = current($goodsProductList);
    $mapGoodsProductMinCost[$goodsId]   = $goodsProductInfo['product_cost'];
}

$listMaterialValueId        = ArrayUtility::listField($listGoodsInfo, 'material_value_id');
$listSizeValueId            = ArrayUtility::listField($listGoodsInfo, 'size_value_id');
$listColorValueId           = ArrayUtility::listField($listGoodsInfo, 'color_value_id');
$listWeightValueId          = ArrayUtility::listField($listGoodsInfo, 'weight_value_id');
$listSpecValueId            = array_unique(array_merge(
    $listMaterialValueId,
    $listSizeValueId,
    $listColorValueId,
    $listWeightValueId
));
$listSpecValueInfo          = Spec_Value_Info::getByMulitId($listSpecValueId);
$mapSpecValueInfo           = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

$listSampleGoodsId          = ArrayUtility::listField(ArrayUtility::searchBy(Sample_Info::getByMultiId($listGoodsId),array('is_delete'=>Goods_DeleteStatus::NORMAL)),'goods_id');

foreach ($listGoodsInfo as &$goodsInfo) {

    $goodsId    = $goodsInfo['goods_id'];

	$firstImageInfo	= array();
    if(!empty($groupGoodsIdImages[$goodsId])){
        
        $firstImageInfo = ArrayUtility::searchBy($groupGoodsIdImages[$goodsId],array('is_first_picture' => 1));
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info = current($firstImageInfo);
        $goodsInfo['image_url']     = !empty($info)
            ? AliyunOSS::getInstance('images-sku')->url($info['image_key'])
            : '';       
    }else{

        $info = Sort_Image::sortImage($groupGoodsIdImages[$goodsId]);

        $goodsInfo['image_url']     = !empty($info)
            ? AliyunOSS::getInstance('images-sku')->url($info[0]['image_key'])
            : '';     
    }

    $goodsInfo['product_cost']  = $mapGoodsProductMinCost[$goodsId];

    if(in_array($goodsId,$listSampleGoodsId)){
        
        $goodsInfo['is_sample']   = 1;   
    }
}

$data['mapCategoryInfo']            = $mapCategoryInfo;
$data['mapCategoryInfoLv3']         = $mapCategoryInfoLv3;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['groupStyleInfo']             = $groupStyleInfo;
$data['mapWeightSpecValueInfo']     = $mapWeightSpecValueInfo;
$data['mapSizeSpecValueInfo']       = $mapSizeSpecValueInfo;
$data['mapColorSpecValueInfo']      = $mapColorSpecValueInfo;
$data['mapMaterialSpecValueInfo']   = $mapMaterialSpecValueInfo;
$data['searchType']                 = Search_Sku::getSearchType();
$data['mapSpecValueInfo']           = $mapSpecValueInfo;
$data['listGoodsInfo']              = $listGoodsInfo;
$data['pageViewData']               = $page->getViewData();
$data['mainMenu']                   = Menu_Info::getMainMenu();
$data['onlineStatus']               = array(
    'online'    => Goods_OnlineStatus::ONLINE,
    'offline'   => Goods_OnlineStatus::OFFLINE,
);

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('countCartGoods', $countCartGoods);
$template->display('product/sku/index.tpl');