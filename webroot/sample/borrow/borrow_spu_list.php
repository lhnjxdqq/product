<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$borrowId   = $_GET['borrow_id'];

Validate::testNull($borrowId,'样板ID不能为空');

$borrowInfo                 = Borrow_Info::getByBorrowId($borrowId);
$condition                  = $_GET;

$borrowInfo                 = Borrow_Info::getByBorrowId($borrowId);
$borrowSpuInfo              = Borrow_Spu_Info::getByBorrowId($borrowId);
$countSpuBorrow             = count($borrowSpuInfo);

$listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = Style_Info::listAll();
$listStyleInfo              = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
$mapStyleId                 = ArrayUtility::indexByField($listStyleInfo,'style_id');
$groupStyleInfo             = ArrayUtility::groupByField($listStyleInfo, 'parent_id');
$sampleType = Sample_Type::getSampleType();

//计价类型
$valuationType      = Valuation_TypeInfo::getValuationType();

foreach($sampleType as $typeId=>$typeName){
    
    $mapSampleType[$typeId] = array(
        
        'sample_type_id'     => $typeId,
        'sample_type_name'   => $typeName,
    );
}

$perpage                    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSpuTotal              = Borrow_Spu_List::countByCondition($condition);
$page                       = new PageList(array(
    PageList::OPT_TOTAL     => $countSpuTotal,
    PageList::OPT_URL       => '/sample/borrow/borrow_spu_list.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSpuInfo                = Borrow_Spu_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
Utility::dump($listSpuInfo);
$listWeightValueId          = ArrayUtility::listField($listSpuInfo,'weight_value_id');
$listMaterialValueId          = ArrayUtility::listField($listSpuInfo,'material_value_id');
$listAssistantMaterialValueId = ArrayUtility::listField($listSpuInfo,'assistant_material_value_id');
$listSupplierId             = ArrayUtility::listField($listSpuInfo,'supplier_id');
$listSpecId                 = array_merge($listWeightValueId,$listMaterialValueId,$listAssistantMaterialValueId);
$listSpuId                  = ArrayUtility::listField($listSpuInfo, 'spu_id');
$listSpuSourceCode          = Common_Spu::getSpuSourceCodeList($listSpuId);
$mapSpuSourceCode           = ArrayUtility::groupByField($listSpuSourceCode, 'spu_id');

$listSpuImages              = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuIdImages           = ArrayUtility::groupByField($listSpuImages, 'spu_id');
$supplierInfo               = Supplier_Info::getByMultiId($listSupplierId);
$indexSupplierId            = ArrayUtility::indexByField($supplierInfo,'supplier_id');
foreach($indexSupplierId as $supplierId => $info){
    
    $plusData       = json_decode($info['price_plus_data'],true);
    if(!empty($plusData)){
    
        $suppluerIdColor[$supplierId]   = $plusData['base_color_id'];   
    }
}
$indexSpecValueId              = ArrayUtility::indexByField(Spec_Value_Info::getByMulitId($listSpecId),'spec_value_id');

foreach ($listSpuInfo as $key => $spuInfo) {

    $spuId              = $spuInfo['spu_id'];
    $listSpuGoodsInfo   = Spu_List::listSpuGoodsInfo($spuId);

    foreach ($listSpuGoodsInfo as $spuGoods) {

        $specValueId    = $spuGoods['spec_value_id'];
        $supplierId     = $spuGoods['supplier_id'];
        if ($specValueId ==  $suppluerIdColor[$spuInfo['supplier_id']]) {

            $listSpuInfo[$key]['sale_cost']  = $spuGoods['sale_cost'];
        }
    }
    if(!empty(ArrayUtility::searchBy($borrowSpuInfo,array('spu_id'=>$info['spu_id'],'sample_storage_id'=>$info['sample_storage_id'])))){

        $listSpuInfo[$key]['is_join']   = 1;
    }else{
    
        $listSpuInfo[$key]['is_join']   = 0;
    }
    $imageInfo          = array();  
    $imageInfo          = $groupSpuIdImages[$spuId];
    
    $firstImageInfo = array();
    if(!empty($imageInfo)){
        
        $firstImageInfo = ArrayUtility::searchBy($imageInfo,array('is_first_picture' => 1));
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info = current($firstImageInfo);
        $listSpuInfo[$key]['image_url']  = !empty($info)
            ? AliyunOSS::getInstance('images-spu')->url($info['image_key'])
            : '';       
    }else{

        $info = Sort_Image::sortImage($imageInfo);
        $listSpuInfo[$key]['image_url']  = !empty($info)
            ? AliyunOSS::getInstance('images-spu')->url($info[0]['image_key'])
            : '';     
    }

}

$data['mapSampleType']      = $mapSampleType; 
$data['searchType']                 = Search_Spu::getSearchType();
$data['mapCategoryInfoLv3']         = $mapCategoryInfoLv3;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['pageViewData']               = $page->getViewData();
$data['groupStyleInfo']             = $groupStyleInfo;

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('data',$data);
$template->assign('condition',$condition);
$template->assign('countSpuBorrow',$countSpuBorrow);
$template->assign('valuationType',$valuationType);
$template->assign('mapStyleId',$mapStyleId);
$template->assign('indexSpecValueId',$indexSpecValueId);
$template->assign('indexSupplierId',$indexSupplierId);
$template->assign('listSpuInfo',$listSpuInfo);
$template->display('sample/borrow/borrow_spu_list.tpl');
