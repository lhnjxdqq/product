<?php
ignore_user_abort();
require_once dirname(__FILE__) . '/../../init.inc.php';

echo '***************'.date('Y-m-d H:i:s').'*****************'."\n";

$goodsImageInstance 					 = AliyunOSS::getInstance('images-sku');
$productImageInstance 					 = AliyunOSS::getInstance('images-product');
$spuImageInstance 					     = AliyunOSS::getInstance('images-spu');
$goodsSelect   = 'SELECT * FROM `goods_images_relationship`';
$goodsInfo     = DB::instance('product')->fetchAll($goodsSelect);

foreach($goodsInfo as $key => $info){

    $imageKey           = $info['image_key'];
    if (!$goodsImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除

        Goods_Images_RelationShip::deleteByIdAndKey($info['goods_id'] , $imageKey);
        continue;
    }

    try {
        $productImageInstance->copyCreate($goodsImageInstance, $imageKey, $imageKey, true);
    } catch (Exception $e){
        echo $e->getMessage()."\n";
    }
    echo '正在复制skuId为' . $info['goods_id'] .',位置为'. $info['image_type'] . $info['serial_number']. '的图片'."\n";

}

$spuSelect   = 'SELECT * FROM `spu_images_relationship`';
$spuInfo     = DB::instance('product')->fetchAll($spuSelect);

foreach($spuInfo as $key => $info){

    $imageKey           = $info['image_key'];
    if (!$spuImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除

        Spu_Images_RelationShip::deleteByIdAndKey($info['spu_id'] , $imageKey);
        continue;
    }
    
    try {
        $productImageInstance->copyCreate($spuImageInstance, $imageKey, $imageKey, true);
    } catch (Exception $e){
        echo $e->getMessage();
    }

    echo '正在复制spuId为' . $info['spu_id'] .' ,位置为' . $info['image_type'] . $info['serial_number']. '的图片'."\n";
}
echo '完成';