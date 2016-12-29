<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

if(empty($_POST['goods_type_name'])){
    
    Utility::notice("商品类型名称不能为空");
    exit;
}

$goodsTypeInfo      = ArrayUtility::searchBy(Goods_Type_Info::listAll(), array('goods_type_name'=>$_POST['goods_type_name']));

if(!empty($goodsTypeInfo)){

    $info  = current($goodsTypeInfo);
    if($info['delete_status'] == 0){
        
        Utility::notice("商品类型名称重复");
        exit;   
    }else {
        Goods_Type_Info::update(array(
            'goods_type_id' => $info['goods_type_id'],
            'delete_status' => 0,
        ));
        Utility::notice("添加完成");
        exit;
    }
}

Goods_Type_Info::create(array('goods_type_name'=> $_POST['goods_type_name']));
Utility::notice("添加完成");