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
$colorValueId   = (int) $_POST['color_value_id'];
$colorCost      = sprintf('%.2f', trim($_POST['color_cost']));

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

        if (!isset($spuCost['mapColorCost'][$colorValueId])) {

            echo json_encode(array(
                'statusCode'    => 1,
                'statusInfo'    => 'no color value id',
            ));
            exit;
        }
        
        if(!empty($cost) && is_numeric($cost)){
            
            $spuCost['mapColorCost'][$colorValueId] = $colorCost;
        }else{
            unset($spuCost['mapColorCost'][$colorValueId]);
        }
             
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