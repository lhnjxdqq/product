<?php

require dirname(__FILE__).'/../../init.inc.php';

$data = $_GET;
Validate::testNull($data['source_code'],'买款ID不能为空');
Validate::testNull($data['update_cost_id'],'新报价Id不能为空');

if(empty($data['spu_id'])){
    
    Update_Cost_Source_Info::delete($data['update_cost_id'],$data['source_code']);

}else{
    
    $spuGoodsInfo               = Spu_Goods_RelationShip::getBySpuId($data['spu_id']);
    //sku列表
    $listGoodsId                = ArrayUtility::listField($spuGoodsInfo,'goods_id');
    $updataCosrSourceInfo       = current(Update_Cost_Source_Info::listByCondition(array('source_code'=>$data['source_code'],'update_cost_id'=>$data['update_cost_id']),array()));
    $mapProductId               = explode(',',$updataCosrSourceInfo['relationship_product_id']);
    $mapProductInfo             = Product_Info::getByMultiId($mapProductId);
    $indexProductIdGoodsId      = ArrayUtility::indexByField($mapProductInfo,'product_id','goods_id');
    foreach($indexProductIdGoodsId as $productId =>$goodsId){
        
        if(in_array($goodsId,$listGoodsId)){
            
            unset($indexProductIdGoodsId[$productId]);
        }
    }
    $listProductId              = array_keys($indexProductIdGoodsId);
    
    Update_Cost_Source_Info::update(array(
        'update_cost_id'            => $data['update_cost_id'],
        'source_code'               => $data['source_code'],
        'relationship_product_id'   => empty($listProductId)? '' :implode(',',$listProductId),
    ));
}
$count  = Update_Cost_Source_Info::countByCondition(array('update_cost_id'=>$data['update_cost_id']));
if($count>0){
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $data['update_cost_id'],
        'sample_quantity'      => $count,
    ));
    Utility::notice('删除成功');
}else{
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $data['update_cost_id'],
        'status_id'            => Update_Cost_Status::DELETED,
    ));
    Utility::notice('改价单中已经没有产品,删除完成','/update_cost/index.php');
}