<?php
/**
 * 销售订单导入
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$condition  = array(
    'order_file_status'    => Sales_Order_File_Status::STANDBY,
);
$order      = array(
    'create_time'   => 'DESC',
);
$listInfo   = Sales_Order_Info::listByCondition($condition, $order, 0, 100);

foreach ($listInfo as $info) {

    $excelFile    = Order::getFilePathByOrderSn($info['sales_order_sn']);

    Sales_Order_Info::update(array(
        'sales_order_id'     => $info['sales_order_id'],
        'order_file_status'  => Sales_Order_File_Status::RUNNING,
    ));
    Sales_Order_Import::updateSalesOrderSku($info, $excelFile);

    Sales_Order_Info::update(array(
        'sales_order_id'     => $info['sales_order_id'],
        'order_file_status'  => Sales_Order_File_Status::FINISH,
    ));
    
}
