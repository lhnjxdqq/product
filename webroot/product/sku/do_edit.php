<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('method error');
}

$goodsName      = trim($_POST['goods-name']);
$styleId        = (int) $_POST['style-id-lv2'];
$specList       = $_POST['spec-list'];
$selfCost       = sprintf('%.2f', $_POST['self-cost']);
$saleCost       = sprintf('%.2f', $_POST['sale-cost']);
$goodsRemark    = trim($_POST['goods-remark']);
$goodsId        = (int) $_POST['goods-id'];
$imageIdList    = $_POST['sku-image'];

$files  = $_FILES['sku-image'];
foreach ($files['tmp_name'] as $stream) {
    if ($stream) {
        $imageId        = AliyunOSS::getInstance('images-sku')->create($stream, null, true);
        $imageIdList[]  = $imageId;
    }
}

$specValueList  = array();
foreach ($specList as $specValue) {

    $temp               = explode('~', $specValue);
    $specValueList[]    = array(
        'spec_id'       => $temp[0],
        'spec_value_id' => $temp[1],
    );
}

$goodsInfo      = Goods_Info::getById($goodsId);
$categoryId     = $goodsInfo['category_id'];
$isGoodsExists  = Goods_Spec_Value_RelationShip::validateGoods($specValueList, $styleId, $categoryId);

if ($isGoodsExists && ($isGoodsExists != $goodsId)) {

    $existedGoodsInfo   = Goods_Info::getById($isGoodsExists);
    Utility::notice('SKU已经存在, 编号为: ' . $existedGoodsInfo['goods_sn']);
}

$data   = array(
    'goods_id'      => $goodsId,
    'goods_name'    => $goodsName,
    'style_id'      => $styleId,
    'self_cost'     => $selfCost,
    'sale_cost'     => $saleCost,
    'goods_remark'  => $goodsRemark,
);

if (Goods_Info::update($data)) {

    // 更新goods_spec_value_relationship
    Goods_Spec_Value_RelationShip::deleteByGoodsId($goodsId);
    foreach ($specValueList as $specValue) {
        $specValue['goods_id']  = $goodsId;
        Goods_Spec_Value_RelationShip::create($specValue);
    }
    // 更新图片关系
    $number = 0;
    Goods_Images_RelationShip::deleteByGoodsId($goodsId);
    if ($imageIdList) {
        foreach ($imageIdList as $imageId) {

            $number++;
            $isFirstPicture = 0;
            
            if($number == 1){
            
                $isFirstPicture = 1;    
            }
            Goods_Images_RelationShip::create(array(
                'goods_id'          => $goodsId,
                'image_key'         => $imageId,
                'image_type'        => 'R',
                'serial_number'     => $number,
                'is_first_picture'  => $isFirstPicture,
            ));
        }
    }

    // 推送更新SKU数据到生产工具
    Goods_Push::updatePushGoodsData($goodsId);
    Utility::notice('编辑成功', '/product/sku/index.php');
} else {

    Utility::notice('编辑失败');
}