<?php
/**
 * 批量加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuId      =  $_POST['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuId,'无效spuID');

$listSpuInfo        = Spu_Info::getByMultiId($spuId);
$listOnlineStatus   = array_unique(ArrayUtility::listField($listSpuInfo, 'online_status'));
$listDeleteStatus   = array_unique(ArrayUtility::listField($listSpuInfo, 'delete_status'));
$hasOffLineSpu      = in_array(Spu_OnlineStatus::OFFLINE, $listOnlineStatus);
$hasDeletedSpu      = in_array(Spu_DeleteStatus::DELETED, $listDeleteStatus);
if ($hasOffLineSpu || $hasDeletedSpu) {

    echo json_encode(array(
        'code'      => 1,
        'message'   => '列表中含有状态异常的SPU, 不能批量加入报价单',
    ));
    exit;
}
foreach($spuId as $id){
    $data       = array(
        'user_id'       => $userId,
        'spu_id'        => $id,
    );

    Cart_Spu_Info::create($data);
}

$countCartSpu = Cart_Spu_Info::countByUser($userId);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countCartSpu,
    ),
));