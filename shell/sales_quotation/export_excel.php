<?php
/**
 * 导出excel
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$condition  = array(
    'run_status'    => Product_Export_RunStatus::STANDBY,
);
$order      = array(
    'sales_quotation_date'   => 'DESC',
);
$listInfo   = Sales_Quotation_Info::listByCondition($condition, $order, 0, 100);

foreach ($listInfo as $info) {

    $excelFile    = Quotation::getExportExcelFileByHashCode($info['hash_code']);
   
    Sales_Quotation_Info::update(array(
        'sales_quotation_id'   => $info['sales_quotation_id'],
        'run_status'             => Product_Export_RunStatus::RUNNING,
    ));
    Quotation::outputExcel($info, $excelFile);
    Sales_Quotation_Info::update(array(
        'sales_quotation_id'   => $info['sales_quotation_id'],
        'run_status'             => Product_Export_RunStatus::FINISH,
    ));
}
