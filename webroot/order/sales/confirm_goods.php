<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

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

$listCategoryInfo           = Category_Info::listAll();
$mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = Style_Info::listAll();
$listStyleInfo              = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
$indexStyleId               = ArrayUtility::indexByField($listStyleInfo,'style_id');
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

$listGoodsInfo              = Goods_List::listByCondition($condition);
$listGoodsId                = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
$mapGoodsImages             = ArrayUtility::indexByField($listGoodsImages, 'goods_id');
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
$listSpecValueId            = array_unique(array_merge(
    $listMaterialValueId,
    $listSizeValueId,
    $listColorValueId,
    $listWeightValueId
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
    $imageKey   = $mapGoodsImages[$goodsId]['image_key'];
    $goodsInfo['image_url']     = $imageKey
        ? AliyunOSS::getInstance('thumb-images-sku')->url($imageKey)
        : '';
    $goodsInfo['product_cost']  = $mapGoodsProductMinCost[$goodsId];
    $goodsInfo['remark']        = $indexSales[$goodsId]['remark'];
    $goodsInfo['quantity']      = $indexSales[$goodsId]['goods_quantity'];
    $goodsInfo['spu_sn_list']   = implode(",",$groupSku[$goodsId]);
    $goodsInfo['source']        = implode(',', $groupProductIdSourceId[$goodsId]);

}

$data['mapCategoryInfo']            = $mapCategoryInfo;
$data['mapCategoryInfoLv3']         = $mapCategoryInfoLv3;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['groupStyleInfo']             = $groupStyleInfo;
$data['mapWeightSpecValueInfo']     = $mapWeightSpecValueInfo;
$data['mapSizeSpecValueInfo']       = $mapSizeSpecValueInfo;
$data['indexStyleId']               = $indexStyleId;
$data['mapColorSpecValueInfo']      = $mapColorSpecValueInfo;
$data['mapMaterialSpecValueInfo']   = $mapMaterialSpecValueInfo;
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
$template->assign('salesOrderId', $salesOrderId);
$template->assign('salesOrderInfo', $salesOrderInfo);
$template->display('order/sales/confirm.tpl');

