<?php

require dirname(__FILE__).'/../../../init.inc.php';

$listStyle      = ArrayUtility::searchBy(Style_Info::listAll(),array('delete_status'=>0));
$indexStyleId   = ArrayUtility::indexByField($listStyle,'style_id');
$listParentId       = array_unique(ArrayUtility::listField($listStyle,'parent_id'));
//按照父类ID分组
$groupParentId      = ArrayUtility::groupByField($listStyle,'parent_id');

foreach($groupParentId as $key => $info){
    
    foreach($info as $keys => $val){
        
        if($val['style_level'] == 0){

            if(in_array($val['style_id'],$listParentId)){
            
                $val['is_parent']   = 1;
            }
            $oneLevelStyleInfo[] = $val;
        }

        if($val['style_level'] == 1){

            $twoLevelStyleInfo[$val['parent_id']][] = $val;
        }        
    }
}
$template = Template::getInstance();

$template->assign('indexStyleId', $indexStyleId);
$template->assign('oneLevelStyleInfo', $oneLevelStyleInfo);
$template->assign('twoLevelStyleInfo', $twoLevelStyleInfo);
$template->assign('mainMenu' , Menu_Info::getMainMenu());
$template->display('manage/style/index.tpl');