<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['produce_order_id']) {

    Utility::notice('produce_order_id is missing');
}

$produceOrderId = (int) $_GET['produce_order_id'];
$exportTaskInfo = Produce_Order_Export_Task::getByProduceOrderId($produceOrderId);
$filePath       = $exportTaskInfo['export_filepath'];
$pathConfig     = Config::get('path|PHP', 'produce_order_export');
$filePath       = $pathConfig . $filePath;

if (!is_file($filePath)) {

    Utility::notice('文件不存在');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($filePath));
readfile($filePath);