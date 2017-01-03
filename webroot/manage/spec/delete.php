<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

$specValueId    = $_GET['spec_value_id'];
Validate::testNull($specValueId,'规格ID不能为空');

$specGoodsInfo  = Goods_Spec_Value_RelationShip::getBySpecValueId($specValueId);
$specInfo   = Spec_Value_Info::getById($specValueId);

$apiList    = Config::get('api|PHP', 'api_list');
$apiUrl         = $apiList['select']['spec_value'];
$plApiUrl       = $apiList['select']['pl_spec_value'];
$deleteData     = array('delete'=>array(
    array(
    'spec_value_id'     => $specValueId,
    'spec_value_data'   => $specInfo['spec_value_data'],
    'serial_number'     => $specInfo['serial_number'],
    'delete_status'     => 1,
)));
if(empty($specGoodsInfo)){
    
    Spec_Value_Info::update(array(
        'spec_value_id' => $specValueId,
        'delete_status' => Spec_Value_DeleteStatus::DELETED,
    ));
    
    if($plApiUrl){

        $res    = HttpRequest::getInstance($plApiUrl)->post($deleteData);
    }

    if($apiUrl){

        $res    = HttpRequest::getInstance($apiUrl)->post($deleteData);
    }
    Utility::notice("删除成功");
    exit;
}
$listGoodsId    = ArrayUtility::listField($specGoodsInfo,'goods_id');

$goodsInfo      = ArrayUtility::searchBy(Goods_Info::getByMultiId($listGoodsId),array('delete_status'=>Goods_DeleteStatus::NORMAL));
if(count($goodsInfo)>0){
    
    Utility::notice("删除失败，有关联SKU未删除");
    
}else{
    
    Spec_Value_Info::update(array(
        'spec_value_id' => $specValueId,
        'delete_status' => Spec_Value_DeleteStatus::DELETED,
    ));

    if($plApiUrl){

        $res    = HttpRequest::getInstance($plApiUrl)->post($deleteData);
    }

    if($apiUrl){

        $res    = HttpRequest::getInstance($apiUrl)->post($deleteData);
    }
    Utility::notice("删除成功");
    exit;
    
}