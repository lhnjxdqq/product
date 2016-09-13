<?php
/**
 * 导出excel
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$condition  = array(
    'refund_file_status'        => Produce_Order_Arrive_RefundStatus::WAIT_TO_START,
    'is_storage'                => Produce_Order_Arrive_IsStorage::YES,
);
$order      = array(
    'produce_order_arrive_id'   => 'DESC',
);
$listInfo   = Produce_Order_Arrive_Info::listByCondition($condition, $order, 0, 100);

foreach ($listInfo as $info) {

    Produce_Order_Arrive_Info::update(array(
        'produce_order_arrive_id'   => $info['produce_order_arrive_id'],
        'refund_file_status'        => Produce_Order_Arrive_RefundStatus::RUNNING,
    ));
    
    $path =  Arrive::outputExcel($info);

    Produce_Order_Arrive_Info::update(array(
        'refund_file_path'          => $path,
        'produce_order_arrive_id'   => $info['produce_order_arrive_id'],
        'refund_file_status'        => Produce_Order_Arrive_RefundStatus::FINISH,
    ));
}
