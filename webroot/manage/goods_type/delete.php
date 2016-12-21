<?php

//品类删除
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (empty($_GET['goods_type_id'])) {

    Utility::notice('商品类型Id不能为空');
}

$categoryInfo   = ArrayUtility::searchBy(Category_Info::getByGoodsTypeId($_GET['goods_type_id']),array('delete_status'=>0));

if(!empty($categoryInfo)){
    Utility::notice('有未删除关联的产品分类,无法删除');
}

$data   = array(
    'goods_type_id'   => (int) $_GET['goods_type_id'],
    'delete_status'   => Goods_Type_DeleteStatus::DELETED,
);

Goods_Type_Info::update($data);

Utility::notice('删除商品类型成功');