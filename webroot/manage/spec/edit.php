<?php

require dirname(__FILE__).'/../../../init.inc.php';

$data   = $_POST;

$specValueInfo      = Goods_Type_Spec_Value_Relationship::getByMultiSpecValueId(array($data['spec_value_id']));

$listGoodsTypeId        = ArrayUtility::listField($specValueInfo,'goods_type_id');

if(empty($data['spec_value_id'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '规格ID不能为空',
        'data'      => array(
            'goods_type_id'   => $listGoodsTypeId,
        ),
    ));
    exit;
}
if(empty($data['spec_value_data'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '规格名称不能为空',
        'data'      => array(
            'goods_type_id'   => $listGoodsTypeId,
        ),
    ));
    exit;
}
$specInfo   = Spec_Value_Info::getBySpecValueData($data['spec_value_data']);
if(!empty($specInfo) && $specInfo['spec_value_id']!=$data['spec_value_id']){

    echo json_encode(array(
        'code'      => 1,
        'message'   => '规格名称重复',
        'data'      => array(
            'goods_type_id'   => $listGoodsTypeId,
        ),
    ));
    exit;   
}
$goodsTypeInfo  = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));
$indexGoodsIdName   = ArrayUtility::indexByField($goodsTypeInfo,'goods_type_id','goods_type_name');
Spec_Value_Info::update(array(
    'spec_value_id'     => $data['spec_value_id'],
    'spec_value_data'   => $data['spec_value_data'],
));
$specValueInfo      = Goods_Type_Spec_Value_Relationship::getByMultiSpecValueId(array($data['spec_value_id']));
$specId             = $specValueInfo[0]['spec_id'];
if(!empty($data['spec_id'])){

    $specId = $data['spec_id'];
}

Goods_Type_Spec_Value_Relationship::deleteBySpecValueId($data['spec_value_id']);

if(!empty($data['goods_type_id'])){
    
    foreach($data['goods_type_id'] as $goodsTypeId){

        Goods_Type_Spec_Value_Relationship::create(array(
            'spec_id'       => $specId,
            'spec_value_id' => $data['spec_value_id'],
            'goods_type_id' => $goodsTypeId,
        ));
		$listGoodsType[]	= $indexGoodsIdName[$goodsTypeId];
    }
}else{
    
    foreach($goodsTypeInfo as $key => $info){

        Goods_Type_Spec_Value_Relationship::create(array(
            'spec_id'       => $specId,
            'spec_value_id' => $data['spec_value_id'],
            'goods_type_id' => $info['goods_type_id'],
        ));
		$listGoodsType[]	= $indexGoodsIdName[$info['goods_type_id']];
    }
}
echo json_encode(array(
    'code'      => 0,
    'message'   => '成功',
    'data'      => array(
          'listGoodsName'	=> implode(',',array_unique($listGoodsType)),
    ),
));