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

$userId         = (int) $_SESSION['user_id'];
$sourceCode     = trim($_POST['source_code']);
$spuId          = (int) $_POST['spu_id'];
$spuRemark      = trim($_POST['spu_remark']);


$cartData       = Sales_Quotation_Spu_Cart::getByPrimaryKey($userId, $sourceCode);
if (!$cartData) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'no data',
    ));
    exit;
}

$spuListField   = json_decode($cartData['spu_list'], true);
foreach ($spuListField as &$spuCost) {

    if ($spuId == $spuCost['spuId']) {


        $spuCost['remark']   = $spuRemark;
    }
    unset($spuCost);
}

$updateData     = array(
    'user_id'       => $userId,
    'source_code'   => $sourceCode,
    'spu_list'      => json_encode($spuListField),
);
Sales_Quotation_Spu_Cart::update($updateData);

echo json_encode(array(
    'statusCode'    => 0,
    'statusInfo'    => 'success',
));
exit;