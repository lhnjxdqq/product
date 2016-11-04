<?php
require_once    __DIR__ . '/../../init.inc.php';

ignore_user_abort();
/**
 * 图片顺序初始化默认
 */
//spu修改
$sql = 'SELECT * FROM `spu_images_relationship` group BY `spu_id`';

$spuGroupInfo = DB::instance('product')->fetchAll($sql);
$row = 0;
for($row=0; $row<=count($spuGroupInfo); $row+= 100 ){
    
    $sql            = 'SELECT `spu_id` FROM `spu_images_relationship` GROUP BY `spu_id` limit ' . $row . ',100';
    $listspuId      = DB::instance('product')->fetchAll($sql);
    $sql            = 'SELECT * FROM `spu_images_relationship` WHERE `spu_id` in(' . implode(',',ArrayUtility::listField($listspuId,'spu_id')) . ')';
    $spuInfo        = DB::instance('product')->fetchAll($sql);

    $groupSpuInfo = ArrayUtility::groupByField($spuInfo,'spu_id');

    foreach($groupSpuInfo as $spuId =>$info){
        
        echo '正在修改SPUID为'.$spuId.'的图片顺序'."\n";
        if(count($info)<=1){
            
            continue;
        }
        $updateSql      = 'UPDATE `spu_images_relationship` set `is_first_picture` = "0" WHERE `spu_id`='.$spuId;

        DB::instance('product')->execute($updateSql);
        
        $sortImage      = Sort_Image::sortImage($info);
        
        $groupInfo      = ArrayUtility::groupByField($sortImage,'image_type');
        
        foreach($groupInfo as $type => $spuImageInfo){
            
            $sortNumber = 1;
            if(count($spuImageInfo)<=1){

                continue;
            }
            
            foreach($spuImageInfo as $key =>$imageInfo){

                Spu_Images_RelationShip::update(array(
                    'spu_id'            => $spuId,
                    'image_key'         => $imageInfo['image_key'],
                    'serial_number'     => $sortNumber,
                ));
                $sortNumber++;
            }
        }

        Spu_Images_RelationShip::update(array(
            'spu_id'            => $spuId,
            'image_key'         => $sortImage[0]['image_key'],
            'is_first_picture'  => 1,
        ));
    }
}

echo "SPU修改成功,时间-". date('Y-m-d H:i:s') ."\n";
unset($groupInfo);

//Goods修改

$sql = 'SELECT * FROM `goods_images_relationship` group BY `goods_id`';

$goodsGroupInfo = DB::instance('product')->fetchAll($sql);
$row = 0;
for($row=0; $row<=count($goodsGroupInfo); $row+= 100 ){
    
    $sql            = 'SELECT `goods_id` FROM `goods_images_relationship` GROUP BY `goods_id` limit ' . $row . ',100';
    $listGoodsId    = DB::instance('product')->fetchAll($sql);
    $sql            = 'SELECT * FROM `goods_images_relationship` WHERE `goods_id` in(' . implode(',',ArrayUtility::listField($listGoodsId,'goods_id')) . ')';
    $goodsInfo      = DB::instance('product')->fetchAll($sql);

    $groupGoodsInfo = ArrayUtility::groupByField($goodsInfo,'goods_id');

    foreach($groupGoodsInfo as $goodsId =>$info){
        
        echo '正在修改GoodsID为'.$goodsId.'的图片顺序'."\n";
        if(count($info)<=1){
            
            continue;
        }
        $updateSql      = 'UPDATE `goods_images_relationship` set `is_first_picture` = "0" WHERE `goods_id`='.$goodsId;

        DB::instance('product')->execute($updateSql);
        
        $sortImage      = Sort_Image::sortImage($info);
        
        $groupInfo      = ArrayUtility::groupByField($sortImage,'image_type');
        
        foreach($groupInfo as $type => $goodsImageInfo){
            
            $sortNumber = 1;
            if(count($goodsImageInfo)<=1){

                continue;
            }
            
            foreach($goodsImageInfo as $key =>$imageInfo){

                Goods_Images_RelationShip::update(array(
                    'goods_id'          => $goodsId,
                    'image_key'         => $imageInfo['image_key'],
                    'serial_number'     => $sortNumber,
                ));
                $sortNumber++;
            }
        }

        Goods_Images_RelationShip::update(array(
            'goods_id'          => $goodsId,
            'image_key'         => $sortImage[0]['image_key'],
            'is_first_picture'  => 1,
        ));
    }
}
echo "SKU修改成功,时间-". date('Y-m-d H:i:s') ."\n";
unset($groupInfo);
 
//Product修改

$sql = 'SELECT * FROM `product_images_relationship` group BY `product_id`';

$productGroupInfo = DB::instance('product')->fetchAll($sql);
$row = 0;
for($row=0; $row<=count($productGroupInfo); $row+= 100 ){
    
    $sql            = 'SELECT `product_id` FROM `product_images_relationship` GROUP BY `product_id` limit ' . $row . ',100';
    $listProductId  = DB::instance('product')->fetchAll($sql);
    $sql            = 'SELECT * FROM `product_images_relationship` WHERE `product_id` in(' . implode(',',ArrayUtility::listField($listProductId,'product_id')) . ')';
    $productInfo    = DB::instance('product')->fetchAll($sql);
    $groupProductInfo   = ArrayUtility::groupByField($productInfo,'product_id');

    foreach($groupProductInfo as $productId =>$info){
        
        echo '正在修改产品ID为'.$productId.'的图片顺序'."\n";

        if(count($info)<=1){
            
            continue;
        }
        $updateSql      = 'UPDATE `product_images_relationship` set `is_first_picture` = "0" WHERE `product_id`='.$productId;

        DB::instance('product')->execute($updateSql);
        
        $sortImage      = Sort_Image::sortImage($info);
        
        $groupInfo      = ArrayUtility::groupByField($sortImage,'image_type');
        
        foreach($groupInfo as $type => $productImageInfo){
            
            $sortNumber = 1;
            if(count($productImageInfo)<=1){

                continue;
            }
            
            foreach($productImageInfo as $key =>$imageInfo){

                Product_Images_RelationShip::update(array(
                    'product_id'        => $productId,
                    'image_key'         => $imageInfo['image_key'],
                    'serial_number'     => $sortNumber,
                ));
                $sortNumber++;
            }
        }

        Product_Images_RelationShip::update(array(
            'product_id'        => $productId,
            'image_key'         => $sortImage[0]['image_key'],
            'is_first_picture'  => 1,
        ));
    }
}
echo "产品修改成功,时间-". date('Y-m-d H:i:s') ."\n";
unset($groupInfo);