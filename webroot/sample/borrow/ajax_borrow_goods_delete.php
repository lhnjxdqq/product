<?php

require_once  dirname(__FILE__) .'/../../../init.inc.php';

if(empty($_POST['borrow_id'])){
    
    $response   = array(
            'code'      => 1,
            'message'   => '借版Id不能为空',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;
}

if(empty($_POST['goods_id'])){
    
    $response   = array(
            'code'      => 1,
            'message'   => '商品Id不能为空',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;
}

foreach($_POST['goods_id'] as $id){

    Borrow_Goods_Info::deleteByborrowIdAndGoodsId(array(
            'goods_id'  => $id,
            'borrow_id' => $_POST['borrow_id'],
    ));
}
$condition['borrow_id']     = $_POST['borrow_id'];
$orderBy                    = array();
$countGoods                 = Borrow_Goods_Info::countByCondition($condition);

Borrow_Info::update(array(
    'borrow_id'         => $_POST['borrow_id'],
    'sample_quantity'   => $countGoods,
));
$response   = array(
            'code'      => 0,
            'message'   => '批量删除成功',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;