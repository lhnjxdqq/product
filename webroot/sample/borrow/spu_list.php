<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$condition                  = $_GET;

$borrowId   = $_GET['borrow_id'];
Validate::testNull($borrowId,'借板ID不能为空');

$borrowInfo                 = Borrow_Info::getByBorrowId($borrowId);
$borrowSpuInfo              = Borrow_Spu_Info::getByBorrowId($borrowId);
$countSpuBorrow             = count($borrowSpuInfo);
$condition['start_time']    = $borrowInfo['start_time'];
$condition['end_time']      = $borrowInfo['end_time'];

$listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listStyleInfo              = Style_Info::listAll();
$listStyleInfo              = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
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

$condition['online_status'] = Spu_OnlineStatus::ONLINE;
$condition['delete_status'] = Spu_DeleteStatus::NORMAL;
$condition['is_delete']     = Spu_DeleteStatus::NORMAL;

$perpage                    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSpuTotal              = Search_BorrowSample::countByCondition($condition);
$page                       = new PageList(array(
    PageList::OPT_TOTAL     => $countSpuTotal,
    PageList::OPT_URL       => '/sample/borrow/spu_list.php',
    PageList::OPT_PERPAGE   => $perpage,
));
$listSpecInfo   = Spec_Info::listAll();
$mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');

$listSpuInfo                = Search_BorrowSample::listByCondition($condition, array(), $page->getOffset(), $perpage);

$listSupplierId             = ArrayUtility::listField($listSpuInfo,'supplier_id');

$listSpuId                  = ArrayUtility::listField($listSpuInfo, 'spu_id');
$listSpuSourceCode          = Common_Spu::getSpuSourceCodeList(array_unique($listSpuId));
$mapSpuSourceCode           = ArrayUtility::groupByField($listSpuSourceCode, 'spu_id');
$mapSpuInfo                 = Spu_Info::getByMultiId($listSpuId);
$mapIndexSpuId              = ArrayUtility::indexByField($mapSpuInfo,'spu_id');

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

foreach ($listSpuInfo as $key => $spuInfo) {

    $spuId              = $spuInfo['spu_id'];
    $listSpuGoodsInfo   = Spu_List::listSpuGoodsInfo($spuId);

    $spuCost            = array();
    foreach ($listSpuGoodsInfo as $spuGoods) {

        $listSpuInfo[$key]['category_id'] = $spuGoods['category_id'];
        $listSpuInfo[$key]['weight_value_id']  = $spuGoods['weight_id'];
        $specValueId    = $spuGoods['spec_value_id'];
        $supplierId     = $spuGoods['supplier_id'];
        $spuCost[]      = $spuGoods['sale_cost'];
    }

     $listSpuInfo[$key]['sale_cost']  = max($spuCost);
    if(!empty(ArrayUtility::searchBy($borrowSpuInfo,array('spu_id'=>$spuId,'sample_storage_id'=>$spuInfo['sample_storage_id'])))){

        $listSpuInfo[$key]['is_join']   = 1;
    }else{
    
        $listSpuInfo[$key]['is_join']   = 0;
    }
    
    $sourceCodeList                         = ArrayUtility::listField($mapSpuSourceCode[$spuId], 'source_code');
    $listSpuInfo[$key]['source_code_list']  = implode(',', array_unique($sourceCodeList));
    $listSpuInfo[$key]['spu_sn']            = $mapIndexSpuId[$spuId]['spu_sn'];
    $listSpuInfo[$key]['valuation_type']    = $mapIndexSpuId[$spuId]['valuation_type'];
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
$listWeightValueId          = ArrayUtility::listField($listSpuInfo,'weight_value_id');
$indexWeightId              = ArrayUtility::indexByField(Spec_Value_Info::getByMulitId($listWeightValueId),'spec_value_id');

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
$template->assign('indexWeightId',$indexWeightId);
$template->assign('indexSupplierId',$indexSupplierId);
$template->assign('listSpuInfo',$listSpuInfo);
$template->display('sample/borrow/spu_list.tpl');
