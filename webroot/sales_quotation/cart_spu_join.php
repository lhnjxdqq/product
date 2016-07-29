<?php
/**
 * 加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuId      = (int) $_POST['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuId,'无效spuID');

$taskInfo = Cart_Join_Spu_Task::getByUserIdAndRunStatus($_SESSION['user_id']);

if(!empty($taskInfo) && $taskInfo['run_status'] != Cart_Join_Spu_RunStatus::FINISH)){

    $response   = array(
            'code'      => 1,
            'message'   => '已经有搜索产品正在添加到报价单,请稍等',
        );
    echo    json_encode($response);
    exit;

}
$spuInfo    = Spu_Info::getById($spuId);
if (!$spuInfo || $spuInfo['online_status'] == Spu_OnlineStatus::OFFLINE || $spuInfo['delete_status'] == Spu_DeleteStatus::DELETED) {

    echo json_encode(array(
        'code'  => 1,
        'message'   => 'SPU状态异常, 不能加入报价单',
    ));
    exit;
}

$listGoodsId    = ArrayUtility::listField(Spu_Goods_RelationShip::getBySpuId($spuId), 'goods_id');

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');


// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

//查看所有Goods信息
$listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);

// 根据商品查询品类
$listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
$listCategory   = Category_Info::getByMultiId($listCategoryId);
$mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

// 根据商品查询规格重量
$listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

//获取规格尺寸和主料材质的属性ID
$specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');

$specColorId          = $specColorInfo['color'];

$spuCost    = array();
$mapSpuSalerCostByColor = array();

$mapColor   = array();
foreach ($listGoodsInfo as $goods) {

    $goodsId        = $goods['goods_id'];
    $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

    foreach ($goodsSpecValue as $key => $val) {

        $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

        if($val['spec_id'] == $specColorId) {
            
            $mapColor[$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
        }
    }
}

foreach($mapColor as $specValueId => $cost){
    
    rsort($cost);
    $mapColorInfo[$specValueId] = array_shift($cost);
}

$cartSpuColorCostData  = $mapColorInfo;
$jsonCartSpuColorCostData    = json_encode($cartSpuColorCostData);

$data       = array(
    'user_id'                   => $userId,
    'spu_id'                    => $spuId,
    'spu_color_cost_data'       => $jsonCartSpuColorCostData,
    'remark'                    => $spuInfo['spu_remark'],
);

Cart_Spu_Info::create($data);

$countCartSpu = Cart_Spu_Info::countByUser($userId);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countCartSpu,
    ),
));
