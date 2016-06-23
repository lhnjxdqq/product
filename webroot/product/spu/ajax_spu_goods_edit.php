<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'method error',
    ));
    exit;
}

$spuId          = (int) $_POST['spu_id'];
$goodsId        = (int) $_POST['goods_id'];
$spuGoodsName   = trim($_POST['spu_goods_name']);

if (!$goodsId || !$spuId) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'data error',
    ));
    exit;
}

$data   = array(
    'spu_id'            => $spuId,
    'goods_id'          => $goodsId,
    'spu_goods_name'    => $spuGoodsName,
);

if (Spu_Goods_RelationShip::update($data)) {

    // 推送编辑SPU SKU到选货工具
    Spu_Goods_Push::updatePushSpuGoodsData($spuId, $goodsId);
    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => '更新成功',
    ));
} else {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => '更新失败',
    ));
}