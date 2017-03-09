<?php

require dirname(__FILE__).'/../../../init.inc.php';

$arriveId                   = $_GET['arrive_id'];
$auPrice                    = $_GET['au_price'];
Validate::testNull($arriveId,'到货表ID不能为空');
Validate::testNull($auPrice,'金价不能为空');
//到货单信息
$produceOrderArriveInfo     = Produce_Order_Arrive_Info::getById($arriveId);
Validate::testNull($produceOrderArriveInfo,'不存在的到货单');
$produceOrderId             = $produceOrderArriveInfo['produce_order_id'];
//到货单中的产品
$arriveProductInfo          = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($arriveId);
$indexProductId             = ArrayUtility::indexByField($arriveProductInfo,'product_id');

$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}
// 生产订单详情
$listOrderProduct   = Produce_Order_List::getDetailByMultiProduceOrderId((array) $produceOrderId);
$listProductId      = ArrayUtility::listField($listOrderProduct, 'product_id');
$mapProductImage    = Common_Product::getProductThumbnail($listProductId);

// 分类信息
$listCategoryInfo   = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');

$condition['produce_order_id']  = $produceOrderId;
$condition['delete_status']     = Produce_Order_DeleteStatus::NORMAL;
$condition['produce_order_arrive_id']     = $arriveId;

$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
// 分页
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Arrive_Spu_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/storage.php',
    PageList::OPT_PERPAGE   => $perpage,
));

//已经入库的spu
$listOrderDetail    = Produce_Order_Arrive_Spu_List::listByCondition($condition, array(), $page->getOffset(), $perpage);

$listWeightSpecId   = ArrayUtility::listField($listOrderDetail,'weight_value_id');
$listColorSpecId    = ArrayUtility::listField($listOrderDetail,'color_value_id');
$listMaterialSpecId = ArrayUtility::listField($listOrderDetail,'material_value_id');
$listSpuId          = ArrayUtility::listField($listOrderDetail,'spu_id');
$listSpecValueId    = array_merge($listWeightSpecId,$listColorSpecId,$listMaterialSpecId);
$listSpecInfo       = Spec_Value_Info::getByMulitId($listSpecValueId);
$indexSpecId                = ArrayUtility::indexByField($listSpecInfo,'spec_value_id');
$condition['list_spu_id']   = $listSpuId;
//订单全部spu
$productOrderInfo     = Produce_Order_Spu_List::listByCondition($condition, array(), 0, 20);
$orderIndexSpuId      = ArrayUtility::groupByField($productOrderInfo,'spu_sn');
foreach($orderIndexSpuId as $spuSn => $info){
    
    $orderIndexSpuId[$spuSn]    = ArrayUtility::indexByField($info,'color_value_id');
}
$listSpuImages              = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuIdImages           = ArrayUtility::groupByField($listSpuImages, 'spu_id');

foreach ($listOrderDetail as &$detail) {
    
	$imageInfo                      = $groupSpuIdImages[$detail['spu_id']];
    
    $firstImageInfo = array();
    if(!empty($imageInfo)){
        
        $firstImageInfo = ArrayUtility::searchBy($imageInfo,array('is_first_picture' => 1));
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info = current($firstImageInfo);
        $detail['image_url']  = !empty($info)
            ? AliyunOSS::getInstance('images-spu')->url($info['image_key'])
            : '';       
    }else{

        $info = Sort_Image::sortImage($imageInfo);

        $detail['image_url']  = !empty($info)
            ? AliyunOSS::getInstance('images-spu')->url($info[0]['image_key'])
            : '';     
    }
    $categoryId                         = $detail['category_id'];
    $childStyleId                       = $detail['style_id'];
    $parentStyleId                      = $mapStyleInfo[$childStyleId]['parent_id'];
    $detail['category_name']            = $mapCategoryInfo[$categoryId]['category_name'];
    $detail['image_url']                = $mapProductImage[$productId]['image_url'];
    $detail['weight_value_data']        = $indexSpecId[$detail['weight_value_id']]['spec_value_data'];
    $detail['color_value_data']         = $indexSpecId[$detail['color_value_id']]['spec_value_data'];
    $detail['material_value_data']      = $indexSpecId[$detail['material_value_id']]['spec_value_data'];
    $detail['order_quantity_quantity']  = $orderIndexSpuId[$detail['spu_sn']][$detail['color_value_id']]['total_quantity'];
    $detail['product_cost']             = $orderIndexSpuId[$detail['spu_sn']][$detail['color_value_id']]['product_cost'];
}

$data['produceOrderInfo']   = $produceOrderInfo;
$data['listOrderDetail']    = $listOrderDetail;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();
$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('produceOrderArriveInfo', $produceOrderArriveInfo);
$template->display('order/produce/do_storage.tpl');