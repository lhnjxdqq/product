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
$goods          = $_POST['goods'];

$errorGoodsSnList = array();
foreach ($goods as $goodsId => $goodsInfo) {

    $data = array();
    $data['spu_id'] = $spuId;
    $data['goods_id'] = $goodsId;
    $data['spu_goods_name'] = trim($goodsInfo['goods_name']);

    if ( Spu_Goods_RelationShip::create($data) ){

        // 推送新增SPU SKU到选货工具
        Spu_Goods_Push::addPushSpuGoodsData($spuId, $goodsId);
    } else {
        $errorGoodsSnList[] = $goodsInfo['goods_sn'];
    }

}

if ( empty($errorGoodsSnList) ) {
    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => '添加SKU成功',
    ));
} else {
    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => implode(',', $errorGoodsSnList) . '添加SKU失败',
    ));
}

