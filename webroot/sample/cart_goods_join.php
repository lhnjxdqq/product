<?php
/**
 * 加入goods购物车
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$goodsId    = (int) $_POST['goods_id'];

$taskInfo = Cart_Join_Goods_Task::getByUserIdAndRunStatus($_SESSION['user_id']);

if(!empty($taskInfo) && $taskInfo['run_status'] != Cart_Join_Spu_RunStatus::FINISH){

    $response   = array(
            'code'      => 1,
            'message'   => '已经有搜索产品正在添加到样板库,请稍等',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;

}

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($goodsId,'无效goodsID');

$cartGoodsInfo              = Cart_Goods_Sample::getByUserId($userId);
$listCartGoodsId            = ArrayUtility::listField($cartGoodsInfo,'goods_id');
if(in_array($goodsId,$listCartGoodsId)){
    
    exit;
     
}
$data       = array(
    'user_id'                   => $userId,
    'goods_id'                  => $goodsId,
);

Cart_Goods_Sample::create($data);

$countCartGoods = Cart_Goods_Sample::countByUser($userId);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countCartGoods,
    ),
));