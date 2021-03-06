<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

//获取所有订单详情中的所有sku
$salesGoodsInfo     =  Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);

//订单中所有的sku
$listGoods          = ArrayUtility::listField($salesGoodsInfo,'goods_id');

//销售订单中的备注
$indexSales   = ArrayUtility::indexByField($salesGoodsInfo,'goods_id');

$salesSupplesProductInfo    = ArrayUtility::searchBy(Sales_Supplies_Info::getBySalesOrderId($salesOrderId),array('supplies_status'=>Sales_Supplies_Status::DELIVREED));

//获取出货单
$salesSuppliesId            = ArrayUtility::listField($salesSupplesProductInfo,'supplies_id');
$sumShipment  = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'supplies_quantity_total'));

//获取出货单商品详情
$listSuppliesProductInfo    = Sales_Supplies_Product_Info::getByMultiSuppliesId($salesSuppliesId);

$groupProductIdSupplies     = ArrayUtility::groupByField($listSuppliesProductInfo,'product_id');

$listProductId              = array_keys($groupProductIdSupplies);
$listProductInfo            = Product_Info::getByMultiId($listProductId); 
$indexGoodsId               = ArrayUtility::indexByField($listProductInfo,'goods_id');



if(empty($listGoods)){
    
    Utility::notice('销售订单中没有产品','/order/sales/index.php');
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
$groupGoodsIdImages         = ArrayUtility::groupByField($listGoodsImages, 'goods_id');
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
    $productId  = $indexGoodsId[$goodsId]['product_id'];
    
    if($productId){
        
        $goodsInfo['supplies_weight']       =   array_sum(ArrayUtility::listField($groupProductIdSupplies[$productId],'supplies_weight')); 
        $goodsInfo['supplies_quantity']     =   array_sum(ArrayUtility::listField($groupProductIdSupplies[$productId],'supplies_quantity')); 
    }

    foreach($groupSkuSpu[$goodsId] as $key=>$sku){

        if(empty($groupSpuInfo[$sku])){
            
            continue;
        }
        
        foreach($groupSpuInfo[$sku] as $spu=>$quotationSku){
           
           $goodsInfo['cost']   = $quotationSku['cost'];
           continue;
        }
    }
        
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
    $goodsInfo['source']        = implode(',', $groupProductIdSourceId[$goodsId]);

}

$statusList = Sales_Order_Status::getOrderStatus();

foreach($statusList as $statusId=>$statusName){
    
    $mapOrderStatus[$statusId] = array(
        
        'status_id'     => $statusId,
        'status_name'   => $statusName,
    );
}

$orderTypeInfo = Sales_Order_Type::getOrderType();

foreach($orderTypeInfo as $orderTypeId=>$orderName){
    
    $mapOrderStyle[$orderTypeId] = array(
        
        'order_type_id'     => $orderTypeId,
        'order_type_name'        => $orderName,
    );
}
$mapCustomer        = ArrayUtility::indexByField(ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL)),'customer_id');

$mapUser            = ArrayUtility::indexByField(User_Info::listAll(),'user_id');

$mapSalesperson 	= ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$data['mapCategoryInfoLv3']         = $mapCategoryInfoLv3;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['groupStyleInfo']             = $groupStyleInfo;
$data['mapWeightSpecValueInfo']     = $mapWeightSpecValueInfo;
$data['mapSizeSpecValueInfo']       = $mapSizeSpecValueInfo;
$data['mapColorSpecValueInfo']      = $mapColorSpecValueInfo;
$data['mapMaterialSpecValueInfo']   = $mapMaterialSpecValueInfo;

foreach($listGoodsInfo as $key =>$val){
	
	$export	= array();
	$export['goods_sn']				= $val['goods_sn'];
	$export['spu_sn_list']			= $val['spu_sn_list'];
	$export['source']				= $val['source'];
	$export['goods_name']			= $val['goods_name'];
	$export['category_name']		= $mapCategoryInfo[$val['category_id']]['category_name'];
	$export['style_name']			= $indexStyleId[$indexStyleId[$val['style_id']]['parent_id']]['style_name'];
	$export['two_style_name']		= $indexStyleId[$val['style_id']]['style_name'];
	$export['weight_value_name']	= $mapSpecValueInfo[$val['weight_value_id']]['spec_value_data'];
	$export['size_value_name']		= $mapSpecValueInfo[$val['size_value_id']]['spec_value_data'];
	$export['color_value_name']		= $mapSpecValueInfo[$val['color_value_id']]['spec_value_data'];
	$export['material_value_name']	= $mapSpecValueInfo[$val['material_value_id']]['spec_value_data'];
	$export['cost']					= $indexSales[$val['goods_id']]['cost'];
	$export['remark']				= $val['remark'];
	$export['quantity']				= $val['quantity'];
	$export['supplies_quantity']	= $val['supplies_quantity'];
	$export['supplies_weight']		= $val['supplies_weight'];
	
	$download[] =$export;
	
}

// 这里是数据转换并下载
header('Content-type:text/csv');
header("Content-Disposition:attachment;filename=".$salesOrderInfo['sales_order_sn'].".csv");
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');

$fp = fopen('php://output' , 'w');
fputcsv($fp, array_map('Utility::utf8ToGb' , array('SKU编号' , '关联SPU' , '买款ID' , 'SKU名称' , '三级分类' , '款式' , '子款式' , '规格重量' , '规格尺寸' , '颜色' , '主料材质' , '出货工费' , '备注' , '下单件数' , '出货件数' , '出货重量')));
foreach ($download as $v) {

	fputcsv($fp, array_map('Utility::utf8ToGb' , $v));
}

fclose($fp);
exit;
