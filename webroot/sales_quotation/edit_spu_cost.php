<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$userId         = (int) $_SESSION['user_id'];
$spuId          = (int) $_POST['spu_id'];
$cost           = sprintf('%.2f', trim($_POST['cost']));

$cartData       = Cart_Spu_Info::getByUserIdAndSpuId($userId, $spuId);

if (!$cartData) {

    echo json_encode(array(
        'code'    => 1,
        'statusInfo'    => 'ÎÞÊý¾Ý',
    ));
    exit;
}
foreach(json_decode($cartData['spu_color_cost_data'],true) as $colorID=>$spuCost){
    
    $editCost[$colorID]  = $cost;
}

$updateData     = array(
    'user_id'                   => $userId,
    'spu_id'                    => $spuId,
    'spu_color_cost_data'       => json_encode($editCost),
);
Cart_Spu_Info::update($updateData);

echo json_encode(array(
    'code'          => 0,
    'statusInfo'    => 'success',
));
exit;