<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

$condition  = array();
$condition['spec_id']       = $_GET['spec_id'];
$condition['delete_status'] = Spec_Value_DeleteStatus::NORMAL;

$orderBy    = array(
    'serial_number' => 'ASC',
);
$specInfo       = ArrayUtility::indexByField(Spec_Info::listAll(),'spec_id');
if(!empty($_GET['spec_id'])){
    
    $specName   = $specInfo[$_GET['spec_id']]['spec_name'];
}else{
    
    $specName = '规格';
}
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$countSpec      = Spec_Value_List::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSpec,
    PageList::OPT_URL       => '/manage/spec/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSpecInfo       = Spec_Value_List::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$goodsTypeInfo      = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));
$indexGoodsIdName   = ArrayUtility::indexByField($goodsTypeInfo,'goods_type_id','goods_type_name');
$listSpecValueId    = ArrayUtility::listField($listSpecInfo,'spec_value_id');
$goodsTypeSpecValue = ArrayUtility::groupByField(Goods_Type_Spec_Value_Relationship::getByMultiSpecValueId($listSpecValueId),'spec_value_id','goods_type_id');
$goodsTypeNameSpec  = array();
foreach($goodsTypeSpecValue as $specId=> $info){
    
    $goodsTypeNmaeInfo = array();
    
    foreach($info as $key => $goodsTypeId){
        
        $goodsTypeNmaeInfo[] = $indexGoodsIdName[$goodsTypeId];
    }
    $goodsTypeNameSpec[$specId] = implode(',',array_filter(array_unique($goodsTypeNmaeInfo)));
}

$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('listSpecInfo',$listSpecInfo);
$template->assign('specName',$specName);
$template->assign('goodsTypeNameSpec',$goodsTypeNameSpec);
$template->assign('goodsTypeSpecValue',$goodsTypeSpecValue);
$template->assign('goodsTypeInfo',$goodsTypeInfo);

$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('manage/spec/index.tpl');