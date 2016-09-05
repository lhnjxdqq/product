<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$userId             = $_SESSION['user_id'];
$sampleType         = $_POST['sample_type']; 
$listCartSku        = Cart_Goods_Sample::getByUserId($userId);

Validate::testNull($sampleType,'请选择样板类型');
Validate::testNull($userId,'登录后进行操作');

if(empty($listCartSku)){
    
    Utility::notice('样板购物车中没有产品,在进行操作','/product/sku/index.php');   
}
$listGoodsId        = ArrayUtility::listField($listCartSku , 'goods_id');
$listGoodsInfo      = Sample_Info::getByMultiId($listGoodsId);
$listSkuId          = ArrayUtility::listField($listGoodsInfo,'goods_id');

foreach($listGoodsId as $id){
    
    if(!empty(ArrayUtility::searchBy($listGoodsInfo,array('goods_id'=>$id,'is_delete'=>Role_DeleteStatus::DELETED)))){
        
        Sample_Info::update(array(
                'goods_id'      => $id,
                'sample_type'   => $sampleType,
                'is_delete'     => Goods_DeleteStatus::NORMAL,
        ));
    }
    if(!in_array($id,$listSkuId)){
         
        Sample_Info::create(array(
                'goods_id'      => $id,
                'sample_type'   => $sampleType,
        ));   
    }
}
Cart_Goods_Sample::cleanByUserId($userId);
Utility::notice('添加成功','/sample/index.php');