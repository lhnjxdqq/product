<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$sampleStroageId    = $_GET['sample_id'];
Validate::testNull($_GET['sample_id'],'样本入库单id不能为空','/sample/storage/index.php');

$sampleStorageInfo     = Sample_Storage_Info::getById($sampleStroageId);
if(empty($sampleStorageInfo) || $sampleStorageInfo['status_id'] == Sample_Status::DELETED){
    
     throw   new ApplicationException(array(
        'message'   => '样板为空或者已经删除',
        'to_url'    => '/sample/storage/index.php',
     ));
}
$indexSupplierIdInfo       = ArrayUtility::indexByField(Supplier_Info::listAll(),'supplier_id');

$supplierInfo       = Supplier_Info::getById($sampleStorageInfo['supplier_id']);
$colorInfo          = json_decode($supplierInfo["price_plus_data"],true);
$mainColorId        = $colorInfo["base_color_id"];

//用户
$mapUser        = ArrayUtility::indexByField(ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1)),'user_id');
$listAllCommodityConsultant     = ArrayUtility::searchBy(Commodity_Consultant_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));
$mapCommodityConsultant         = ArrayUtility::indexByField($listAllCommodityConsultant,'commodity_consultant_id');
$buyer          = explode(",",$sampleStorageInfo['buyer']);

foreach($buyer as $key => $commodityConsultantId){
    
    $buyerUser[] =  $mapCommodityConsultant[$commodityConsultantId]['commodity_consultant_name'];
}
$sampleStorageInfo['buyerName'] = implode(",",$buyerUser);
//样版类型
$sampleType = Sample_Type::getSampleType();
$allSample  = Sample_Type::getAllType();
//样式
$mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(),array('delete_status'=>0));
$indexStyleId       = ArrayUtility::indexByField($mapStyleInfo,'style_id');

//计价类型
$valuationType      = Valuation_TypeInfo::getValuationType();

$condition['sample_storage_id']    = $sampleStroageId;
// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSampleStorage   = Sample_Storage_Cart_Info::countByCondition($condition);

$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSampleStorage,
    PageList::OPT_URL       => '/sample/storage/reviewed.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSampleStorageSpuInfo  = Sample_Storage_Cart_Info::listByCondition($condition, array('relationship_spu_id'=>'DESC'), $page->getOffset(), $perpage);
$indexSourceCode            = ArrayUtility::indexByField($listSampleStorageSpuInfo,'source_code');

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
$specAssistantMaterialInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'assistant_material')),'spec_alias','spec_id');
$specMaterialId       = $specMaterialInfo['material'];
$specSizeId           = $specSizeInfo['size'];
$specAssistantMaterialId          = $specColorInfo['assistant_material'];

//品类
$listCategory   = Category_Info::listAll();
$mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

$listGoodsId            = array();
$mapSpuInfo             = array();
foreach($indexSourceCode as $source_code => $info){


    $mapSpuInfo[$source_code][0]            = $info;
    $data                                   = json_decode($info['json_data'],true);
    if(empty($info['relationship_spu_id'])){
     
        continue ;
    }
    $mapSpuId[$source_code]                 = explode(',',$info['relationship_spu_id']);
    //所有SPU
    $mapSpuInfo[$source_code][1]            = ArrayUtility::searchBy(Spu_Info::getByMultiId($mapSpuId[$source_code]),array('delete_status'=>0));

    if(empty($mapSpuInfo[$source_code][1])){
        
        continue;
    }
    //获取SPU数量

    $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId(array_unique($mapSpuId[$source_code]));
    $mapSpuImages[$source_code]   = ArrayUtility::groupByField($listSpuImages, 'spu_id');

    foreach ($mapSpuImages[$source_code] as $spuId => $spuImage) {

        if(!empty($spuImage)){
            
            $firstImageInfo = ArrayUtility::searchBy($spuImage,array('is_first_picture' => 1));
        }
        if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
            
            $info = current($firstImageInfo);
            $mapSpuImages[$source_code][$spuId]['image_url']  = !empty($info)
                ? AliyunOSS::getInstance('images-spu')->url($info['image_key'])
                : '';       
        }else{

            $info = Sort_Image::sortImage($spuImage);

            $mapSpuImages[$source_code][$spuId]['image_url']  = !empty($info)
                ? AliyunOSS::getInstance('images-spu')->url($info[0]['image_key'])
                : '';     
        }
    }
    
    //所有skuId集合
    $mapGoodsIdSpuId[$source_code]          = Spu_Goods_RelationShip::getByMultiSpuId($mapSpuId[$source_code]);
    $mapGroupSpuId[$source_code]            = ArrayUtility::groupByField($mapGoodsIdSpuId[$source_code],'spu_id');
    $mapGoodsId[$source_code]               = ArrayUtility::listField($mapGoodsIdSpuId[$source_code],'goods_id');

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
    
    $mapProductInfo[$source_code]= Product_Info::getByMultiGoodsId($mapGoodsId[$source_code]);
    $indexGoodsIdCost[$source_code] = ArrayUtility::indexByField($mapProductInfo[$source_code],'goods_id','product_cost');

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
                if($val['spec_id']  == $specAssistantMaterialId) {
                    
                    $mapAssistantMaterialValue[$spuId][]  = $specValueData;
                }
                if($val['spec_id']  == $specSizeId) {
                    
                    $mapSizeValue[$spuId][]  = $specValueData;
                }
                if($val['spec_value_id'] == $mainColorId) {

                    $mapColorValue[$spuId]    = $indexGoodsIdCost[$source_code][$goodsId];
                }
            }
        }
        $mapSizeValue[$spuId]                   = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
        $mapAssistantMaterialValue[$spuId]      = !empty($mapAssistantMaterialValue[$spuId]) ? array_unique($mapAssistantMaterialValue[$spuId]) : "";
        $mapMaterialValue[$spuId]               = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";
    }
}

$listSpuInfo        = array();
$row                = 0;

foreach($mapSpuInfo as $source=>$listSpu){
    
    $row            = ++$row;

    $productInfo    = json_decode($mapSpuInfo[$source][0]['json_data'],true);

    $productInfo['category_name']       = $productInfo['categoryLv3'];
    $productInfo['material_name']       = $productInfo['material_main_name'];
    $productInfo['weight_value']        = $productInfo['weight_name'];
    $productInfo['assistant_material_name']        = $productInfo['assistant_material'];
    $productInfo['spu_remark']          = $productInfo['remark'];
    $productInfo['source_row']          = $row;  
    $productInfo['source_type']         = 1; 
    
    if(empty($mapSpuInfo[$source][1])){
     
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
        
            $productInfo['source_type'] = 0;
            $info['sku_code'] = $productInfo['sku_code'];
            $sampleSpu          = Sample_Storage_Spu_Info::getBySpuId($info['spu_id']);
            if(!empty($sampleSpu)){
                $listSampleType = array_unique(ArrayUtility::listField($sampleSpu,'sample_type'));
                foreach($listSampleType as $spuSampleType){
                    $typeName[] = $allSample[$spuSampleType];
                }
                $info['sample_type_name'] = implode(",",array_unique($typeName));
            }
            // 品类名 && 规格重量
            $goodsId    = $mapSpuGoods[$info['sku_code']][$info['spu_id']];

            if (!$goodsId) {

                $info['category_name'] = '';
                $info['weight_value']  = '';
            } else {

                $goodsInfo  = $mapGoodsInfo[$info['sku_code']][$goodsId];
                $categoryId = $goodsInfo['category_id'];
                $info['category_name'] = $mapCategory[$categoryId]['category_name'];
                $info['material_name'] = !empty($mapMaterialValue[$info['spu_id']]) ? implode(",",$mapMaterialValue[$info['spu_id']]): '';
                $info['assistant_material_name'] = !empty($mapAssistantMaterialValue[$info['spu_id']]) ? implode(",",$mapAssistantMaterialValue[$info['spu_id']]): '';
                $info['size_name']     = !empty($mapSizeValue[$info['spu_id']]) ? implode(",",$mapSizeValue[$info['spu_id']]): '';
                $info['weight_value']  = $mapWeightSpecValue[$info['sku_code']][$goodsId];
                $info['cost']          = $mapColorValue[$info['spu_id']];
                if(!empty($goodsInfo['style_id'])){
                    
                    $info['style_two_level'] = $indexStyleId[$goodsInfo['style_id']]['style_name'];
                    $info['style_one_level'] = $indexStyleId[$indexStyleId[$goodsInfo['style_id']]['parent_id']]['style_name'];
                }
            }
         
            $info['color']             = array();
            $info['source_row']        = $row;
            $info['image_url'] = $mapSpuImages[$info['sku_code']][$info['spu_id']]['image_url'];       
            $listSpuInfo[] = $info;
        }
    }

}

$template       = Template::getInstance();

$template->assign('countSpu',$countSampleStorage);
$template->assign('pageViewData',$page->getViewData());
$template->assign('sampleType',$sampleType);
$template->assign('allSample',$allSample);
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('supplierInfo',$supplierInfo);
$template->assign('indexSupplierIdInfo',$indexSupplierIdInfo);
$template->assign('valuationType',$valuationType);
$template->assign('mapUser',$mapUser);
$template->assign('sampleStorageInfo',$sampleStorageInfo);
$template->assign('mapSpecValueInfo',$mapSpecValueInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sample/storage/review.tpl');