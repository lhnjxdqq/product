<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'method error',
    ));
    exit;
}

$produceOrderId = (int) $_POST['produce_order_id'];
$productId      = (int) $_POST['product_id'];
$quantity       = (int) $_POST['quantity'];

$status = Produce_Order_ExportStatus::GENERATING;
$taskInfo   = Produce_Order_Export_Task::getByProduceOrderId($produceOrderId);
if (!empty($taskInfo) && $taskInfo['export_status'] == $status) {
    
    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => '该订单正在生成下载文件，无法操作',
    ));
    exit;
}else if(!empty($taskInfo)){
    Produce_Order_Export_Task::update(array(
        'task_id'           => $taskInfo['task_id'],
        'export_status'     => Sales_Order_ExportStatus::WAITING,
    ));
}

if (!$produceOrderId || !$productId || $quantity <= 0) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'value error',
    ));
    exit;
}

$data   = array(
    'produce_order_id'  => $produceOrderId,
    'product_id'        => $productId,
    'quantity'          => $quantity,
);

if (Produce_Order_Product_Info::update($data)) {

    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => 'success',
    ));
    exit;
} else {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'update error',
    ));
    exit;
}