<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$styleId    = $_GET['style_id'];
Validate::testNull($styleId,'款式ID不能为空');

$listCategort       = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status'=>0));
//获取所有的父类Id
$listParentId       = array_unique(ArrayUtility::listField($listCategort,'parent_id'));
if(in_array($styleId,$listParentId)){
    
    Utility::notice('该款式下有子款式');
}
$count      = Goods_Info::countByCondition(array('style_id' => $styleId,'delete_status'=>0));

if($count >= 1){
    
    Utility::notice('该款式下有未删除的SKU');
}
Style_Info::update(array(
    'style_id'      => $styleId,
    'delete_status' => Style_DeleteStatus::DELETED,
));

Utility::notice('删除成功');