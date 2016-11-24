<?php
// 客户端断开连接时不中断脚本的执行
ignore_user_abort();

require_once dirname(__FILE__) . '/../../init.inc.php';

// 获取未处理的记录
$standby = Sales_Quotation_Task::getByRunStatus(Sales_Quotation_RunStatus::STANDBY);
if(empty($standby)){
    return ;
}
$apiUrl       = $config['apiConfig']['sales_quotation_log_file'];
$plApiUrl     = $config['apiConfig']['pl_sales_quotation_log_file'];

foreach ($standby as $info) {
    
    Sales_Quotation_Task::update(array(
        'task_id'               => $info['task_id'],
        'run_status'            => Sales_Quotation_RunStatus::RUNNING,
        'run_time'              => date('Y-m-d H:i:s'),
    ));

    $filePath   = Quotation::salesQuotationLogFile($info['sales_quotation_id']);

    HttpRequest::getInstance($apiUrl)->post(array('filePath'=>$filePath));
    HttpRequest::getInstance($plApiUrl)->post(array('filePath'=>$filePath));
    
    Sales_Quotation_Task::update(array(
        'task_id'               => $info['task_id'],
        'run_status'            => Sales_Quotation_RunStatus::FINISH,
        'is_push'               => Sales_Quotation_IsPush::YES,
        'log_file'              => $filePath,
        'finish_time'           => date('Y-m-d H:i:s'),
    ));
}