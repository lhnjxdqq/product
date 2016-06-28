<?php
/**
 * 从SPU购物车中删除
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId     =  $_POST['sales_quotation_id'];
$spuId                =  $_POST['spu_id'];

Validate::testNull($salesQuotationId,'无效的销售报价单ID');
Validate::testNull($spuId,'无效spuID');

foreach($spuId as $id){

    Sales_Quotation_Spu_Info::getBySpuIdAndSalesQuotationIdDelete($salesQuotationId,$id);
}

$mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesQuotationId));

$spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');
$spuCount                   = count($spuInfo);
if($spuCount < 1){
    
    Sales_Quotation_Info::delete($salesQuotationId);

}else{
    Sales_Quotation_Info::update(
        array(
            'sales_quotation_id' => $salesQuotationId,
            'run_status'         => Product_Export_RunStatus::STANDBY,
            'spu_num'            => $spuCount,
        )
    );
}
echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $spuCount,
    ),
));
