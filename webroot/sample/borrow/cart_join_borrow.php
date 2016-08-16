<?php

require dirname(__FILE__).'/../../../init.inc.php';

$userId         = $_SESSION['user_id'];
$listGoodsId    = ArrayUtility::listField(Cart_Sample_Info::getByUserId($userId),'goods_id');
Validate::testNull($_GET['borrow_id'],'借版Id不存在');
Validate::testNull($listGoodsId,'借版购物车中没有样板,请返回添加','/sample/index.php');

foreach($listGoodsId as $goodsId){
    
    Borrow_Goods_Info::create(array(
        'borrow_id' => $_GET['borrow_id'],
        'goods_id'  => $goodsId,
    ));
}
Cart_Sample_Info::cleanByUserId($userId);

$condition['borrow_id']     = $_GET['borrow_id'];
$countGoods                 = Borrow_Goods_Info::countByCondition($condition);

Borrow_Info::update(array(
    'borrow_id'         => $_GET['borrow_id'],
    'sample_quantity'   => $countGoods,
));
Utility::notice('合并成功');