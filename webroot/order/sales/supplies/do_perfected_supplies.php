<?php

require dirname(__FILE__).'/../../../../init.inc.php';

$data   = $_POST;

if(empty($data['supplies_id']) || empty($data['supplies_au_price'])){
    
    Utility::notice('出货单Id和金价不能为空');
}
$data['supplies_status']    = 1;

$salesSuppliesInfo  = Sales_Supplies_Info::getById($data['supplies_id']);

//获取出货单商品详情
$listSuppliesProductInfo    = Sales_Supplies_Product_Info::getBySuppliesId($data['supplies_id']);

$groupProductIdSupplies     = ArrayUtility::groupByField($listSuppliesProductInfo,'product_id');
$listProductId              = array_keys($groupProductIdSupplies);

$listProductInfo            = Product_Info::getByMultiId($listProductId); 

$indexProductId             = ArrayUtility::indexByField($listProductInfo,'product_id');

//获取出货单SPU
$salesOrderGoodsInfo        = Sales_Order_Goods_Info::getBySalesOrderId($salesSuppliesInfo['sales_order_id']);
//订单中所有的sku
$indexGoodsId               = ArrayUtility::indexByField($salesOrderGoodsInfo,'goods_id');
$price = 0;
foreach($listSuppliesProductInfo as $key => $val){
    
    $price += ($data['supplies_au_price'] + $indexGoodsId[$indexProductId[$val['product_id']]['goods_id']]['cost']) * $val['supplies_weight'];
}
$data['total_price']    = $price;
Sales_Supplies_Info::update($data);

Utility::notice('保存成功','/order/sales/supplies/index.php?sales_order_id='.$salesSuppliesInfo['sales_order_id']);