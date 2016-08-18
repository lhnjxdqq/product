<?php
/**
 * 下载文件
 */
require_once dirname(__FILE__) . '/../../../init.inc.php';

$borrowId   = $_GET['borrow_id'];
if (empty($borrowId)) {

    throw   new ApplicationException('借板Id不存在');
}

$taskInfo   = Borrow_Export_Task::getByBorrowId($borrowId);

$path = $taskInfo['export_filepath'];

if (empty($path) || !is_file($path)) {

    throw   new ApplicationException('文件不存在');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=export.xlsx');
readfile($path);
