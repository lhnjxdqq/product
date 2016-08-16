<?php

require dirname(__FILE__).'/../../../init.inc.php';

$userId             = $_SESSION['user_id'];
$listGoodsId        = ArrayUtility::listField(Cart_Sample_Info::getByUserId($userId),'goods_id');
Validate::testNull($_GET['borrow_id'],'借版Id不存在');
Validate::testNull($listGoodsId,'借版购物车中没有样板,请返回添加','/sample/borrow/edit.php?borrow_id='.$_GET['borrow_id']);
$borrowGoodsInfo    = Borrow_Goods_Info::getByBorrowId($_GET['borrow_id']);
$listBorrowGoodsId  = ArrayUtility::listField($borrowGoodsInfo,'goods_id');;

$addNum = 0;
$repeat = 0;
foreach($listGoodsId as $goodsId){
    
    if(!empty($listBorrowGoodsId)){
        
        if(in_array($goodsId,$listBorrowGoodsId)){

            $repeat++;
        }else{
                
            Borrow_Goods_Info::create(array(
                'borrow_id' => $_GET['borrow_id'],
                'goods_id'  => $goodsId,
            ));
            $addNum++;
        }
    }else{
         
        Borrow_Goods_Info::create(array(
            'borrow_id' => $_GET['borrow_id'],
            'goods_id'  => $goodsId,
        ));   
        $addNum++;
    }
}
Cart_Sample_Info::cleanByUserId($userId);

$condition['borrow_id']     = $_GET['borrow_id'];
$countGoods                 = Borrow_Goods_Info::countByCondition($condition);

Borrow_Info::update(array(
    'borrow_id'         => $_GET['borrow_id'],
    'sample_quantity'   => $countGoods,
));
$strArray[] = '合并成功';
if($addNum>0){
    $strArray[] ='成功添加样板数:'.$addNum;
}
if($repeat>0){
    $strArray[] ='重复样板数:'.$repeat;
}

Utility::notice(implode(',',$strArray));