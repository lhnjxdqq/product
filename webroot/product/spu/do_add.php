<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$spuSn      = trim($_POST['spu-sn']);
$spuName    = trim($_POST['spu-name']);
$spuRemark  = trim($_POST['spu-remark']);

if (empty($spuSn) || empty($spuName)) {

    Utility::notice('SPU编号和SPU名称不能为空');
}

$multiGoods = array();
foreach ($_POST['goods-id'] as $key => $goodsId) {

    $multiGoods[]   = array(
        'goodsId'       => $goodsId,
        'spuGoodsName'  => $_POST['spu-goods-name'][$key]
    );
}

$spuId  = Spu_Info::create(array(
    'spu_sn'        => $spuSn,
    'spu_name'      => $spuName,
    'spu_remark'    => $spuRemark,
));

$files          = $_FILES['spu-image'];
$imageKeyList   = array();
// 复制sku图片到spu
if ($_POST['sku-image']) {

    foreach ($_POST['sku-image'] as $skuImageKey) {

        $skuImageInstance   = AliyunOSS::getInstance('images-sku');
        $spuImageKey        = AliyunOSS::getInstance('images-spu')->copyCreate($skuImageInstance, $skuImageKey, null, true);
        $imageKeyList[]     = $spuImageKey;
    }
}
foreach ($files['tmp_name'] as $stream) {

    if ($stream) {

        $imageKey       = AliyunOSS::getInstance('images-spu')->create($stream, null, true);
        $imageKeyList[] = $imageKey;
    }
}

if ($spuId) {

    Spu_Goods_RelationShip::createMultiSpuGoodsRelationship($multiGoods, $spuId);
    foreach ($imageKeyList as $imageKey) {

        Spu_Images_RelationShip::create(array(
            'spu_id'    => $spuId,
            'image_key' => $imageKey,
        ));
    }
    // 推送新增SPU数据到选货工具
    Spu_Push::addPushSpuData($spuId);

    Utility::notice('添加SPU成功', '/product/spu/index.php');
}