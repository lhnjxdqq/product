<?php
require_once    __DIR__ . '/../../init.inc.php';

ignore_user_abort();
/**
 * 图片顺序初始化默认
 */
//spu修改
$sql = 'SELECT count(*) as cnt, spu_id FROM `spu_images_relationship` GROUP BY `spu_id`';

$spuGroupInfo = DB::instance('product')->fetchAll($sql);

$row = 0;
$spuImageInstance 	       = AliyunOSS::getInstance('images-spu');
for($row=0; $row<=count($spuGroupInfo); $row+= 100 ){
    
    $sql            = 'SELECT `spu_id` FROM `spu_images_relationship` GROUP BY `spu_id` limit ' . $row . ',100';
    $listspuId      = DB::instance('product')->fetchAll($sql);
    $sql            = 'SELECT * FROM `spu_images_relationship` WHERE `spu_id` in(' . implode(',',ArrayUtility::listField($listspuId,'spu_id')) . ')';
    $spuInfo        = DB::instance('product')->fetchAll($sql);

    $groupSpuInfo = ArrayUtility::groupByField($spuInfo,'spu_id');

    foreach($groupSpuInfo as $spuId =>$info){
        
        echo '正在检测SPUID为'.$spuId.'的图片是否重复'."\n";
        $mapSpuImages   = array();
        if(count($info)<6){
            
            continue;
        }
        $info      = Sort_Image::sortImage($info);
        foreach($info as $spuImageInfo){

            if (!$spuImageInstance->isExist($spuImageInfo['image_key'])) { // 如果数据库数据存在 , 但远程数据不存在,删除
                Spu_Images_RelationShip::deleteByIdAndKey($spuId , $spuImageInfo['image_key']);
            }
            
            $mapSpuImages[$spuImageInfo['image_key']]  = md5_file(AliyunOSS::getInstance('images-spu')->url($spuImageInfo['image_key']));
        }
        $uniqueArr  = array_unique($mapSpuImages);
        $repearArr  = array_diff_assoc($mapSpuImages,$uniqueArr);
        
        if(!empty($repearArr)){
            
            foreach($repearArr as $imageKey => $imageMd5){
                
                Spu_Images_RelationShip::deleteByIdAndKey($spuId , $imageKey);
                $spuImageInstance->delete($imageKey);
            }
        }
        echo '修改SPUID为'.$spuId.'的图片内容'."\n";
    }
}

echo "SPU修改成功,时间-". date('Y-m-d H:i:s') ."\n";
unset($groupInfo);