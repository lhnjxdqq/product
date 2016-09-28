<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId       = (int) $_POST['sales_quotation_id'];
$spuId                  = (int) $_POST['spu_id'];
$cost                   = sprintf('%.2f', trim($_POST['cost']));

$spuData               = Sales_Quotation_Spu_Info::getBySalesQuotationIdAndSpuId($salesQuotationId, $spuId);
if (!$spuData) {

    echo json_encode(array(
        'code'    => 1,
        'statusInfo'    => 'ÎÞÊý¾Ý',
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