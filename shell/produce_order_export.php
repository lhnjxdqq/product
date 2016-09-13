<?php

/**
* 生成生产订单
*/
require_once dirname(__FILE__) . '/../init.inc.php';
// 取一条待执行的任务
$exportStatus   = Produce_Order_ExportStatus::WAITING;
$listExportTask = Produce_Order_Export_Task::getByExportStatus($exportStatus);
$exportTask     = current($listExportTask);
$produceOrderId = $exportTask['produce_order_id'];
$data           = array(
    'task_id'       => $exportTask['task_id'],
    'export_status' => Produce_Order_ExportStatus::GENERATING,
);
Produce_Order_Export_Task::update($data);
$filePath       = Produce_Order_Export_Task::export($produceOrderId);
if ($filePath) {
    $data['export_status']      = Produce_Order_ExportStatus::SUCCESS;
    $data['export_filepath']    = $filePath;
} else {
    $data['export_status']      = Produce_Order_ExportStatus::FAILED;
}
Produce_Order_Export_Task::update($data);