<?php
require_once    __DIR__ . '/../../init.inc.php';

ignore_user_abort();
/**
 * 图片顺序初始化默认
 */
//spu修改
$sql = 'select  count(1) as cnt FROM 
 (SELECT count(spu_id) as count_spu,spu_id  FROM `spu_images_relationship` GROUP BY `spu_id` having count_spu > 5) 
  as tmp_image';

$spuGroupInfo = DB::instance('product')->fetchOne($sql);

$row = 0;
$spuImageInstance 	       = AliyunOSS::getInstance('images-spu');
for($row=0; $row<=$spuGroupInfo['cnt']; $row+= 100 ){
    
    $sql                = 'SELECT count(spu_id) as count_spu,`spu_id` FROM `spu_images_relationship` GROUP BY `spu_id` having count_spu > 5 limit ' . $row . ', 100';
    $listSpuCountInfo   = DB::instance('product')->fetchAll($sql);
    $listSpuId          = ArrayUtility::listField($listSpuCountInfo,'spu_id');
    
    $sql            = 'SELECT * FROM `spu_images_relationship` WHERE `spu_id` in(' . implode(',' , $listSpuId) . ')';
    $spuInfo        = DB::instance('product')->fetchAll($sql);
    $maSpuInfo        = Spu_Info::getByMultiId($listSpuId);
    $listSpuSn      = ArrayUtility::listField($maSpuInfo,'spu_sn');

    $groupSpuInfo = ArrayUtility::groupByField($spuInfo,'spu_id');

    foreach($groupSpuInfo as $spuId =>$info){
        
        echo '正在检测SPUID为'.$spuId.'的图片是否重复'."\n";
        $mapSpuImages   = array();

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
                
                echo '修改SPUID为'.$spuId.'的图片内容'."\n";       
                Spu_Images_RelationShip::deleteByIdAndKey($spuId , $imageKey);
                $spuImageInstance->delete($imageKey);
            }
        }    
        
        echo '修改SPUID为'.$spuId.'的图片内容修改成功'."\n";
    }
    
    if(!empty($listSpuSn)){

        Spu_Push::pushListSpuSn($listSpuSn);
        Spu_Push::pushTagsListSpuSn($listSpuSn, array('imageExists'=>1));
        
    }
}

echo "SPU修改成功,时间-". date('Y-m-d H:i:s') ."\n";
unset($groupInfo);