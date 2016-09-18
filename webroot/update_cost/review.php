<?php

require_once dirname(__FILE__).'/../../init.inc.php';

$updateCostId       = $_GET['update_cost_id'];
Validate::testNull($updateCostId,'id不能为空');
$updateCostInfo     = Update_Cost_Info::getByUpdateCostId($updateCostId);
if(empty($updateCostInfo) || $updateCostInfo['status_id'] == Update_Cost_Status::DELETED){
    
     throw   new ApplicationException(array(
        'message'	=> '报价单为空或者已经删除',
        'to_url'	=> '/update_cost/index.php',
     ));
}
$supplierInfo       = ArrayUtility::indexByField(Supplier_Info::listAll(),'supplier_id');

$condition['update_cost_id']    = $updateCostId;
// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countUpdateCostProduct   = Update_Cost_Source_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countUpdateCostProduct,
    PageList::OPT_URL       => '/update_cost/review.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listUpdateCostProductInfo  = Update_Cost_Source_Info::listByCondition($condition, array('relationship_product_id'=>'DESC'), $page->getOffset(), $perpage);
$indexSourceCode            = ArrayUtility::indexByField($listUpdateCostProductInfo,'source_code');

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

//获取规格尺寸和主料材质的属性ID
$specMaterialInfo     = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'material')),'spec_alias','spec_id');
$specSizeInfo         = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'size')),'spec_alias','spec_id');
$specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
$specMaterialId       = $specMaterialInfo['material'];
$specSizeId           = $specSizeInfo['size'];
$specColorId          = $specColorInfo['color'];

$listGoodsId            = array();
foreach($indexSourceCode as $source_code => $info){


    $mapSpuInfo[$source_code][0]            = $info;
    $data                                   = json_decode($info['json_data'],true);
    $mapColorInfo[$source_code] = $data['cost'];
    if(empty($info['relationship_product_id'])){
     
        continue ;
    }
    $mapProductId[$source_code]             = explode(',',$info['relationship_product_id']);
    $mapProductInfo[$source_code]           = Product_Info::getByMultiId($mapProductId[$source_code]);
    $mapGoodsId[$source_code]               = ArrayUtility::listField($mapProductInfo[$source_code],'goods_id');
    $indexGoodsIdCost[$source_code]         = ArrayUtility::indexByField($mapProductInfo[$source_code],'goods_id','product_cost');
    //所有skuId集合
    $mapGoodsIdSpuId[$source_code]          = Spu_Goods_RelationShip::getByMultiGoodsId($mapGoodsId[$source_code]);
    $mapGroupSpuId[$source_code]            = ArrayUtility::groupByField($mapGoodsIdSpuId[$source_code],'spu_id');
    $mapSpuId[$source_code]                 = ArrayUtility::listField($mapGoodsIdSpuId[$source_code],'spu_id');
    //所有SPU
    $mapSpuInfo[$source_code][1]            = ArrayUtility::searchBy(Spu_Info::getByMultiId($mapSpuId[$source_code]),array('delete_status'=>0));

    // 查所当前所有SPU的商品 商品信息 规格和规格值
    $allGoodsInfo           = Goods_Info::getByMultiId($mapGoodsId[$source_code]);
    $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
    $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($mapGoodsId[$source_code]);
    $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

    // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
    $mapSpuGoods[$source_code]    = ArrayUtility::indexByField($mapGoodsIdSpuId[$source_code], 'spu_id', 'goods_id');
    $listGoodsId                  = array_values($mapSpuGoods[$source_code]);
    $listGoodsInfo                = Goods_Info::getByMultiId($listGoodsId);
    $mapGoodsInfo[$source_code]   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

    //获取SPU数量

    $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId(array_unique($mapSpuId[$source_code]));
    $mapSpuImages[$source_code]   = ArrayUtility::indexByField($listSpuImages, 'spu_id');

    foreach ($mapSpuImages[$source_code] as $spuId => $spuImage) {

        $mapSpuImages[$source_code][$spuId]['image_url']  = AliyunOSS::getInstance('images-spu')->url($spuImage['image_key']);
    }

    // 根据商品查询品类
    $listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
    $listCategory   = Category_Info::getByMultiId($listCategoryId);
    $mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

    // 根据商品查询规格重量
    $listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

    foreach ($listSpecValue as $specValue) {

        $specName       = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
        $specValueData  = $mapSpecValueInfo[$specValue['spec_value_id']]['spec_value_data'];
        if ($specName == '规格重量') {

            $mapWeightSpecValue[$source_code][$specValue['goods_id']] = $specValueData;
        }
    }
    
    foreach ($mapGroupSpuId[$source_code] as $spuId => $spuGoods) {

        $mapColor   = array();
        foreach ($spuGoods as $goods) {

            $goodsId        = $goods['goods_id'];
            $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

            foreach ($goodsSpecValue as $key => $val) {

                $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                if($val['spec_id']  == $specMaterialId) {
                    
                    $mapMaterialValue[$spuId][]  = $specValueData;
                }
                if($val['spec_id']  == $specSizeId) {
                    
                    $mapSizeValue[$spuId][]  = $specValueData;
                }
                if($val['spec_id'] == $specColorId) {

                    $mapColor[$spuId][$val['spec_value_id']][]    = $indexGoodsIdCost[$source_code][$goodsId];
                }
            }
        }

        foreach($mapColor as $spuIdKey => $colorInfo){

            foreach($colorInfo as $colorId => $cost){
                
                rsort($cost);
                $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
            }
        }
        $mapSizeValue[$spuId]     = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
        $mapMaterialValue[$spuId] = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";
    }
    
}
//获取颜色属性Id列表
$listSpecValueColotId   = array();

foreach($mapColorInfo as $spuId=>$colorCost){
    
    if(empty($colorCost)){
        
        continue;
    }
    foreach($colorCost as $specColorId=>$cost){
        
        $listSpecValueColotId[$specColorId] = $specColorId;
    }
}
//获取颜色种类数量
$countColor         = count($listSpecValueColotId);
$mapColorValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueColotId);
$mapSpecColorId     = ArrayUtility::indexByField($mapColorValueInfo,'spec_value_id', 'spec_value_data');

$listSpuInfo        = array();
$row                = 0;
foreach($mapSpuInfo as $source=>$listSpu){
    
    $row            = ++$row;

    $productInfo    = json_decode($mapSpuInfo[$source][0]['json_data'],true);

    foreach($mapSpecColorId as $colorId=>$colorName){
    
        $productInfo['color'][$colorId] = !empty($mapColorInfo[$productInfo['sku_code']][$colorId]) ? $mapColorInfo[$productInfo['sku_code']][$colorId] : "-"; 
    }
    $productInfo['category_name']       = $productInfo['categoryLv3'];
    $productInfo['material_name']       = $productInfo['material_main_name'];
    $productInfo['weight_value']        = $productInfo['weight_name'];
    $productInfo['spu_remark']          = $productInfo['remark'];
    $productInfo['source_row']          = $row;  
    unset($productInfo['price']);
    unset($productInfo['cost']);
    if(empty($mapSpuInfo[$source][0]['relationship_product_id'])){
     
        $productInfo['is_new']  = 2;
        $listSpuInfo[]  = $productInfo;
        continue;
    }else{
        $productInfo['is_new']  = 1;
        $listSpuInfo[]  = $productInfo;
    }
    unset($listSpu[0]);
    foreach($listSpu as $spuInfo){
        
        foreach($spuInfo as $key=>$info){
        
            $info['sku_code'] = $productInfo['sku_code'];
            // 品类名 && 规格重量
            
            $goodsId    = $mapSpuGoods[$info['sku_code']][$info['spu_id']];
            if (!$goodsId) {

                $info['category_name'] = '';
                $info['weight_value']  = '';
            } else {

                $categoryId = $mapGoodsInfo[$info['sku_code']][$goodsId]['category_id'];
                $info['category_name'] = $mapCategory[$categoryId]['category_name'];
                $info['material_name'] = !empty($mapMaterialValue[$info['spu_id']]) ? implode(",",$mapMaterialValue[$info['spu_id']]): '';
                $info['size_name']     = !empty($mapSizeValue[$info['spu_id']]) ? implode(",",$mapSizeValue[$info['spu_id']]): '';
                $info['weight_value']  = $mapWeightSpecValue[$info['sku_code']][$goodsId];
            }
         
            $info['color']             = array();
            $info['source_row']        = $row;  
            foreach($mapSpecColorId as $colorId=>$colorName){
            
                $info['color'][$colorId] = !empty($mapColorInfo[$info['spu_id']][$colorId]) ? $mapColorInfo[$info['spu_id']][$colorId] : "-";
            }

            $info['image_url'] = $mapSpuImages[$info['sku_code']][$info['spu_id']]['image_url'];       
            $listSpuInfo[] = $info;
        }
    }
}
$template       = Template::getInstance();

$template->assign('countSpu',$countUpdateCostProduct);
$template->assign('pageViewData',$page->getViewData());
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('updateCostInfo',$updateCostInfo);
$template->assign('supplierInfo',$supplierInfo);
$template->assign('countColor',$countColor);
$template->assign('mapSpecColorId',$mapSpecColorId);
$template->assign('mapSpecValueInfo',$mapSpecValueInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('update_cost/review.tpl');