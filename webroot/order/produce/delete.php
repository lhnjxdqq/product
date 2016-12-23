<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['produce_order_id'])) {

    Utility::notice('produce_order_id is missing');
}

// 生产订单
$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}
Produce_Order_Info::update(array(
        'produce_order_id'  => (int) $produceOrderId,
        'status_code'       => Produce_Order_StatusCode::DELETED,
));
Utility::notice('删除成功');