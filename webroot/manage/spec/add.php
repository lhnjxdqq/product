<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$data   = $_POST;
Validate::testNull(trim($data['spec_value_data']),'规格名称不能为空');
$specInfo   = Spec_Value_Info::getBySpecValueData($data['spec_value_data']);

if(!empty($specInfo) && $specInfo['delete_status'] == 0){

    $specValueId        = $specInfo['spec_value_id'];
    $specValueGoodsType = Goods_Type_Spec_Value_Relationship::getByMultiSpecValueId(array($specValueId));
    $specId     = array_unique(ArrayUtility::listField($specValueGoodsType,'spec_id'));

    if($data['spec_id'] == $specId[0]){
        Utility::notice('规格重复');
        exit;
    }
}elseif($specInfo['delete_status'] == 1){
    
    Spec_Value_Info::update(array(
        'spec_value_id'   => $specInfo['spec_value_id'],
        'delete_status'   => 0,
    ));
    $specValueId = $specInfo['spec_value_id'];
}else{

    $specValueId = Spec_Value_Info::create(array(
        'spec_value_data'   => trim($data['spec_value_data']),
        'delete_status'     => 0,
    ));     
}
if(!empty($data['goods_type_id'])){
    
    foreach($data['goods_type_id'] as $goodsTypeId){

        Goods_Type_Spec_Value_Relationship::create(array(
            'spec_id'       => $data['spec_id'],
            'spec_value_id' => $specValueId,
            'goods_type_id' => $goodsTypeId,
        ));
    }
}else{
    $goodsTypeInfo  = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));
    
    foreach($goodsTypeInfo as $key => $info){

        Goods_Type_Spec_Value_Relationship::create(array(
            'spec_id'       => $data['spec_id'],
            'spec_value_id' => $specValueId,
            'goods_type_id' => $info['goods_type_id'],
        ));
    }
}
Utility::notice('添加成功');
exit;