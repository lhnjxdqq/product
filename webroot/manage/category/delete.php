<?php

//品类删除
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['category_id'])) {

    Utility::notice('品类Id不存在');
}
$listCategort       = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status'=>0));
//获取所有的父类Id
$listParentId       = array_unique(ArrayUtility::listField($listCategort,'parent_id'));
if(in_array($_GET['category_id'],$listParentId)){
    
    Utility::notice('有子分类无法删除');
}
$count      = Goods_Info::countByCondition(array('category_id'=>$_GET['category_id']));
if($count > 0 ){
    
    Utility::notice('有未删除的SKU');
}

$data   = array(
    'category_id'   => (int) $_GET['category_id'],
    'delete_status' => 1,
);

Category_Info::update($data);

Utility::notice('删除品类成功');