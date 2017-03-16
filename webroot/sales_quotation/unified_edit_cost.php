<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId       = (int) $_POST['sales_quotation_id'];
$spuId                  = (int) $_POST['spu_id'];
$cost                   = sprintf('%.2f', trim($_POST['cost']));

$slaesQuotationInfo     = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);

if($slaesQuotationInfo['run_status'] == Product_Export_RunStatus::RUNNING || $slaesQuotationInfo['run_status'] == Product_Export_RunStatus::EDIT_RUNING){
    
    echo json_encode(array(
        'code'    => 1,
        'statusInfo'    => '文件正在生成中或者正在修改信息，请稍后修改',
    ));
    exit;
}
$spuData               = Sales_Quotation_Spu_Info::getBySalesQuotationIdAndSpuId($salesQuotationId, $spuId);
if (!$spuData) {

    echo json_encode(array(
        'code'    => 1,
        'statusInfo'    => '无数据',
    ));
    exit;
}

foreach ($spuData as $spuInfo) {

    Sales_Quotation_Spu_Info::update(array(
        'sales_quotation_id'    => $salesQuotationId,
        'spu_id'                => $spuId,
        'color_id'              => $spuInfo['color_id'],
        'cost'                  => $cost,
    ));
}

echo json_encode(array(
    'code'          => 0,
    'statusInfo'    => 'success',
));
exit;