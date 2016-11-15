<?php
require_once    __DIR__ . '/../../init.inc.php';

ignore_user_abort();
/**
 * SPU表中初始化图片张数
 */
//spu修改
$sql = 'select count(*) as cnt from spu_info';

$countSpu = DB::instance('product')->fetchOne($sql);

$row = 0;

for($row=0; $row<=$countSpu['cnt']; $row+= 100 ){
    
    $sql                = 'SELECT `spu_id` FROM `spu_info` limit ' . $row . ', 100';
    $listSpuCountInfo   = DB::instance('product')->fetchAll($sql);
    $listSpuId          = ArrayUtility::listField($listSpuCountInfo,'spu_id');
    
    $sql            = 'SELECT * FROM `spu_images_relationship` WHERE `spu_id` in(' . implode(',' , $listSpuId) . ')';
    $spuImageInfo   = DB::instance('product')->fetchAll($sql);
    $groupSpuIdInfo = ArrayUtility::groupByField($spuImageInfo,'spu_id');
    
    if(empty($groupSpuIdInfo)){
        
        continue;
    }
    foreach($groupSpuIdInfo as $spuId =>$info){
        
        echo '正在修改SPUID为' . $spuId .'的图片信息'."\n";
        Spu_Info::update(array(
            'spu_id'        => $spuId,
            'image_total'   => count($info),
        ));
    }
}

echo "SPU修改成功,时间-". date('Y-m-d H:i:s') ."\n";