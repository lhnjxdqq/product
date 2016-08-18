<?php
/**
 * 导出excel
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$listInfo   = Borrow_Export_Task::getByExportStatus(Borrow_Export_Status::STANDBY);

foreach ($listInfo as $info) {

    Borrow_Export_Task::update(array(
        'task_id'         => $info['task_id'],
        'export_status'   => Product_Export_RunStatus::RUNNING,
    ));

    $filePath   = Borrow::outputExcel($info);

    Borrow_Export_Task::update(array(
        'task_id'         => $info['task_id'],
        'export_filepath' => $filePath,
        'export_status'   => Product_Export_RunStatus::FINISH,
    ));
}