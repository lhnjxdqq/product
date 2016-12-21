<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

if(empty($_POST['goods_type_name'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '商品类型名称不能为空',
        'data'      => array(
        ),
    ));
    exit;
}

if(empty($_POST['goods_type_id'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '商品类型ID不能为空',
        'data'      => array(
        ),
    ));
    exit;
}
$listGoodsType  = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));

$goodsTypeInfo  = ArrayUtility::SearchBy($listGoodsType, array('goods_type_name'=>$_POST['goods_type_name']));

if(!empty($goodsTypeInfo) && $goodsTypeInfo['goods_type_id'] != $_POST['goods_type_id']){

    echo json_encode(array(
        'code'      => 1,
        'message'   => '商品类型名称重复',
        'data'      => array(
        ),
    ));
    exit;
}

Goods_Type_Info::update(array(
    'goods_type_name'   => $_POST['goods_type_name'],
    'goods_type_id'     => $_POST['goods_type_id'],
    'update_time'       => date("Y-m-d H:i:s"),
));

echo json_encode(array(
    'code'      => 0,
    'message'   => '修改完成',
    'data'      => array(
    ),
));