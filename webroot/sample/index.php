<?php
/**
 * 样板列表
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';
$condition              = $_GET;
$userId                 = $_SESSION['user_id'];
$condition['delete_status'] = Goods_DeleteStatus::NORMAL;
$condition['is_delete']     = Goods_DeleteStatus::NORMAL;
$listSampleType = Sample_Type::getSampleType();
$sampleTypeInfo  = array();
foreach($listSampleType as $typeId=>$typeName){
    
    $sampleTypeInfo[$typeId]['type_id']    = $typeId;
    $sampleTypeInfo[$typeId]['type_name']  = $typeName;
}

$page                   = new PageList(array(
    PageList::OPT_TOTAL     => isset($condition['category_id'])
                               ? Search_Sample::countByCondition($condition)
                               : Sample_List::countByCondition($condition),
    PageList::OPT_URL       => '/sample/index.php',
    PageList::OPT_PERPAGE   => 20,
));

$listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = Style_Info::listAll();
$listStyleInfo              = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
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

$listGoodsInfo              = isset($condition['category_id'])
                              ? Search_Sample::listByCondition($condition, array(), $page->getOffset(), 20)
                              : Sample_List::listByCondition($condition, array(), $page->getOffset(), 20);
$listGoodsId                = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
$groupGoodsIdImages         = ArrayUtility::groupByField($listGoodsImages, 'goods_id');
$listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
$groupGoodsProductInfo      = ArrayUtility::groupByField($listGoodsProductInfo, 'goods_id');
$mapGoodsProductMinCost     = array();
foreach ($groupGoodsProductInfo as $goodsId => $goodsProductList) {

    $goodsProductList   = ArrayUtility::sortMultiArrayByField($goodsProductList, 'product_cost');
    $goodsProductInfo   = current($goodsProductList);
    $mapGoodsProductMinCost[$goodsId]   = $goodsProductInfo['product_cost'];
}
$listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
$listSourceId   = ArrayUtility::listField($listGoodsProductInfo,'source_id');
$mapSourceInfo  = Source_Info::getByMultiId($listSourceId);
$indexSourceInfo= ArrayUtility::indexByField($mapSourceInfo,'source_id','source_code');
$groupSkuSourceId   = ArrayUtility::groupByField($listGoodsProductInfo,'goods_id','source_id');
$groupProductIdSourceId = array();
foreach($groupSkuSourceId as $productId => $sourceIdInfo){
    
    $groupProductIdSourceId[$productId]    = array();
    foreach($sourceIdInfo as $key=>$sourceId){

        $groupProductIdSourceId[$productId][] = $indexSourceInfo[$sourceId];   
    }
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
$countSampleGoods           = Cart_Sample_Info::countByUser($userId);
$cartSampleInfo             = Cart_Sample_Info::getByUserId($userId);
$listCartSampleId           = ArrayUtility::listField($cartSampleInfo,'goods_id');

foreach ($listGoodsInfo as &$goodsInfo) {

    $goodsId    = $goodsInfo['goods_id'];
        
    if(!empty($groupGoodsIdImages[$goodsId])){
        
        $firstImageInfo = ArrayUtility::searchBy($groupGoodsIdImages[$goodsId],array('is_first_picture' => 1));
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info       = current($firstImageInfo);
        $imageKey   = $info['image_key'];
    }else{

        $info       = Sort_Image::sortImage($groupGoodsIdImages[$goodsId]);
        $imageKey   = $info[0]['image_key'];
    }

    $goodsInfo['image_url']     = $imageKey
        ? AliyunOSS::getInstance('images-sku')->url($imageKey)
        : '';
    $goodsInfo['product_cost']  = $mapGoodsProductMinCost[$goodsId];
    if(in_array($goodsId,$listCartSampleId)){
        
        $goodsInfo['is_cart']   = 1;   
    }
    
    $goodsInfo['source']        = implode(',', $groupProductIdSourceId[$goodsId]);
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
$template->assign('listSampleType', $listSampleType);
$template->assign('countCartGoods', $countSampleGoods);
$template->assign('sampleTypeInfo', $sampleTypeInfo);
$template->display('sample/index.tpl');