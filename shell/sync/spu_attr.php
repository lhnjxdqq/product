<?php
/**
 * SPU属性数据初始化
 *
 * 执行示例
 * php shell/sync/spu_info.php 
 */
ignore_user_abort(true);
require_once dirname(__FILE__) . '/../../init.inc.php';

$size               = 50;
$totalSpu           = Spu_Info::countByCondition(array());
$listApiUrl         = Config::get('api|PHP', 'api_list');
$apiUrl             = $listApiUrl['select']['tag_product_attr'];

for ($offset = 0; $offset <= $totalSpu; $offset += $size) {
    
    $listSpuInfo    = Spu_Info::listByCondition(array(), array(
        'spu_id'    => 'ASC',
    ), $offset, $size);
    
    if(empty($listSpuInfo)){
        
        continue;
    }
    $listSpuSn      = ArrayUtility::listField($listSpuInfo, 'spu_sn');
    $mapSpuSn       = ArrayUtility::indexByField($listSpuInfo,'spu_sn','spu_id');

    $res = TagApi::getInstance()->Spu_getByMultiProductSn($listSpuSn)->call();
    
    if(empty($res['data'])){
        
        continue;
    }
    $data  = current($res['data']);

    foreach($data as $attrInfo){
        
        Spu_Attribute::createSpuAttr($attrInfo,$mapSpuSn);
    }
    sleep(1);
}
echo "执行完成\n";