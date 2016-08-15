<?php
/**
 * 从sku购物车中批量删除
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$userId           = (int) $_SESSION['user_id'];
$goodsId          =  $_POST['goods_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($goodsId,'无效skuId');

foreach($goodsId as $id){
    $data       = array(
        'user_id'       => $userId,
        'goods_id'      => $id,
    );

    Cart_Sample_Info::delete($data);
}

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
    
    ),
));
