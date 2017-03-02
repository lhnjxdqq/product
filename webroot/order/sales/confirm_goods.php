<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

if(strstr($_SERVER['HTTP_REFERER'],'/order/sales/index.php')){
    
    $_SESSION['order_sales_index']  = $_SERVER['HTTP_REFERER'];
}

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

$status = Sales_Order_ExportStatus::GENERATING;
$taskInfo   = Sales_Order_Export_Task::getBySalesOrderId($salesOrderId);
if (!empty($taskInfo) && $taskInfo['export_status'] == $status) {

    Utility::notice("该订单正在生成下载文件，无法操作",'/order/sales/index.php');
    exit;
}else if(!empty($taskInfo)){
    Sales_Order_Export_Task::update(array(
        'task_id'           => $taskInfo['task_id'],
        'export_status'     => Sales_Order_ExportStatus::WAITING,
    ));
}
$listValuatimType = Valuation_TypeInfo::getValuationType();
//获取对应销售报价单中的所有SPU
$salesQuotationSpuInfo = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesOrderInfo['sales_quotation_id']));
$groupSpuInfo       = ArrayUtility::groupByField($salesQuotationSpuInfo,'spu_id');
$groupSalesQuotationSpuId   = ArrayUtility::groupByField($salesQuotationSpuInfo,'spu_id');

//获取所有订单详情中的所有sku
$salesGoodsInfo     =  Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
//订单中所有的sku
$listGoods          = ArrayUtility::listField($salesGoodsInfo,'goods_id');

//销售订单中的备注
$indexSales   = ArrayUtility::indexByField($salesGoodsInfo,'goods_id');

if(empty($listGoods)){
    
    Utility::notice('销售订单中没有产品','/order/sales/add_goods.php?sales_order_id='.$salesOrderId);
}

$skuRelationShipSpuInfo = Spu_Goods_RelationShip::getByMultiGoodsId($listGoods);

// 该销售订单中SKU相关的SPU数量
$salesOrderSkuSpuRelation   = Spu_Goods_RelationShip::getByMultiGoodsId($listGoods);
$listRelationSpuId          = array_unique(ArrayUtility::listField($salesOrderSkuSpuRelation, 'spu_id'));
$countRelationSpu           = count($listRelationSpuId);

//查出所有关联Spu的信息
$listSpuInfo        = ArrayUtility::indexByField(Spu_Info::getByMultiId(ArrayUtility::listField($skuRelationShipSpuInfo,'spu_id')),'spu_id');

foreach($skuRelationShipSpuInfo as &$info) {
    $info = &$info;
    $info['spu_sn'] = $listSpuInfo[$info['spu_id']]['spu_sn'];
}
//把spu按sku分组
$groupSku               = ArrayUtility::groupByField($skuRelationShipSpuInfo,'goods_id','spu_sn');
$groupSkuSpu            = ArrayUtility::groupByField($skuRelationShipSpuInfo,'goods_id','spu_id');

$condition['list_goods_id'] = $listGoods;

$listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = Style_Info::listAll();
$listStyleInfo              = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
$indexStyleId               = ArrayUtility::indexByField($listStyleInfo,'style_id');
$groupStyleInfo             = ArrayUtility::groupByField($listStyleInfo, 'parent_id');

$listGoodsInfo              = Goods_List::listByCondition($condition);
$listGoodsId                = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
$groupGoodsIdImages             = ArrayUtility::groupByField($listGoodsImages, 'goods_id');
$listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
$listSourceId               = ArrayUtility::listField($listGoodsProductInfo,'source_id');
$mapSourceInfo              = Source_Info::getByMultiId($listSourceId);
$indexSourceInfo            = ArrayUtility::indexByField($mapSourceInfo,'source_id','source_code');
$groupSkuSourceId           = ArrayUtility::groupByField($listGoodsProductInfo,'goods_id','source_id');
$groupProductIdSourceId     = array();
foreach($groupSkuSourceId as $productId => $sourceIdInfo){
    
    $groupProductIdSourceId[$productId]    = array();
    foreach($sourceIdInfo as $key=>$sourceId){

        $groupProductIdSourceId[$productId][] = $indexSourceInfo[$sourceId];   
    }
}
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
$listAssistantMaterialValueId          = ArrayUtility::listField($listGoodsInfo, 'assistant_material_value_id');
$listSpecValueId            = array_unique(array_merge(
    $listMaterialValueId,
    $listSizeValueId,
    $listColorValueId,
    $listWeightValueId,
    $listAssistantMaterialValueId
));
$listSpecValueInfo          = Spec_Value_Info::getByMulitId($listSpecValueId);
$mapSpecValueInfo           = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

foreach ($listGoodsInfo as &$goodsInfo) {

    $goodsId    = $goodsInfo['goods_id'];
    
    foreach($groupSkuSpu[$goodsId] as $key=>$sku){

        if(empty($groupSpuInfo[$sku])){
            
            continue;
        }
        
        foreach($groupSpuInfo[$sku] as $spu=>$quotationSku){
           
           $goodsInfo['cost']   = $quotationSku['cost'];
           continue;
        }
    }
    $firstImageInfo = array();
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
    $goodsInfo['remark']        = $indexSales[$goodsId]['remark'];
    $goodsInfo['quantity']      = $indexSales[$goodsId]['goods_quantity'];
    $goodsInfo['spu_sn_list']   = implode(",",$groupSku[$goodsId]);
    $goodsInfo['source']        = implode(',', array_unique($groupProductIdSourceId[$goodsId]));

}

$data['mapCategoryInfo']            = $mapCategoryInfo;
$data['mapCategoryInfoLv3']         = $mapCategoryInfoLv3;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['groupStyleInfo']             = $groupStyleInfo;
$data['indexStyleId']               = $indexStyleId;
$data['searchType']                 = Search_Sku::getSearchType();
$data['mapSpecValueInfo']           = $mapSpecValueInfo;
$data['listGoodsInfo']              = $listGoodsInfo;
$data['mainMenu']                   = Menu_Info::getMainMenu();
$data['onlineStatus']               = array(
    'online'    => Goods_OnlineStatus::ONLINE,
    'offline'   => Goods_OnlineStatus::OFFLINE,
);

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listValuatimType', $listValuatimType);
$template->assign('indexSales', $indexSales);
$template->assign('salesOrderId', $salesOrderId);
$template->assign('salesOrderInfo', $salesOrderInfo);
$template->assign('countRelationSpu', $countRelationSpu);
$template->display('order/sales/confirm.tpl');

