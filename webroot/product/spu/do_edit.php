<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$spuId      = trim($_POST['spu-id']);
$spuName    = trim($_POST['spu-name']);
$spuRemark  = trim($_POST['spu-remark']);

if (empty($spuId) || empty($spuName)) {

    Utility::notice('SPU名称不能为空');
}

$multiGoods = array();
foreach ($_POST['goods-id'] as $key => $goodsId) {

    $multiGoods[]   = array(
        'goodsId'       => $goodsId,
        'spuGoodsName'  => $_POST['spu-goods-name'][$key]
    );
}

$files          = $_FILES['spu-image'];
$imageKeyList   = $_POST['spu-image'];
foreach ($files['tmp_name'] as $stream) {

    if ($stream) {

        $imageKey       = AliyunOSS::getInstance('images-spu')->create($stream, null, true);
        $imageKeyList[] = $imageKey;
    }
}

$update = Spu_Info::update(array(
    'spu_id'        => $spuId,
    'spu_name'      => $spuName,
    'spu_remark'    => $spuRemark,
));

if ($update) {
    Spu_Goods_RelationShip::delBySpuId($spuId);
    Spu_Goods_RelationShip::createMultiSpuGoodsRelationship($multiGoods, $spuId);
    Spu_Images_RelationShip::delBySpuId($spuId);
    if ($imageKeyList) {
        foreach ($imageKeyList as $imageKey) {

            Spu_Images_RelationShip::create(array(
                'spu_id' => $spuId,
                'image_key' => $imageKey,
            ));
        }
    }

    Utility::notice('编辑SPU成功', '/product/spu/index.php');
}