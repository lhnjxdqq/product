<?php

require dirname(__FILE__).'/../../../init.inc.php';

$listCategort       = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status'=>0));
//获取所有的父类Id
$listParentId       = array_unique(ArrayUtility::listField($listCategort,'parent_id'));
//按照父类ID分组
$groupParentId      = ArrayUtility::groupByField($listCategort,'parent_id');
//获取商品类型
$goodsTypeInfo      = ArrayUtility::indexByField(Goods_Type_Info::listAll(),'goods_type_id');

foreach($groupParentId as $key => $info){
    
    foreach($info as $keys => $val){
        
        if($val['category_level'] == 0){
            
            if(in_array($val['category_id'],$listParentId)){
            
                $val['is_parent']    = 1;
            }
            $oneLevelCategoryInfo[] = $val;
        }
        if($val['category_level'] == 1){
            
            if(in_array($val['category_id'],$listParentId)){
            
                $val['is_parent']   = 1;
            }
            $twoLevelCategoryInfo[$val['parent_id']][] = $val;
        }
        if($val['category_level'] == 2){
            
            if(in_array($val['category_id'],$listParentId)){
            
                $val['is_parent']   = 1;
            }
            $threeLevelCategoryInfo[$val['parent_id']][] = $val;
        }
        
    }
}

$template = Template::getInstance();

$template->assign('listCategort', $listCategort);
$template->assign('goodsTypeInfo', $goodsTypeInfo);
$template->assign('jsonGoodsTypeInfo', json_encode($goodsTypeInfo));
$template->assign('oneLevelCategoryInfo', $oneLevelCategoryInfo);
$template->assign('twoLevelCategoryInfo', $twoLevelCategoryInfo);
$template->assign('threeLevelCategoryInfo', $threeLevelCategoryInfo);
$template->assign('mainMenu' , Menu_Info::getMainMenu());
$template->display('manage/category/index.tpl');