<?php 

//修改产品价格
require dirname(__FILE__).'/../init.inc.php';

$condition  = array(
    'status_id'    => Update_Cost_Status::WAIT_UPDATE,
);
$order      = array(
    'create_time'   => 'DESC',
);

$mapUpdateCost = Update_Cost_Info::listByCondition($condition, $order,0,1);

if(empty($mapUpdateCost)){
    
    exit;
}

foreach($mapUpdateCost as $key=>$info){
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $info['update_cost_id'],
        'status_id'            => Update_Cost_Status::UPDATE,
    ));
    $mapUpdateCostSourceInfo    = Update_Cost_Source_Info::getByUpdateCostId($info['update_cost_id']);
    $data                       = array();
    foreach($mapUpdateCostSourceInfo as $updateCostSourceInf){
        
        $jsonData                 = json_decode($updateCostSourceInf['json_data'],true);

        if(!empty($updateCostSourceInf['relationship_product_id'])){
            
            $mapProductId             = explode(',',$updateCostSourceInf['relationship_product_id']);
            $mapProductInfo           = ArrayUtility::searchBy(Product_Info::getByMultiId($mapProductId),array('delete_status'=>0));
            if(empty($mapProductInfo)){
                
                $data[] = $jsonData;
                continue;
            }
            $listGoodsId              = ArrayUtility::listField($mapProductInfo,'goods_id');
            $indexGoodsIdProductId    = ArrayUtility::indexByField($mapProductInfo,'goods_id','product_id');
            $listGoodsInfo            = Goods_List::listByCondition(array('list_goods_id'=>$listGoodsId,'delete_status'=>'0'));
            if(empty($listGoodsInfo)){
                
                $data[] = $jsonData;
                continue;
            }
            $indexGoodsId             = ArrayUtility::indexByField($listGoodsInfo,'goods_id');
            
            $productCost              = $jsonData['cost'];
            $productSize              = $jsonData['size'];
            $mapGoodsIdSpuId          = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
            $indexSpuIdGoodsId        = ArrayUtility::groupByField($mapGoodsIdSpuId,'goods_id');
            $listSpuId                = array_unique(ArrayUtility::listField($mapGoodsIdSpuId,'spu_id'));
            $mapSpuInfo               = ArrayUtility::searchBy(Spu_Info::getByMultiId($listSpuId),array('delete_status'=>0));
            $listNotDeletedSpuId      = ArrayUtility::listField($mapSpuInfo,'spu_id');    
            
            if(empty($mapSpuInfo)){
                
                $data[] = $jsonData;
                continue;
            }

            foreach($productCost as $colorId=>$colorPrice){
                
                if(!empty($productSize)){
                
                    foreach($productSize as $key => $sizeId){
                        
                        $updateGoodsInfo  = ArrayUtility::searchBy($listGoodsInfo,array('size_value_id'=>$sizeId));

                        if(empty($updateGoodsInfo)){
                            
                            $size[] = $sizeId;
                            unset($jsonData['size'][$key]);
                            
                        }else{
                            
                            $updateGoodsId  = ArrayUtility::listField(ArrayUtility::searchBy($listGoodsInfo,array('color_value_id'=>$colorId,'size_value_id'=>$sizeId)),'goods_id');
                            
                            if(!empty($updateGoodsId)){
                                
                                foreach($updateGoodsId as $id){
                                    
                                    $listSpuId  = ArrayUtility::listField($indexSpuIdGoodsId[$id],'spu_id');

                                    if(empty(array_intersect($listSpuId,$listNotDeletedSpuId))){
                                        
                                        $cost[$colorId] = $colorPrice;
                                    }else{

                                        Goods_Info::update(array(
                                            'goods_id'      => $id,
                                            'self_cost'     => $colorPrice+PLUS_COST,
                                            'sale_cost'     => $colorPrice+PLUS_COST,
                                            'delete_status' => Goods_DeleteStatus::NORMAL,
                                            'online_status' => Goods_OnlineStatus::ONLINE,
                                        ));
                                        Goods_Push::linePushByMultiSkuId('online',array($id));

                                        $spuGoodsInfo = Spu_Goods_RelationShip::getByGoodsId($id);

                                        if(!empty($spuGoodsInfo)){
                                            
                                            foreach($spuGoodsInfo as $key=>$val){
                                                
                                                Spu_Info::update(array(
                                                    'spu_id'        => $val['spu_id'],
                                                    'online_status' => Spu_OnlineStatus::ONLINE,
                                                ));
                                            }
                                        }
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
            $sizeOld    = $jsonData['size'];
            if(!empty($size)){
                $jsonData['size']           = array_unique($size);
                $jsonData['list_spu_id']    = $listSpuId;
                $data[] = $jsonData;
                $jsonData['size']           = $productSize;
                $size  = array();
            }
            if(!empty($cost)){
                $jsonData['cost']           = $cost;
                $jsonData['size']           = $sizeOld;
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
        $mapValuation       = Valuation_TypeInfo::getValuationType();
        $listCategoryName   = ArrayUtility::listField($data,'categoryLv3');
        $mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
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
            'supplierMarkupRuleId' => $info['supplier_markup_rule_id'],
            'mapTypeSpecValue'     => $mapTypeSpecValue,
            'mapIndexSpecAlias'    => $mapIndexSpecAlias,
            'mapSpecValue'         => $mapSpecValue,
            'mapSizeId'            => $mapSizeId,
            'mapStyle'             => $mapStyleInfo,
            'mapSpecInfo'          => $mapSpecInfo,
            'mapValuation'         => $mapValuation,
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
