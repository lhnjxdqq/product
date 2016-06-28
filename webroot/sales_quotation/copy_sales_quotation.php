<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId   = (int) $_POST['sales_quotation_id'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");

$salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);
Validate::testNull($salesQuotationInfo, "不存在此报价单");
$mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesQuotationInfo['sales_quotation_id']));

$slaesQuotation = array(
        'markup_rule'          => $salesQuotationInfo['markup_rule'],
        'author_id'            => $_SESSION['user_id'],
        'sales_quotation_name' => $salesQuotationInfo['sales_quotation_name']."的附件",
        'customer_id'          => $salesQuotationInfo['customer_id'],
        'spu_num'              => $salesQuotationInfo['spu_num'],
        'hash_code'            => md5(time()),
        'run_status'           => Product_Export_RunStatus::STANDBY,
    );

$newSalesQuotationId   = Sales_Quotation_Info::create($slaesQuotation);

foreach($mapSalesQuotationSpuInfo as $key => $info){
     
    $content = array(
        'sales_quotation_id'    => $newSalesQuotationId,
        'spu_id'                => $info['spu_id'],
        'cost'                  => $info['cost'],
        'color_id'              => $info['color_id'],
        'sales_quotation_remark'=> $info['sales_quotation_remark'],
    );       
    Sales_Quotation_Spu_Info::create($content);
}
echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
    
    ),
));