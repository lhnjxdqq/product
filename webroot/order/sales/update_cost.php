<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {

    echo json_encode(array(
        'code'      => 1,
        'message'   => 'method error',
    ));
    exit;
}

$status = Sales_Order_ExportStatus::GENERATING;
$taskInfo   = Sales_Order_Export_Task::getBySalesOrderId($_POST['sales_order_id']);
if (!empty($taskInfo) && $taskInfo['export_status'] == $status) {

        echo    json_encode(array(
            'code'      => 1,
            'message'   => "该订单正在生成下载文件，无法操作",
            'data'      => array(
            ),
        ));
        exit;
}else if(!empty($taskInfo)){
    Sales_Order_Export_Task::update(array(
        'task_id'           => $taskInfo['task_id'],
        'export_status'     => Sales_Order_ExportStatus::WAITING,
    ));
}

$salesOrderId   = (int) $_POST['sales_order_id'];
$goodsId        = (int) $_POST['goods_id'];
$cost           = sprintf('%.2f', (float) trim($_POST['cost']));

if (!$salesOrderId || !$goodsId) {

    echo json_encode(array(
        'code'      => 1,
        'message'   => '参数值有误',
    ));
    exit;
}

$data           = array(
    'sales_order_id'    => $salesOrderId,
    'goods_id'          => $goodsId,
    'cost'              => $cost,
);

Sales_Order_Goods_Info::update($data);

echo            json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
));
exit;