<?php
require_once    __DIR__ . '/../../init.inc.php';

ignore_user_abort();
/**
 * 检查并修改不符合要求的SPU下SKU的规格名称
 */
//spu修改
$sql = 'select count(*) as cnt from `goods_info` WHERE `delete_status`=' . Goods_DeleteStatus::NORMAL;

$countGoods = DB::instance('product')->fetchOne($sql);

$row = 0;

$sizeSpecInfo               = Spec_Info::getByAlias('size');
$listSizeSpecValue          = Goods_Type_Spec_Value_Relationship::getBySpecId($sizeSpecInfo['spec_id']);
$listSizeSpecValueId        = array_unique(ArrayUtility::listField($listSizeSpecValue, 'spec_value_id'));
$listSizeSpecValueInfo      = Spec_Value_Info::getByMulitId($listSizeSpecValueId);
$mapSizeSpecValueInfo       = ArrayUtility::indexByField($listSizeSpecValueInfo, 'spec_value_id');

$colorSpecInfo              = Spec_Info::getByAlias('color');
$listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
$listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

for($row=0; $row<=$countGoods['cnt']; $row+= 100 ){
    
    $sql                = 'SELECT * FROM `goods_info` WHERE `delete_status`='. Goods_DeleteStatus::NORMAL .' limit ' . $row . ', 100';

    $listGoodsInfo = DB::instance('product')->fetchAll($sql);

    $listGoodsId        = ArrayUtility::listField($listGoodsInfo,'goods_id');
    $goodsSpesInfo      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
    
    $listGoodsIdSizeSpec    = ArrayUtility::indexByField(ArrayUtility::searchBy($goodsSpesInfo,array('spec_id'=>$sizeSpecInfo['spec_id'])),'goods_id');
    $listGoodsIdColorSpec   = ArrayUtility::indexByField(ArrayUtility::searchBy($goodsSpesInfo,array('spec_id'=>$colorSpecInfo['spec_id'])),'goods_id');
    
    foreach($listGoodsInfo as $key => $info){
        
        $goodsId    = $info['goods_id'];
        echo '正在检测修改goodsId为'.$goodsId.'名称'."\n";
        $goodsSizeValueData  = $mapSizeSpecValueInfo[$listGoodsIdSizeSpec[$goodsId]['spec_value_id']]['spec_value_data'];
        $goodsColorValueData  = $mapColorSpecValueInfo[$listGoodsIdColorSpec[$goodsId]['spec_value_id']]['spec_value_data'];
        $goodsName  = $goodsSizeValueData.$goodsColorValueData;
        if($goodsName != $info['goods_name']){
            
            Goods_Info::update(array(
                'goods_id'      => $goodsId,
                'goods_name'    => $goodsName,
            ));
                    
            // 推送更新SKU数据到生产工具
            Goods_Push::updatePushGoodsData($goodsId);
        }
    }
}

echo "SKU信息修改成功,时间-". date('Y-m-d H:i:s') ."\n";