<?php

require_once  dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_GET['produce_order_id'],'订单ID不能为空');
$produceOrderInfo   = Produce_Order_Arrive_Info::getByProduceOrderId($_GET['produce_order_id']);
if(!empty(ArrayUtility::searchBy($produceOrderInfo, array('is_storage'=>Produce_Order_Arrive_IsStorage::NO)))){
    
    Utility::notice('该订单中有未入库的到货单,无法结束订单');
}
Produce_Order_Info::update(array(
    'produce_order_id'  => $_GET['produce_order_id'],
    'status_code'       => Produce_Order_StatusCode::FINISHED,
));
Utility::notice('订单已完成','/order/produce/index.php');