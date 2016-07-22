<?php
/**
 * 加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuId      = (int) $_POST['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuId,'无效spuID');

$spuInfo    = Spu_Info::getById($spuId);
if (!$spuInfo || $spuInfo['online_status'] == Spu_OnlineStatus::OFFLINE || $spuInfo['delete_status'] == Spu_DeleteStatus::DELETED) {

    echo json_encode(array(
        'code'  => 1,
        'message'   => 'SPU状态异常, 不能加入报价单',
    ));
    exit;
}

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
