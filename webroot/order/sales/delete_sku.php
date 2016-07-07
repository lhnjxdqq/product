<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$data      = $_GET;

Validate::testNull($data['sales_order_id'], "销售订单ID不能为空");
Validate::testNull($data['goods_id'], "skuId不能为空");

Sales_Order_Goods_Info::getBySkuIdAndSalesOrderIdDelete($data['sales_order_id'],$data['goods_id']);

$salesSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($data['sales_order_id']);

Sales_Order_Info::update(array(
        'sales_order_id'    => $data['sales_order_id'],
        'count_goods'       => count($salesSkuInfo),    
        'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
        'update_time'       => date('Y-m-d H:i:s', time()),
        'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
    )
);

Utility::redirect($_SERVER['HTTP_REFERER']);