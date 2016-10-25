<?php
require_once    __DIR__ . '/../../init.inc.php';

/**
 * 图片头图初始化默认
 */
$sql = 'SELECT `goods_id`,MAX(`create_time`) as time,`image_key` FROM `goods_images_relationship` GROUP BY `goods_id`';

$goodsInfo  = DB::instance('product')->fetchAll($sql);

foreach($goodsInfo as $key => $info){
    
    echo '修改goodsId 为' . $info['goods_id'] . '的默认头图' . "\n";
    $select   = 'SELECT * FROM `goods_images_relationship` WHERE `goods_id`='.$info['goods_id'] .' AND `create_time`='. '"' .$info['time'] . '"'; 

    $goods    = DB::instance('product')->fetchOne($select);
    Goods_Images_RelationShip::update(array(
        'goods_id'          => $goods['goods_id'],
        'image_key'         => $goods['image_key'],
        'is_first_picture'  => 1,
    ));
}

$sql = 'SELECT `product_id`,MAX(`create_time`) as time,`image_key` FROM `product_images_relationship` GROUP BY `product_id`';

$productInfo  = DB::instance('product')->fetchAll($sql);

foreach($productInfo as $key => $info){
    
    echo '修改productId 为' . $info['product_id'] . '的默认头图' . "\n";
    $select   = 'SELECT * FROM `product_images_relationship` WHERE `product_id`='.$info['product_id'] .' AND `create_time`='. '"' .$info['time'] . '"'; 

    $product    = DB::instance('product')->fetchOne($select);
    Product_Images_RelationShip::update(array(
        'product_id'        => $product['product_id'],
        'image_key'         => $product['image_key'],
        'is_first_picture'  => 1,
    ));
}

$sql = 'SELECT `spu_id`,MAX(`create_time`) as time,`image_key` FROM `spu_images_relationship` GROUP BY `spu_id`';

$spuInfo  = DB::instance('product')->fetchAll($sql);

foreach($spuInfo as $key => $info){
    
    echo '修改spuId 为' . $info['spu_id'] . '的默认头图' . "\n";
    $select   = 'SELECT * FROM `spu_images_relationship` WHERE `spu_id`='.$info['spu_id'] .' AND `create_time`='. '"' .$info['time'] . '"'; 

    $spu      = DB::instance('product')->fetchOne($select);
    Spu_Images_RelationShip::update(array(
        'spu_id'            => $spu['spu_id'],
        'image_key'         => $spu['image_key'],
        'is_first_picture'  => 1,
    ));
}
echo '修改完成,请删除初始化文件';