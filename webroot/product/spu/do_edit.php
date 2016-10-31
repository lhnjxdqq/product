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

$spuInfo = Spu_Info::getById($spuId);

$update = Spu_Info::update(array(
    'spu_id'        => $spuId,
    'spu_name'      => $spuName,
    'spu_remark'    => $spuRemark,
));

if ($update) {

    Spu_Images_RelationShip::delBySpuId($spuId);
    if ($imageKeyList) {

        $number = 0;
        foreach ($imageKeyList as $imageKey) {

            $number++;
            $isFirstPicture = 0;
            
            if($number == 1){
            
                $isFirstPicture = 1;    
            }
            Spu_Images_RelationShip::create(array(
                'spu_id'          => $spuId,
                'image_key'         => $imageKey,
                'image_type'        => 'R',
                'serial_number'     => $number,
                'is_first_picture'  => $isFirstPicture,
            ));
        }
    }
    // 推送SPU更新数据到选货工具
    Spu_Push::updatePushSpuData($spuId);
    Spu_Push::pushListSpuSn(array($spuInfo['spu_sn']));

    Utility::notice('编辑SPU成功', '/product/spu/index.php');
}