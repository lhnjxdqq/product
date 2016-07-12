<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_POST['sales_order_id'],'销售订单不能为空');
Validate::testNull($_POST['goods_id'],'产品ID不能为空');
$content    = array(
    'sales_order_id'    => $_POST['sales_order_id'],
    'goods_id'          => $_POST['goods_id'],
    'goods_quantity'    => (int)$_POST['quantity'],
    'reference_weight'  => ((int) $_POST['quantity']) * $_POST['weight'],
    'remark'            => $_POST['remark'],
);


$salesGoodsOrderInfo         = Sales_Order_Goods_Info::getBySalesOrderIdAndGooodsID($_POST['sales_order_id'],$_POST['goods_id']);

if(!empty($salesGoodsOrderInfo)){
    
    Sales_Order_Goods_Info::update($content);
}else{
    
    Sales_Order_Goods_Info::create($content);
}

$salesSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($_POST['sales_order_id']);

Sales_Order_Info::update(array(
        'sales_order_id'    => $_POST['sales_order_id'],
        'count_goods'       => count($salesSkuInfo),    
        'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
        'update_time'       => date('Y-m-d H:i:s', time()),
        'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
    )
);
echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count'             => count($salesSkuInfo),
        'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
        'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
    ),
));