<?php

require dirname(__FILE__).'/../../../init.inc.php';

if(empty($_GET['sales_order_id'])){
    
    Utility::notice('销售订单ID不能为空');
}
$assignSumGoods     = Api_Controller_Order::getGoodsAssignQuantityBySalesOrderId($_GET['sales_order_id']);
$salesOrderGoods    =  Sales_Order_Goods_Info::getBySalesOrderId($_GET['sales_order_id']);

$salesOrderStatus   = Sales_Order_Status::COMPLETION;

if(!empty($salesOrderGoods)){

    foreach($salesOrderGoods as $key => $salesGoodsInfo){
        
        if($salesGoodsInfo['goods_quantity'] > $assignSumGoods[$salesGoodsInfo['goods_id']]['assignQuantity']){

            $salesOrderStatus = Sales_Order_Status::PARTIALLY_OUT_OF_STOCK;
            break;
        }
    }   
}

Sales_Order_Info::update(array(
    'sales_order_id'    => $_GET['sales_order_id'],
    'sales_order_status'=> $salesOrderStatus,
));
Utility::notice('订单已完成','/order/sales/supplies/index.php?sales_order_id='.$_GET['sales_order_id']);  