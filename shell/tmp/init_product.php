<?php

require_once    dirname(__FILE__).'/../../init.inc.php';

ignore_user_abort();

//获取黄金黄,K白的specID
$specValueInfo      = Spec_Value_Info::getByMultiValueData(array('黄金黄','K白'));
$kwhiteInfo         = current(ArrayUtility::searchBy($specValueInfo,array('spec_value_data'=>'K白')));
$goldInfo           = current(ArrayUtility::searchBy($specValueInfo,array('spec_value_data'=>'黄金黄')));
$kwhiteId           = $kwhiteInfo['spec_value_id'];
$goldId             = $goldInfo['spec_value_id'];

$weightSpecInfo           = Spec_Info::getByAlias('weight');
$colorSpecInfo            = Spec_Info::getByAlias('color');
$materialSpecInfo         = Spec_Info::getByAlias('material');
$sizeSpecInfo             = Spec_Info::getByAlias('size');

//获取工厂信息
$supplierInfoSql    = 'SELECT * FROM `supplier_info` WHERE `supplier_code` in ("0006","0097","0131","0267","0266")';

$supplierInfo       = DB::instance('product')->fetchAll($supplierInfoSql);

foreach($supplierInfo as &$info){
    
    switch($info['supplier_code']){
        case "0006" :
            $info['plus_price'] = 2;
            break;
        case "0097" :
            $info['plus_price'] = 1;
            break;
        case "0131" : 
            $info['plus_price'] = 1;
            break;
        case "0267" :
            $info['plus_price'] = 1;
            break;
        case "0266" :
            $info['plus_price'] = 0.5;
            break;
    }
}
$indexSupplierId    = ArrayUtility::indexByField($supplierInfo,'supplier_id');
$listSupplierId     = ArrayUtility::listField($supplierInfo,'supplier_id');

//获取分类信息
$listCategory       = Category_Info::listAll();
$indexCategoryId    = ArrayUtility::indexByField($listCategory,'category_id');

//获取属性值
$listSpec           = Spec_Value_Info::listAll();
$indexSpecValueId   = ArrayUtility::indexByField($listSpec,'spec_value_id');

//获取工厂未删除的买款ID
$sourceInfo         = Source_Info::getByMulitSupplierId($listSupplierId);

foreach($sourceInfo as $key => $info){
    
    $plusPrice      = $indexSupplierId[$info['supplier_id']]['plus_price'];

    $condition      = array();
    $sourceId       = $info['source_id'];
    
    echo '正在修改买款ID为'.$info['source_id'].',买款代码为'.$info['source_code'].'的产品'."\n";
    $productInfo    = Product_Info::getBySourceId($sourceId);
    $listGoodsId    = ArrayUtility::listField($productInfo,'goods_id');
    $goodsInfo      = ArrayUtility::searchBy(Goods_Info::getByMultiId($listGoodsId),array('delete_status'=>Goods_DeleteStatus::NORMAL));
    
    if(empty($goodsInfo)){
        
        continue;
    }
    
    $indexGoodsIdInfo   = ArrayUtility::indexByField($goodsInfo,'goods_id'); 
    $listGoodsSn        = ArrayUtility::listField($goodsInfo,'goods_sn');
    
    if(empty($listGoodsSn)){
        
        continue;
    }

    $condition['color_value_id']        = $kwhiteId;
    $condition['search_type']           = "goods_sn";
    $condition['search_value_list']     = implode(" ",$listGoodsSn);
  
    $mapGoodsInfo   = Search_Sku::listByCondition($condition);
    if(empty($mapGoodsInfo)){
        
        continue;
    }
    //获取所有尺寸
    $listSizeInfo   = ArrayUtility::listField($mapGoodsInfo,'size_value_id');
    $condition['color_value_id']        = $goldId;

    $mapGoldGoodsInfo   = Search_Sku::listByCondition($condition);
    $listGoldSizeInfo   = array();
    
    if(!empty($mapGoldGoodsInfo)){
        
        $listGoldSizeInfo = ArrayUtility::listField($mapGoldGoodsInfo,'size_value_id');
    }

    foreach($mapGoodsInfo as $goodsInfo){
        
        $sizeValueId  = $goodsInfo['size_value_id'];
        
        if(in_array($sizeValueId,$listGoldSizeInfo)){
            
            continue;
        }
        $goodsName  = $indexSpecValueId[$sizeValueId]['spec_value_data']."黄金黄";
        $contentGoods   = array(
            'goods_sn'      => Goods_Info::createGoodsSn($indexCategoryId[$goodsInfo['category_id']]['category_sn']),
            'goods_name'    => $goodsName,
            'goods_type_id' => $indexGoodsIdInfo[$goodsInfo['goods_id']]['goods_type_id'],
            'category_id'   => $goodsInfo['category_id'],
            'self_cost'     => $goodsInfo['self_cost']+$plusPrice,
            'sale_cost'     => $goodsInfo['sale_cost']+$plusPrice,
            'style_id'      => $goodsInfo['style_id'],
            'goods_remark'  => $goodsInfo['remark'],
        );

        // 记录商品的规格 和 规格值
        $goodsId            = Goods_Info::create($contentGoods);
        $specValueList      = array(
            array(
                'spec_id'           => $weightSpecInfo['spec_id'],
                'spec_value_id'   => $goodsInfo['weight_value_id'],
            ),
            array(
                'spec_id'           => $colorSpecInfo['spec_id'],
                'spec_value_id'     => $goldId,
            ),
            array(
                'spec_id'           => $materialSpecInfo['spec_id'],
                'spec_value_id'     => $goodsInfo['material_value_id'],
            ),
            array(
                'spec_id'           => $sizeSpecInfo['spec_id'],
                'spec_value_id'    => $sizeValueId,
            ),
        );
        $goodsSpuInfo       = Spu_Goods_RelationShip::getByGoodsId($goodsInfo['goods_id']);
        $listSpuId          = ArrayUtility::listField($goodsSpuInfo,'spu_id');
        foreach($listSpuId as $spuId){
 
            $content =array(
                'spu_id'            => $spuId,
                'goods_id'          => $goodsId,
                'spu_goods_name'    => $goodsName,
            );
            Spu_Goods_RelationShip::create($content);
        }

        foreach ($specValueList as $specValue) {
            
            if(!empty($specValue['spec_value_id'])){
                 
                Goods_Spec_Value_RelationShip::create(array(
                    'goods_id'      => $goodsId,
                    'spec_id'       => $specValue['spec_id'],
                    'spec_value_id' => $specValue['spec_value_id'],
                ));   
            }
        }

        // 新增产品
        $productData    = array(
            'product_sn'        => Product_Info::createProductSn($indexCategoryId[$goodsInfo['category_id']]['category_sn']),
            'product_name'      => $goodsName,
            'product_cost'      => sprintf('%.2f', ($goodsInfo['self_cost']-2+$plusPrice)),
            'source_id'         => $sourceId,
            'product_remark'    => $goodsInfo['remark'],
        );
        $productData['goods_id']    = $goodsId;

        Goods_Info::update(array(
            'goods_id'      => $goodsId,
            'online_status' => Goods_OnlineStatus::ONLINE,
        ));
        $productId                  = Product_Info::create($productData);
        Cost_Update_Log_Info::create(array(
            'product_id'        => $productId,
            'cost'              => $productData['product_cost'],
            'update_means'      => Cost_Update_Log_UpdateMeans::BATCH,
        ));
    }
}