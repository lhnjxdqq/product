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

if ($spuId) {

    Spu_Goods_RelationShip::createMultiSpuGoodsRelationship($multiGoods, $spuId);

    Utility::notice('添加SPU成功', '/product/spu/index.php');
}