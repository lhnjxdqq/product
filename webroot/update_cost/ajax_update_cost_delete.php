<?php

require dirname(__FILE__) .'/../../init.inc.php';

$data   = $_POST;
if(empty($data['update_cost_id'])){
    
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '新报价单ID不能为空',
        'data'      => array(
        ),
    ));
}
foreach($data['source_code'] as $info){
    
    $info   = explode("&",$info);

    if(empty($info[1])){
        
        Update_Cost_Source_Info::delete($data['update_cost_id'],$info[0]);

    }else{
        
        $spuGoodsInfo               = Spu_Goods_RelationShip::getBySpuId($info[1]);
        //sku列表
        $listGoodsId                = ArrayUtility::listField($spuGoodsInfo,'goods_id');
        $updataCosrSourceInfo       = current(Update_Cost_Source_Info::listByCondition(array('source_code'=>$info[0],'update_cost_id'=>$data['update_cost_id']),array()));
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
            'source_code'               => $info[0],
            'relationship_product_id'   => empty($listProductId)? '' :implode(',',$listProductId),
        ));
    }
}
$count  = Update_Cost_Source_Info::countByCondition(array('update_cost_id'=>$data['update_cost_id']));
if($count>0){
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $data['update_cost_id'],
        'sample_quantity'      => $count,
    ));
    
    echo    json_encode(array(
        'code'      => 0,
        'message'   => '删除成功',
        'data'      => array(
        ),
    ));
}else{
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $data['update_cost_id'],
        'status_id'            => Update_Cost_Status::DELETED,
    ));
    
    echo    json_encode(array(
        'code'      => 0,
        'message'   => '改价单中已经没有产品,删除完成',
        'data'      => array(
        ),
    ));
}