<?php 

require dirname(__FILE__).'/../init.inc.php';

$condition  = array(
    'status_id'    => Update_Cost_Status::UPDATE,
);
$order      = array(
    'create_time'   => 'DESC',
);

$mapUpdateCost = Update_Cost_Info::listByCondition($condition, $order,0,100);

if(empty($mapUpdateCost)){
    
    exit;
}
foreach($mapUpdateCost as $key=>$info){
    
    $mapUpdateCostSourceInfo    = Update_Cost_Source_Info::getByUpdateCostId($info['update_cost_id']);
    $data                       = array();
    foreach($mapUpdateCostSourceInfo as $updateCostSourceInf){
        
        $jsonData                 = json_decode($updateCostSourceInf['json_data'],true);

        $newPrice   = array();
        $newSize    = array();
        if(!empty($updateCostSourceInf['relationship_product_id'])){
            
            $mapProductId             = explode(',',$updateCostSourceInf['relationship_product_id']);
            $mapProductInfo           = Product_Info::getByMultiId($mapProductId);
            $listGoodsId              = ArrayUtility::listField($mapProductInfo,'goods_id');
            $indexGoodsIdProductId    = ArrayUtility::indexByField($mapProductInfo,'goods_id','product_id');
            $listGoodsInfo            = Goods_List::listByCondition(array('list_goods_id'=>$listGoodsId));
            $indexGoodsId             = ArrayUtility::indexByField($listGoodsInfo,'goods_id');
            
            $productCost              = $jsonData['cost'];
            $productSize              = $jsonData['size'];
            $mapGoodsIdSpuId          = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
            $listSpuId                = array_unique(ArrayUtility::listField($mapGoodsIdSpuId,'spu_id'));
            
            foreach($productCost as $colorId=>$colorPrice){
                
                if(!empty($productSize)){
                
                    foreach($productSize as $sizeId){
                        
                        $updateGoodsInfo  = ArrayUtility::searchBy($listGoodsInfo,array('size_value_id'=>$sizeId));
                        if(empty($updateGoodsInfo)){
                            
                            $size[] = $sizeId;
                        }else{    
                            $updateGoodsId  = ArrayUtility::listField(ArrayUtility::searchBy($listGoodsInfo,array('color_value_id'=>$colorId,'size_value_id'=>$sizeId)),'goods_id');
                            if(!empty($updateGoodsId)){
                                
                                foreach($updateGoodsId as $id){

                                    Goods_Info::update(array(
                                        'goods_id'      => $id,
                                        'self_cost'     => $colorPrice+PLUS_COST,
                                        'sale_cost'     => $colorPrice+PLUS_COST,
                                        'delete_status' => Goods_DeleteStatus::NORMAL,
                                        'online_status' => Goods_OnlineStatus::ONLINE,
                                    ));
                                    Product_Info::update(array(
                                        'product_id'    => $indexGoodsIdProductId[$id],
                                        'product_cost'  => $colorPrice,
                                        'delete_status' => Product_DeleteStatus::NORMAL,
                                        'online_status' => Product_OnlineStatus::ONLINE,
                                    ));
                                                                
                                    Cost_Update_Log_Info::create(array(
                                        'product_id'        => $indexGoodsIdProductId[$id],
                                        'cost'              => sprintf('%.2f',$colorPrice),
                                        'handle_user_id'    => $info['auditor_user_id'],
                                        'update_means'      => Cost_Update_Log_UpdateMeans::BATCH,
                                    ));
                                }
                            }else{
                                $cost[$colorId] = $colorPrice;
                            }
                        }
                    }
                    
                }else{                                 
                    $updateGoodsId  = ArrayUtility::listField(ArrayUtility::searchBy($listGoodsInfo,array('color_value_id'=>$colorId)),'goods_id');
                    if(!empty($updateGoodsId)){
                        
                        foreach($updateGoodsId as $id){

                            Goods_Info::update(array(
                                'goods_id'      => $id,
                                'self_cost'     => $colorPrice+PLUS_COST,
                                'sale_cost'     => $colorPrice+PLUS_COST,
                            ));
                            Product_Info::update(array(
                                'product_id'    => $indexGoodsIdProductId[$id],
                                'product_cost'  => $colorPrice,
                            ));
                        }
                    }else{
                        $cost[$colorId] = $colorPrice;
                    }   
                }
                
            }
            
            if(!empty($size)){
                $jsonData['size']           = array_unique($size);
                $jsonData['list_spu_id']    = $listSpuId;
                $data[] = $jsonData;
                $jsonData['size']           = $productSize;
                $size  = array();
            }
            if(!empty($cost)){
                $jsonData['cost']           = $cost;
                $jsonData['list_spu_id']    = $listSpuId;
                $data[] = $jsonData;
                $cost   = array();
            }
        }else{
            
            $data[] = $jsonData;
        }
    }
    if(empty($data)){
                
        Update_Cost_Info::update(array(
            'update_cost_id'       => $info['update_cost_id'],
            'status_id'            => Update_Cost_Status::FINISHED,
        ));
    }else{
        
        $mapEnumeration = array();

        $listCategoryName   = ArrayUtility::listField($data,'categoryLv3');
        $mapStyleInfo       = Style_Info::listAll();
        $mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
        $listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
        if (empty($listGoodsType)) {
            exit("表中无匹配产品类型,请修改后重新上传\n");
        }
        $mapTypeSpecValue   = Goods_Type_Spec_Value_Relationship::getByMulitGoodsTypeId($listGoodsType);
        $mapSpecInfo        = Spec_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_id'));
        $mapIndexSpecAlias  = ArrayUtility::indexByField($mapSpecInfo, 'spec_alias' ,'spec_id');
        $mapSpecValue       = Spec_Value_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_value_id'));
        $mapSizeId          = ArrayUtility::listField(ArrayUtility::searchBy($mapSpecInfo,array("spec_name"=>"规格尺寸")),'spec_id');
        $mapEnumeration =array(
            'mapCategory'          => $mapCategoryName,
            'mapTypeSpecValue'     => $mapTypeSpecValue,
            'mapIndexSpecAlias'    => $mapIndexSpecAlias,
            'mapSpecValue'         => $mapSpecValue,
            'mapSizeId'            => $mapSizeId,
            'mapStyle'             => $mapStyleInfo,
            'mapSpecInfo'          => $mapSpecInfo,
        );

        foreach ($data as $offsetRow => $row) {
            
           $goodsIds[] = Quotation::updateCostcreateQuotation($row,$mapEnumeration,1 ,$info['supplier_id']);
        }
        Update_Cost_Info::update(array(
            'update_cost_id'       => $info['update_cost_id'],
            'status_id'            => Update_Cost_Status::FINISHED,
        ));   
    }
}