<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$getData    = $_GET;
$customerId = '';
$indexCartColorId   = array();
if(is_numeric($_GET['customer_id']) && !empty($_GET['customer_id'])){
 
    $order      = array();   
    $customerId = $_GET['customer_id'];
    $salesCondition = array(
        'customer_id'   => $customerId,
        'is_confirm'    => Sales_Quotation_ConfirmStatus::YES,
    );
    $mapCartSpuInfo    = Sales_Quotation_Info::listByCondition($salesCondition, $order, 0, 100);
    if(!empty($mapCartSpuInfo)){
        
        //该用户下所有销售出货单的记录ID
        $salesQuotationInfo         = ArrayUtility::listField($mapCartSpuInfo, 'sales_quotation_id');
        $mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId($salesQuotationInfo);
        $spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');
        foreach($spuInfo as $spuId=>$info){
            
            $indexCartColorId[$spuId]['color'] = ArrayUtility::indexByField($info, 'color_id', 'cost');
            $indexCartColorId[$spuId]['sales_quotation_remark'] = ArrayUtility::indexByField($info, 'spu_id', 'sales_quotation_remark');
        }
    }
}

$userId          = $_SESSION['user_id'];
$listCustomer    = Customer_Info::listAll();

$orderBy                = array();
$perpage                = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$condition['user_id']   = $userId;
$countSpu   = Cart_Spu_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSpu,
    PageList::OPT_URL       => '/sales_quotation/create.php',
    PageList::OPT_PERPAGE   => $perpage,
));
$listCartInfo    = Cart_Spu_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

//获取sqlID的组合
$listSpuId       = ArrayUtility::listField($listCartInfo,"spu_id");

$mapSpuRemark    = ArrayUtility::indexByField($listCartInfo,'spu_id','remark');    

foreach($listCartInfo as $key=>$info){
    
    $costInfo   = json_decode($info['spu_color_cost_data'],true);
    $mapSpuColorCost[$info['spu_id']] = $costInfo;

}

$listSpuInfo     = Spu_Info::getByMultiId($listSpuId);
//获取SPU数量
$listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$mapSpuImages   = ArrayUtility::indexByField($listSpuImages, 'spu_id');
foreach ($mapSpuImages as $spuId => $spuImage) {

    $mapSpuImages[$spuId]['image_url']  = AliyunOSS::getInstance('thumb-images-spu')->url($spuImage['image_key']);
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
        }
    }

    $mapSizeValue[$spuId]     = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
    $mapMaterialValue[$spuId] = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";

}

//获取颜色属性Id列表
$listSpecValueColotId   = array();

foreach($mapSpuColorCost as $spuId=>$colorCost){
    
    if(empty($colorCost)){
        
        continue;
    }
    foreach($colorCost as $specColorId=>$cost){
        
        $listSpecValueColotId[$specColorId] = $specColorId;
    }
}
$countColor         = count($listSpecValueColotId);
$mapColorValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueColotId);
$mapSpecColorId     = ArrayUtility::indexByField($mapColorValueInfo,'spec_value_id', 'spec_value_data');

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
    
    $costNumber = array_unique($mapSpuColorCost[$spuInfo['spu_id']]);
        
    if(count($costNumber)>1){
        
            $unifiedCost  = '';
        }else{
            
            $unifiedCost  = end($costNumber);
    }
    $listSpuInfo[$key]['unified_cost']   = $unifiedCost;
    foreach($mapSpecColorId as $colorId=>$colorName){
        
        if($indexCartColorId[$spuInfo['spu_id']]['color'][$colorId]){
            
            $listSpuInfo[$key]['color'][$colorId] = !empty($mapSpuColorCost[$spuInfo['spu_id']][$colorId]) ? $indexCartColorId[$spuInfo['spu_id']]['color'][$colorId] : "-";
            $listSpuInfo[$key]['spu_remark'] = $indexCartColorId[$spuInfo['spu_id']]['sales_quotation_remark'][$spuInfo['spu_id']];
            $listSpuInfo[$key]['is_exist']  = 1;
        } else {
        
            $listSpuInfo[$key]['color'][$colorId] = !empty($mapSpuColorCost[$spuInfo['spu_id']][$colorId]) ? $mapSpuColorCost[$spuInfo['spu_id']][$colorId] : "-"; 
            $listSpuInfo[$key]['spu_remark']      = $mapSpuRemark[$spuInfo['spu_id']];
        }
        $listSpuInfo[$key]['image_url'] = $mapSpuImages[$spuInfo['spu_id']]['image_url'];
                
    }
}
$template       = Template::getInstance();

$template->assign('listCustomer', $listCustomer);
$template->assign('countSpu',$countSpu);
$template->assign('getData',$getData);
$template->assign('pageViewData',$page->getViewData());
$template->assign('customerId',$customerId);
$template->assign('listSpecValueId',$listSpecValueId);
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('countColor',$countColor);
$template->assign('mapSpecColorId',$mapSpecColorId);
$template->assign('mapSpecValueInfo',$mapSpecValueInfo);
$template->assign('listIndexColorName',$listIndexColorName);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/create.tpl');