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
$indexSupplierIdInfo= ArrayUtility::indexByField(Supplier_Info::listAll(),'supplier_id');

$supplierInfo       = Supplier_Info::getById($sampleStorageInfo['supplier_id']);
$colorInfo          = json_decode($supplierInfo["price_plus_data"],true);
$mainColorId        = $colorInfo["base_color_id"];

//用户
$mapUser        = ArrayUtility::indexByField(ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1)),'user_id');

$buyer          = explode(",",$sampleStorageInfo['buyer']);
foreach($buyer as $key => $userId){
    
    $buyerUser[] =  $mapUser[$userId]['username'];
}
$sampleStorageInfo['buyerName'] = implode(",",$buyerUser);
//样版类型
$sampleType = Sample_Type::getSampleType();

//款式
$mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(),array('delete_status'=>0));
$indexStyleId       = ArrayUtility::indexByField($mapStyleInfo,'style_id');

//状态
$sampleStatus       = Sample_Status::getSampleStatus();
//计价类型
$valuationType      = Valuation_TypeInfo::getValuationType();

$condition['sample_storage_id']    = $sampleStroageId;
// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSampleStorage   = Sample_Storage_Spu_Info::countByCondition($condition);

$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSampleStorage,
    PageList::OPT_URL       => '/sample/storage/detail.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSampleStorageSpuInfo   = Sample_Storage_Spu_Info::listByCondition($condition, array('spu_id'=>'DESC'), $page->getOffset(), $perpage);

$indexSpuId           = ArrayUtility::indexByField($listSampleStorageSpuInfo,'spu_id');
$listSpuId            = ArrayUtility::listField($listSampleStorageSpuInfo,'spu_id');

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

$listSpuInfo          = ArrayUtility::indexByField(Spu_Info::getByMultiId($listSpuId),'spu_id');

$listGoodsId            = array();
$mapSpuInfo             = array();
$listSpuSourceCode          = Common_Spu::getSpuSourceCodeList($listSpuId);
$mapSpuSourceCode           = ArrayUtility::groupByField($listSpuSourceCode, 'spu_id');

foreach ($listSpuInfo as  &$spuInfo) {

    $spuId              = $spuInfo['spu_id'];

    $listSpuImages  = Spu_Images_RelationShip::getBySpuId($spuId);
    $mapSpuImages   = ArrayUtility::groupByField($listSpuImages, 'spu_id');

    foreach ($mapSpuImages as $spuId => $spuImage) {

        if(!empty($spuImage)){
            
            $firstImageInfo = ArrayUtility::searchBy($spuImage,array('is_first_picture' => 1));
        }
        if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
            
            $info = current($firstImageInfo);
            $spuInfo['image_url']  = !empty($info)
                ? AliyunOSS::getInstance('images-spu')->url($info['image_key'])
                : '';       
        }else{

            $info = Sort_Image::sortImage($spuImage);

            $spuInfo['image_url']  = !empty($info)
                ? AliyunOSS::getInstance('images-spu')->url($info[0]['image_key'])
                : '';     
        }
    }
    
    //所有skuId集合
    $mapGoodsIdSpuId          = Spu_Goods_RelationShip::getBySpuId($spuId);
    $mapGroupSpuId            = ArrayUtility::groupByField($mapGoodsIdSpuId,'spu_id');
    $mapGoodsId               = ArrayUtility::listField($mapGoodsIdSpuId,'goods_id');

    // 查所当前所有SPU的商品 商品信息 规格和规格值
    $allGoodsInfo           = Goods_Info::getByMultiId($mapGoodsId);
    $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
    $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($mapGoodsId);
    $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

    // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
    $mapSpuGoods    = ArrayUtility::indexByField($mapGoodsIdSpuId, 'spu_id', 'goods_id');
    $listGoodsId                  = array_values($mapSpuGoods);
    $listGoodsInfo                = Goods_Info::getByMultiId($listGoodsId);
    $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

    $mapProductInfo = Product_Info::getByMultiGoodsId($mapGoodsId);
    $indexGoodsIdCost = ArrayUtility::indexByField($mapProductInfo,'goods_id','product_cost');

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

            $spuInfo['weight_value'] = $specValueData;
        }
    }

    foreach ($mapGroupSpuId as $spuId => $spuGoods) {

        $mapColor   = array();
        foreach ($spuGoods as $goods) {

            $goodsId        = $goods['goods_id'];
            $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

            foreach ($goodsSpecValue as $row => $val) {

                $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                if($val['spec_id']  == $specMaterialId) {
                  
                    $mapMaterialValue[$spuId][]  = $specValueData;
                }
                if($val['spec_id']  == $specAssistantMaterialId) {
                    
                    $mapAssistantMaterialValue[$spuId][]  = $specValueData;
                }
                if($val['spec_value_id'] == $mainColorId) {

                    $spuInfo['cost']  = $indexGoodsIdCost[$goodsId];
                }
            }
        }

        $sourceCodeList                         = ArrayUtility::listField($mapSpuSourceCode[$spuId], 'source_code');
        $spuInfo['source_code_list']  = implode(',', array_unique($sourceCodeList));
        $goodsInfo  = current($mapGoodsInfo);
        $spuInfo['style_two_level'] = $indexStyleId[$goodsInfo['style_id']]['style_name'];
        $spuInfo['style_one_level'] = $indexStyleId[$indexStyleId[$goodsInfo['style_id']]['parent_id']]['style_name'];
        $spuInfo['category_name']   = $mapCategory[$goodsInfo['category_id']]['category_name'];
        $spuInfo['assistant_material_name']     = !empty($mapAssistantMaterialValue[$spuId]) ? array_unique($mapAssistantMaterialValue[$spuId]) : "";
        $spuInfo['material_name']               = !empty($mapMaterialValue[$spuId]) ? implode(",",array_unique($mapMaterialValue[$spuId])) : "";
    }
}

$template       = Template::getInstance();

$template->assign('countSpu',$countSampleStorage);
$template->assign('pageViewData',$page->getViewData());
$template->assign('sampleType',$sampleType);
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('sampleStatus',$sampleStatus);
$template->assign('supplierInfo',$supplierInfo);
$template->assign('indexSpuId',$indexSpuId);
$template->assign('indexSupplierIdInfo',$indexSupplierIdInfo);
$template->assign('valuationType',$valuationType);
$template->assign('mapUser',$mapUser);
$template->assign('sampleStorageInfo',$sampleStorageInfo);
$template->assign('mapSpecValueInfo',$mapSpecValueInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sample/storage/detail.tpl');