<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

header('Content-type: application/json; charset=utf8');

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'method error',
    ));
    exit;
}

$userId             = (int) $_SESSION['user_id'];
$sourceCodeList     = $_POST['source_code'];
$spuIdList          = $_POST['spu_id'];

$sourceCodeToSpuId  = array();
foreach ($sourceCodeList as $offset => $sourceCode) {

    $sourceCodeToSpuId[$sourceCode][]   = $spuIdList[$offset];
    unset($offset);
    unset($sourceCode);
}

foreach ($sourceCodeToSpuId as $sourceCode => $listSpuId) {

    Sales_Quotation_Spu_Cart::delSpuCost($userId, $sourceCode, $listSpuId);
}

echo json_encode(array(
    'statusCode'    => 0,
    'statusInfo'    => 'success',
));
exit;