<?php
/**
 * 批量加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuIds     =  $_POST['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuIds,'无效spuID');

$taskInfo = Cart_Join_Spu_Task::getByUserIdAndRunStatus($_SESSION['user_id']);

if(!empty($taskInfo) && $taskInfo['run_status'] != Cart_Join_Spu_RunStatus::FINISH){

    $response   = array(
            'code'      => 1,
            'message'   => '已经有搜索产品正在添加到报价单,请稍等',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;

}
$listSpuInfo        = Spu_Info::getByMultiId($spuIds);
$listOnlineStatus   = array_unique(ArrayUtility::listField($listSpuInfo, 'online_status'));
$listDeleteStatus   = array_unique(ArrayUtility::listField($listSpuInfo, 'delete_status'));
$hasOffLineSpu      = in_array(Spu_OnlineStatus::OFFLINE, $listOnlineStatus);
$hasDeletedSpu      = in_array(Spu_DeleteStatus::DELETED, $listDeleteStatus);
if ($hasOffLineSpu || $hasDeletedSpu) {

    echo json_encode(array(
        'code'      => 1,
        'message'   => '列表中含有状态异常的SPU, 不能批量加入报价单',
    ));
    exit;
}

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

// 查询SPU下的商品
$listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($spuIds);
$groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

// SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
$mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
$listGoodsId    = array_values($mapSpuGoods);
$listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

// 根据商品查询品类
$listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
$listCategory   = Category_Info::getByMultiId($listCategoryId);
$mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

// 根据商品查询规格重量
$listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

//获取颜色的属性ID
$specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
$specColorId          = $specColorInfo['color'];

$spuCost    = array();
$mapSpuSalerCostByColor = array();

foreach ($groupSpuGoods as $spuId => $spuGoods) {

    $mapColor   = array();
    foreach ($spuGoods as $goods) {

        $goodsId        = $goods['goods_id'];
        $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

        foreach ($goodsSpecValue as $key => $val) {

            $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

            if($val['spec_id'] == $specColorId) {
                
                $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            }
        }
    }
    foreach($mapColor as $spuIdKey => $colorInfo){

        foreach($colorInfo as $colorId => $cost){
            
            rsort($cost);
            $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
        }
    }
}
$indexSpuIdRemark   = ArrayUtility::indexByField($listSpuInfo,'spu_id','spu_remark');

foreach($spuIds as $id){
    $cartSpuInfo        = array();
    $cartSpuInfo        = $mapColorInfo[$id];
    $cartSpuInfo        = json_encode($cartSpuInfo);

    $data       = array(
        'user_id'               => $userId,
        'spu_id'                => $id,
        'spu_color_cost_data'   => $cartSpuInfo,
        'remark'                => $indexSpuIdRemark[$id],
    );

    Cart_Spu_Info::create($data);
}

$countCartSpu = Cart_Spu_Info::countByUser($userId);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countCartSpu,
    ),
));