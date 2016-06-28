<?php
/**
 * 删除购物车中SPU
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$spuId              = (int) $_GET['spu_id'];
$salesQuotationId   = (int) $_GET['sales_quotation_id'];

Validate::testNull($salesQuotationId,'无效的销售报价单ID');
Validate::testNull($spuId,'无效spuID');

Sales_Quotation_Spu_Info::getBySpuIdAndSalesQuotationIdDelete($salesQuotationId,$spuId);

$mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesQuotationId));
$spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');
$spuCount                   = count($spuInfo);
if($spuCount < 1){
    
    Sales_Quotation_Info::delete($salesQuotationId);
    Utility::redirect('/sales_quotation/index.php');
    exit;
}else{
    Sales_Quotation_Info::update(
        array(
            'sales_quotation_id' => $salesQuotationId,
            'run_status'         => Product_Export_RunStatus::STANDBY,
            'spu_num'            => $spuCount,
        )
    );
}
Utility::redirect($_SERVER['HTTP_REFERER']);
