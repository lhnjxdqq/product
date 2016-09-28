<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$userId         = (int) $_SESSION['user_id'];
$sourceCode     = trim($_POST['source_code']);
$spuId          = (int) $_POST['spu_id'];
$cost           = sprintf('%.2f', trim($_POST['cost']));

$cartData       = Sales_Quotation_Spu_Cart::getByPrimaryKey($userId, $sourceCode);
if (!$cartData) {

    echo json_encode(array(
        'code'    => 1,
        'statusInfo'    => '无数据',
    ));
    exit;
}

$spuListField   = json_decode($cartData['spu_list'], true);
foreach ($spuListField as &$spuCost) {

    if ($spuId == $spuCost['spuId']) {

        if (!isset($spuCost['mapColorCost'][$colorValueId])) {

            foreach($spuCost['mapColorCost'] as $colorId=>$colorCost){
                
                $editColor[$colorId]    = $cost;
            }
        }
        $spuCost['mapColorCost'] = $editColor;
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
    'code'          => 0,
    'statusInfo'    => 'success',
));
exit;