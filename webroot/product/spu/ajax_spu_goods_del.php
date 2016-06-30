<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'method error',
    ));
    exit;
}

$spuId      = (int) $_POST['spu_id'];
$goodsId    = (int) $_POST['goods_id'];

if (!$goodsId || !$spuId) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'data error',
    ));
    exit;
}

if (Spu_Goods_RelationShip::delSpuGoods($spuId, $goodsId)) {

    // 推送删除SPU SKU到选货工具
    Spu_Goods_Push::changePushSpuGoodsDataStatus($spuId, $goodsId, 'delete');
    $statusInfo = '删除成功';
    $redirect   = '';
    // 判断该SPU下是否有SKU, 如果没有删除该SPU
    $listSpuGoods   = Spu_Goods_RelationShip::getBySpuId($spuId);
    if (!$listSpuGoods) {

        Spu_Info::update(array(
            'spu_id'        => $spuId,
            'delete_status' => Spu_DeleteStatus::DELETED,
        ));
        // 推送删除SPU数据到选货工具
        Spu_Push::deletePushSpuData($spuId);
        $statusInfo = '最后一个SKU已经删除, SPU删除成功';
        $redirect   = '/product/spu/index.php';
    }
    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => $statusInfo,
        'redirect'      => $redirect,
    ));
} else {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => '删除失败',
    ));
}