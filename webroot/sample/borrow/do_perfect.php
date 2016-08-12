<?php

require_once  dirname(__FILE__) . '/../../../init.inc.php';

$content    = $_POST;

Validate::testNull($content['salesperson_id'],'销售员不能为空');
Validate::testNull($content['customer_id'],'顾客名称不能为空');
Validate::testNull($content['borrow_time'],'借版日期不能为空');

if(!empty($content['estimate_return_time'])){
    
    if($content['estimate_return_time'] < $content['borrow_time']){
        
        throw   new ApplicationException('归还时间不得早于借版时间');
    }
}
$userId         = $_SESSION['user_id'];
$listGoodsId    = ArrayUtility::listField(Cart_Sample_Info::getByUserId($userId),'goods_id');
Validate::testNull($listGoodsId,'借版购物车中没有样板,请返回添加','/sample/index.php');
$content['sample_quantity']    = count($listGoodsId);
$content['status']             = Borrow_Status::NEW_BORROW;

$borrowId = Borrow_Info::create($content);
foreach($listGoodsId as $goodsId){
    
    Borrow_Goods_Info::create(array(
        'borrow_id' => $borrowId,
        'goods_id'  => $goodsId,
    ));
}
Cart_Sample_Info::cleanByUserId($userId);
Utility::redirect('/sample/borrow/index.php');