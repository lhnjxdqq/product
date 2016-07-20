<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['produce_order_id']) {

    Utility::notice('produce_order_id is missing');
}

$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);

if (!$produceOrderInfo || $produceOrderInfo['delete_status'] == Produce_Order_DeleteStatus::DELETED) {

    Utility::dump('生产订单不存在或状态异常');
}

$data   = array(
    'produce_order_id'  => $produceOrderId,
    'export_status'     => Produce_Order_ExportStatus::WAITING,
    'export_filepath'   => '',
);

$exportTask = Produce_Order_Export_Task::getByProduceOrderId($produceOrderId);
$func       = empty($exportTask) ? 'create' : 'update';
if ($func == 'update') {

    $data['task_id']    = $exportTask['task_id'];
}

if (Produce_Order_Export_Task::$func($data)) {

    Utility::notice('创建导出任务成功, 请稍后下载', '/order/produce/index.php');
} else {

    Utility::notice('操作失败');
}