<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

if(strstr($_SERVER['HTTP_REFERER'],'/sales_quotation/index.php')){
    
    $_SESSION['sales_quotation_page']  = $_SERVER['HTTP_REFERER'];
}

$salesQuotationId           = (int) $_GET['sales_quotation_id'];
$salesQuotationMarkupRule   = (int) $_GET['plue_price'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");
//获取报价单信息
$salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);

$indexCartColorId   = array();
if(is_numeric($_GET['customer_id']) && !empty($_GET['customer_id'] && !empty($salesQuotationMarkupRule))){
 
    $order      = array();   
    $customerId = $_GET['customer_id'];
    $salesQuotationInfo['customer_id'] = $customerId;
    $salesCondition = array(
        'customer_id'   => $customerId,
        'is_confirm'    => Sales_Quotation_ConfirmStatus::YES,
    );
    $mapCartSpuInfo    = Sales_Quotation_Info::listByCondition($salesCondition, $order, 0, 100);
    if(!empty($mapCartSpuInfo)){
        
        //该用户下所有销售出货单的记录ID
        $listSalesQuotation         = ArrayUtility::listField($mapCartSpuInfo, 'sales_quotation_id');
        $mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId($listSalesQuotation);
        $spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');
        foreach($spuInfo as $spuId=>$info){
            
            $indexOldCartColorId[$spuId]['color'] = ArrayUtility::indexByField($info, 'color_id', 'cost');
            $indexOldCartColorId[$spuId]['sales_quotation_remark'] = ArrayUtility::indexByField($info, 'spu_id', 'sales_quotation_remark');
        }
    }
}

//获取客户列表
$listCustomer       = ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL));
$indexCustomerId    = ArrayUtility::indexByField($listCustomer,'customer_id');

$orderBy                = array(
    'is_cart_join'                       => 'DESC',
    'is_red_bg'                          => 'DESC',
    'identical_source_code_spu_num'      => 'DESC',
);
$condition['search_value_list']      = $_GET['search_value_list']; 
 
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$condition['sales_quotation_id']   = $salesQuotationId;
$group              = 'spu_id'; 
$countSpu   = !empty($condition['search_value_list']) ? Search_SalesQuotationSpu::countByCondition($condition)
                : Sales_Quotation_Spu_Info::countBySalesQuotationId($salesQuotationId);


$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSpu,
    PageList::OPT_URL       => '/sales_quotation/edit.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listQuotationSpuInfo       = !empty($condition['search_value_list']) ? Search_SalesQuotationSpu::listByCondition($condition, $orderBy, $page->getOffset(), $perpage) : 
                              Sales_Quotation_Spu_Info::listByCondition($condition, $orderBy, $group, $page->getOffset(), $perpage);
                              
$listSpuId                  = ArrayUtility::listField($listQuotationSpuInfo,'spu_id');
$indexSpuIdRed              = ArrayUtility::indexByField($listQuotationSpuInfo,'spu_id','is_red_bg');
$indexSpuIdIsCartJoin       = ArrayUtility::indexByField($listQuotationSpuInfo,'spu_id','is_cart_join');
$indexSpuIdSource           = ArrayUtility::indexByField($listQuotationSpuInfo,'spu_id','identical_source_code_spu_num');

//获取颜色工费和备注
$indexCartColorId   = array();
$mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationIdAndMuitlSpuId($salesQuotationId , $listSpuId);
$spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');

foreach($spuInfo as $spuId=>$info){
    
    $indexCartColorId[$spuId]['color'] = ArrayUtility::indexByField($info, 'color_id', 'cost');
    $indexCartColorId[$spuId]['sales_quotation_remark'] = ArrayUtility::indexByField($info, 'spu_id', 'sales_quotation_remark');
}

$mapSpuInfo     = ArrayUtility::indexByField(Spu_Info::getByMultiId($listSpuId),'spu_id');

foreach($listSpuId as $spuId){

    $listSpuInfo[] = $mapSpuInfo[$spuId];
}

//获取SPU数量
$listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuIdImages   = ArrayUtility::groupByField($listSpuImages, 'spu_id');

foreach ($groupSpuIdImages as $spuId => $spuImage) {
    
    $firstImageInfo = array();
    if(!empty($spuImage)){
        
        $firstImageInfo = ArrayUtility::searchBy($spuImage,array('is_first_picture' => 1));
    }
    if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
        
        $info = current($firstImageInfo);
        $keyImage   = $info['image_key'];
    }else{

        $info = Sort_Image::sortImage($spuImage);

        $keyImage  = !empty($info)
            ? $info[0]['image_key']
            : '';     
    }
    $mapSpuImages[$spuId]['image_url']  = AliyunOSS::getInstance('images-spu')->url($keyImage);
}

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

// 查询SPU下的商品
$listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

// SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
$mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
$listGoodsId    = array_values($mapSpuGoods);
$listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

// 根据商品查询品类
$listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
$listCategory   = Category_Info::getByMultiId($listCategoryId);
$mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');
$mapProductInfo = Product_Info::getByMultiGoodsId($listGoodsId);

//买款ID
$listSpuSourceCode          = Common_Spu::getSpuSourceCodeList($listSpuId);
$mapSpuSourceCode           = ArrayUtility::groupByField($listSpuSourceCode, 'spu_id');

// 根据商品查询规格重量
$listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

//获取规格尺寸和主料材质的属性ID
$specMaterialInfo     = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'material')),'spec_alias','spec_id');
$specSizeInfo         = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'size')),'spec_alias','spec_id');
$specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
$specMaterialId       = $specMaterialInfo['material'];
$specSizeId           = $specSizeInfo['size'];
$specColorId          = $specColorInfo['color'];

$mapSpecValue   = array();
$mapMaterialValue = array();
$mapSizeValue = array();
foreach ($listSpecValue as $specValue) {

    $specName       = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
    $specValueData  = $mapSpecValueInfo[$specValue['spec_value_id']]['spec_value_data'];
    if ($specName == '规格重量') {

        $mapSpecValue[$specValue['goods_id']] = $specValueData;
    }
}

$spuCost    = array();
$mapSpuSalerCostByColor = array();
$sourceId   = array();
foreach ($groupSpuGoods as $spuId => $spuGoods) {

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
                
                $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            }
        }
    }
   
    if(!empty($sourceId[$spuId])){
     
        $sourceId[$spuId]      = implode(",",$sourceId[$spuId]);   
    }
    $mapSizeValue[$spuId]     = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
    $mapMaterialValue[$spuId] = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";

    foreach($mapColor as $spuIdKey => $colorInfo){

        foreach($colorInfo as $colorId => $cost){
            
            rsort($cost);
            $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
        }
    }
}

//获取颜色属性Id列表
$listSpecValueColotId   = array();

foreach($mapColorInfo as $spuId=>$colorCost){
    
    foreach($colorCost as $specColorId=>$cost){
        
        $listSpecValueColotId[$specColorId] = $specColorId;
    }
}
$countColor         = count($listSpecValueColotId);
$mapColorValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueColotId);
$mapSpecColorId     = ArrayUtility::indexByField($mapColorValueInfo,'spec_value_id', 'spec_value_data');

// 每个SPU下有哪些goodsId
$groupSpuGoodsId    = array();
foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

    $groupSpuGoodsId[$spuId] = ArrayUtility::listField($spuGoodsList, 'goods_id');
}

// 整合数据, 方便前台输出
foreach ($listSpuInfo as $key => $spuInfo) {

    // 品类名 && 规格重量
    $goodsId    = $mapSpuGoods[$spuInfo['spu_id']];
    
    if (!$goodsId) {

        $listSpuInfo[$key]['category_name'] = '';
        $listSpuInfo[$key]['weight_value']  = '';
    } else {

        $categoryId = $mapGoodsInfo[$goodsId]['category_id'];
        $listSpuInfo[$key]['category_name'] = $mapCategory[$categoryId]['category_name'];
        $listSpuInfo[$key]['material_name'] = !empty($mapMaterialValue[$spuInfo['spu_id']]) ? implode(",",$mapMaterialValue[$spuInfo['spu_id']]): '';
        $listSpuInfo[$key]['size_name']     = !empty($mapSizeValue[$spuInfo['spu_id']]) ? implode(",",$mapSizeValue[$spuInfo['spu_id']]): '';
        $listSpuInfo[$key]['weight_value']  = $mapSpecValue[$goodsId];
    }
 
    $listSpuInfo[$key]['color'] = array();
    $listSpuInfo[$key]['is_exist']  = 0; 
 
    if(is_numeric($_GET['customer_id']) && !empty($_GET['customer_id'] && !empty($salesQuotationMarkupRule))){

        foreach($mapSpecColorId as $colorId=>$colorName){
                
            if($indexOldCartColorId[$spuInfo['spu_id']]['color'][$colorId]){
                                
                $listSpuInfo[$key]['color'][$colorId] = isset($mapColorInfo[$spuInfo['spu_id']][$colorId]) ? $indexOldCartColorId[$spuInfo['spu_id']]['color'][$colorId] : "-";
                $listSpuInfo[$key]['spu_remark'] = $indexOldCartColorId[$spuInfo['spu_id']]['sales_quotation_remark'][$spuInfo['spu_id']];
                $listSpuInfo[$key]['is_exist']  = 1;
            } else {
            
                $listSpuInfo[$key]['color'][$colorId] = isset($mapColorInfo[$spuInfo['spu_id']][$colorId]) ? $mapColorInfo[$spuInfo['spu_id']][$colorId] + $salesQuotationMarkupRule : "-"; 
            }      
        }
    }else{
 
        foreach($mapSpecColorId as $colorId=>$colorName){
            
            if($indexCartColorId[$spuInfo['spu_id']]['color'][$colorId]){
                
                $listSpuInfo[$key]['color'][$colorId] = !empty($mapColorInfo[$spuInfo['spu_id']][$colorId]) ? $indexCartColorId[$spuInfo['spu_id']]['color'][$colorId] : "-";
                $listSpuInfo[$key]['spu_remark'] = $indexCartColorId[$spuInfo['spu_id']]['sales_quotation_remark'][$spuInfo['spu_id']];
            }else{
                $listSpuInfo[$key]['color'][$colorId] = "-";
            }
        }
    }
    $costNumber = array_unique($listSpuInfo[$key]['color']);
    foreach($costNumber as $colorId => $colorCost){
        
        if($colorCost == '-'){
            unset($costNumber[$colorId]);
        }
    }
    if(count($costNumber)>1){

        $unifiedCost  = '';
    }else{
        
        $unifiedCost  = end($costNumber);
    }
    
    $listSpuInfo[$key]['unified_cost']   = $unifiedCost;

    
    $sourceCodeList                     = ArrayUtility::listField($mapSpuSourceCode[$spuInfo['spu_id']], 'source_code');
    $listSpuInfo[$key]['source_id']     = implode(',', $sourceCodeList);
    $listSpuInfo[$key]['image_url']     = $mapSpuImages[$spuInfo['spu_id']]['image_url'];    
    $listSpuInfo[$key]['is_red']        = $indexSpuIdRed[$spuInfo['spu_id']];
    $listSpuInfo[$key]['is_cart_join']  = $indexSpuIdIsCartJoin[$spuInfo['spu_id']];
    $listSpuInfo[$key]['source_code_num']   = $indexSpuIdSource[$spuInfo['spu_id']];
}

foreach($listSpuInfo as $key => $info){
    
    $sortIsRed[$key]          = $info['is_red'];
    $sortSourceId[$key]       = $info['source_id'];
    $sortSourceCodeNum[$key]  = $info['source_code_num'];
    $sortCartJoinCodeNum[$key]= $info['is_cart_join'];
}
if(!empty($listSpuInfo)){
 
    array_multisort($sortCartJoinCodeNum,SORT_DESC,$sortIsRed,SORT_DESC,$sortSourceCodeNum,SORT_DESC,$sortSourceId,SORT_DESC,$listSpuInfo);   
}
$rowNum     = 0;
$sourceCode = '';
foreach($listSpuInfo as &$info){
    
    if($sourceCode != $info['source_id']){
     
        $sourceCode = $info['source_id'];
    
        $rowNum++;
    }
    $info['row_num']   = $rowNum;
}
$template       = Template::getInstance();
$template->assign('listCustomer', $listCustomer);
$template->assign('countSpu',$countSpu);
$template->assign('plusPrice',$salesQuotationMarkupRule);
$template->assign('listSpecValueId',$listSpecValueId);
$template->assign('pageViewData',$page->getViewData());
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('countColor',$countColor);
$template->assign('salesQuotationInfo',$salesQuotationInfo);
$template->assign('indexCustomerId',$indexCustomerId);
$template->assign('httpReferer',$_SERVER['HTTP_REFERER']);
$template->assign('mapSpecColorId',$mapSpecColorId);
$template->assign('mapSpecValueInfo',$mapSpecValueInfo);
$template->assign('salesQuotationId',$salesQuotationId);
$template->assign('listIndexColorName',$listIndexColorName);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/edit.tpl');