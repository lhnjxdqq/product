<?php

require dirname(__FILE__).'/../../../init.inc.php';

if(empty($_GET['sales_order_id'])){
    
    Utility::notice('销售订单ID不能为空');
}
Sales_Order_Info::update(array(
    'sales_order_id'    => $_GET['sales_order_id'],
    'sales_order_status'=> Sales_Order_Status::COMPLETION,
));

Utility::notice('订单已完成','/order/sales/index.php');