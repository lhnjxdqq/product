<?php

/**
* 生成销售订单
*/
require_once dirname(__FILE__) . '/../init.inc.php';

// 判断是否有正在执行的脚本
$status = Sales_Order_ExportStatus::GENERATING;
if ( Sales_Order_Export_Task::getByExportStatus($status) ) {

    exit('有正在执行的任务,请稍候重试!'. "\n");
}

// define('FILE_LOCK', ROOT . '/data/file_tag');

// if (is_file(FILE_LOCK)) {

//     exit("File is locked. Please try again later . \n");
// }

// file_put_contents(FILE_LOCK, "it's locked");

// 取一条待执行的任务
$exportStatus   = Sales_Order_ExportStatus::WAITING;
$listExportTask = Sales_Order_Export_Task::getByExportStatus($exportStatus);

if (!$listExportTask) {

    exit('无导出任务,请稍候重试!' . "\n");
}

foreach ($listExportTask as $exportTask) {

    // $exportTask     = current($listExportTask);
    $salesOrderId   = $exportTask['sales_order_id'];
    $data           = array(
        'task_id'       => $exportTask['task_id'],
        'export_status' => Sales_Order_ExportStatus::GENERATING,
    );

    // 修改状态为正在执行
    Produce_Order_Export_Task::update($data);

    echo "生成销售订单开始!\n";
    echo "订单号为{$salesOrderId}\n";

    $filePath       = Sales_Order_Export_Task::export($salesOrderId);
    if ($filePath) {

        // 执行成功
        $data['export_status']      = Sales_Order_ExportStatus::SUCCESS;
        $data['export_filepath']    = $filePath;
        echo "生成销售订单成功!\n";

    } else {

        // 执行失败
        $data['export_status']      = Sales_Order_ExportStatus::FAILED;
        echo "生成销售订单失败!\n";

    }
    // 更新状态
    Sales_Order_Export_Task::update($data);
}

// @unlink(FILE_LOCK);