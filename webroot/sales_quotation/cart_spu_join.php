<?php
/**
 * 加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuId      = (int) $_POST['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuId,'无效spuID');

$data       = array(
    'user_id'       => $userId,
    'spu_id'        => $spuId,
);

Cart_Spu_Info::create($data);

$countCartSpu = Cart_Spu_Info::countByUser($userId);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countCartSpu,
    ),
));
