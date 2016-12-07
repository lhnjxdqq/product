<?php
/**
 * 生产订单导入
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$orderFileStatus = Sales_Order_File_Status::STANDBY;

$orderInfo   = Produce_Order_Arrive_Info::getByOrderFileStatus($orderFileStatus);

if(empty($orderInfo)){
	
	echo "无任务\n";
	exit;
}

Produce_Order_Arrive_Info::update(array(
	'produce_order_arrive_id'     	=> $orderInfo['produce_order_arrive_id'],
	'order_file_status'  			=> Sales_Order_File_Status::RUNNING,
));

Produce_Order_Arrive_Import::importProduceArrive($orderInfo);

Produce_Order_Arrive_Info::update(array(
	'produce_order_arrive_id'     	=> $orderInfo['produce_order_arrive_id'],
	'order_file_status'  			=> Sales_Order_File_Status::FINISH,
));
