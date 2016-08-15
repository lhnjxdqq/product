<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

header('Content-type: application/json; charset=utf8');

if (!isset($_GET['del_condition'])) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'condition error',
    ));
    exit;
}

$delCondition   = explode('~', trim($_GET['del_condition']));
$userId         = (int) $_SESSION['user_id'];
$sourceCode     = trim($delCondition[0]);
$spuId          = (int) $delCondition[1];

$cartData       = Sales_Quotation_Spu_Cart::getByPrimaryKey($userId, $sourceCode);
if (!$cartData) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'no data',
    ));
    exit;
}
$spuListField   = json_decode($cartData['spu_list'], true);


$spuList        = array();
foreach ($spuListField as $spuCost) {

    if ($spuCost['spuId'] == $spuId) {

        continue;
    }
    $spuList[]      = $spuCost;
}

// delete
if (!$spuList) {

    Sales_Quotation_Spu_Cart::delByPrimaryKey($userId, $sourceCode);
    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => 'success',
    ));
    exit;
}

// update
$updateData     = array(
    'user_id'       => $userId,
    'source_code'   => $sourceCode,
    'spu_list'      => json_encode($spuList),
);

Sales_Quotation_Spu_Cart::update($updateData);

echo json_encode(array(
    'statusCode'    => 0,
    'statusInfo'    => 'success',
));
exit;