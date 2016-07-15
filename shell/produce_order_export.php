<?php
require_once dirname(__FILE__) . '/../init.inc.php';

// 取一条待执行的任务
$exportStatus   = Produce_Order_ExportStatus::WAITING;
$listExportTask = Produce_Order_Export_Task::getByExportStatus($exportStatus);
$exportTask     = current($listExportTask);
$produceOrderId = $exportTask['produce_order_id'];

Produce_Order_Export_Task::export($produceOrderId);