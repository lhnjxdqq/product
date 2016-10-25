<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id']) {

    Utility::notice('sales_order_id is missing');
}

$salesOrderId = (int) $_GET['sales_order_id'];
$exportTaskInfo = Sales_Order_Export_Task::getBySalesOrderId($salesOrderId);
$filePath       = $exportTaskInfo['export_filepath'];
$pathConfig     = Config::get('path|PHP', 'sales_order_export');
$filePath       = $pathConfig . $filePath;
// echo $filePath;exit;
if (!is_file($filePath)) {

    Utility::notice('文件不存在');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($filePath));
readfile($filePath);