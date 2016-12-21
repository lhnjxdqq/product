<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

if(empty($_POST['goods_type_name'])){
    
    Utility::notice("商品类型名称不能为空",'/manage/style/index.php');
    exit;
}

$listGoodsType      = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));

$goodsTypeInfo      = ArrayUtility::SearchBy($listGoodsType, array('goods_type_name'=>$_POST['goods_type_name']));

if(!empty($goodsTypeInfo)){

    Utility::notice("商品类型名称重复");
    exit;
}

Goods_Type_Info::create(array('goods_type_name'    => $_POST['goods_type_name']));
Utility::notice("添加完成");