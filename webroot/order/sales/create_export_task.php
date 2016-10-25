<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

/*******销售订单导出********/
if (!$_GET['sales_order_id']) {

    Utility::notice('sales_order_id is missing');
}

$salesOrderId       = $_GET['sales_order_id'];
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);

if (!$salesOrderInfo || $salesOrderInfo['delete_status'] == Sales_Order_Status::DELETE) {

    Utility::dump('销售订单不存在或状态异常');
}

$data   = array(
    'sales_order_id'    => $salesOrderId,
    'export_status'     => Sales_Order_ExportStatus::WAITING,
    'export_filepath'   => '',
);

$exportTask = Sales_Order_Export_Task::getBySalesOrderId($salesOrderId);
$func       = empty($exportTask) ? 'create' : 'update';
if ($func == 'update') {

    $data['task_id']    = $exportTask['task_id'];
}

if (Sales_Order_Export_Task::$func($data)) {

    Utility::notice('创建导出任务成功, 请稍后下载', '/order/sales/index.php');
} else {

    Utility::notice('操作失败');
}