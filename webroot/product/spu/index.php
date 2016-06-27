<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition                  = $_GET;
$userId                     = (int) $_SESSION['user_id'];

$listCategoryInfo           = Category_Info::listAll();
$mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');

$listSupplierInfo           = Supplier_Info::listAll();
$mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$weightSpecInfo             = Spec_Info::getByAlias('weight');
$listWeightSpecValue        = Goods_Type_Spec_Value_Relationship::getBySpecId($weightSpecInfo['spec_id']);
$listWeightSpecValueId      = array_unique(ArrayUtility::listField($listWeightSpecValue, 'spec_value_id'));
$listWeightSpecValueInfo    = Spec_Value_Info::getByMulitId($listWeightSpecValueId);
$mapWeightSpecValueInfo     = ArrayUtility::indexByField($listWeightSpecValueInfo, 'spec_value_id');

$sizeSpecInfo               = Spec_Info::getByAlias('size');
$listSizeSpecValue          = Goods_Type_Spec_Value_Relationship::getBySpecId($sizeSpecInfo['spec_id']);
$listSizeSpecValueId        = array_unique(ArrayUtility::listField($listSizeSpecValue, 'spec_value_id'));
$listSizeSpecValueInfo      = Spec_Value_Info::getByMulitId($listSizeSpecValueId);
$mapSizeSpecValueInfo       = ArrayUtility::indexByField($listSizeSpecValueInfo, 'spec_value_id');

$colorSpecInfo              = Spec_Info::getByAlias('color');
$listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
$listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

$materialSpecInfo           = Spec_Info::getByAlias('material');
$listMaterialSpecValue      = Goods_Type_Spec_Value_Relationship::getBySpecId($materialSpecInfo['spec_id']);
$listMaterialSpecValueId    = array_unique(ArrayUtility::listField($listMaterialSpecValue, 'spec_value_id'));
$listMaterialSpecValueInfo  = Spec_Value_Info::getByMulitId($listMaterialSpecValueId);
$mapMaterialSpecValueInfo   = ArrayUtility::indexByField($listMaterialSpecValueInfo, 'spec_value_id');

$condition['delete_status'] = Spu_DeleteStatus::NORMAL;

$perpage                    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$page                       = new PageList(array(
    PageList::OPT_TOTAL     => Spu_List::countByCondition($condition),
    PageList::OPT_URL       => '/product/spu/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$configData                 = Config::get('product|PHP', 'spu');
$kRedSpecValueData          = $configData['spec_value_data'];
$kRedSpecValueInfo          = Spec_Value_Info::getBySpecValueData($kRedSpecValueData);
$kRedSpecValueId            = $kRedSpecValueInfo['spec_value_id'];

$listSpuInfo                = Spu_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
$listSpuId                  = ArrayUtility::listField($listSpuInfo, 'spu_id');
$listSpuImages              = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$mapSpuImages               = ArrayUtility::indexByField($listSpuImages, 'spu_id');

// 获取当前用户的购物车spu列表
$cartSpuInfo                = Cart_Spu_Info::getByUserId($userId);
$listCartSpuId              = ArrayUtility::listField($cartSpuInfo, 'spu_id');

foreach ($listSpuInfo as $key => $spuInfo) {

    $spuId              = $spuInfo['spu_id'];
    $listSpuGoodsInfo   = Spu_List::listSpuGoodsInfo($spuId);
    $kRedCost           = array();
    $listCost           = array();
    $listSpuInfo[$key]['is_cart']   = in_array($spuId, $listCartSpuId) ? 1 : 0;

    foreach ($listSpuGoodsInfo as $spuGoods) {

        $specValueId    = $spuGoods['spec_value_id'];
        $supplierId     = $spuGoods['supplier_id'];
        if ($specValueId == $kRedSpecValueId) {

            $kRedCost[$supplierId][]    = $spuGoods['sale_cost'];
        } else {

            $listCost[$supplierId][]    = $spuGoods['sale_cost'];
        }
    }

    foreach ($kRedCost as $supplierId => $supplierCostList) {

        rsort($supplierCostList);
        $kRedCost[$supplierId]  = current($supplierCostList);
    }

    foreach ($listCost as $supplierId => $supplierCostList) {

        rsort($supplierCostList);
        $listCost[$supplierId]  = current($supplierCostList);
    }

    $listSpuInfo[$key]['list_cost'] = $kRedCost ? $kRedCost : $listCost;
    $imageInfo                      = $mapSpuImages[$spuId];
    $listSpuInfo[$key]['image_url'] = $imageInfo
                                      ? AliyunOSS::getInstance('images-spu')->url($imageInfo['image_key'])
                                      : '';
}

$countCartSpu                       = Cart_Spu_Info::countByUser($userId);
$data['searchType']                 = Search_Spu::getSearchType();
$data['mainMenu']                   = Menu_Info::getMainMenu();
$data['mapCategoryInfo']            = $mapCategoryInfo;
$data['mapSupplierInfo']            = $mapSupplierInfo;
$data['mapWeightSpecValueInfo']     = $mapWeightSpecValueInfo;
$data['mapSizeSpecValueInfo']       = $mapSizeSpecValueInfo;
$data['mapColorSpecValueInfo']      = $mapColorSpecValueInfo;
$data['mapMaterialSpecValueInfo']   = $mapMaterialSpecValueInfo;
$data['listSpuInfo']                = $listSpuInfo;
$data['pageViewData']               = $page->getViewData();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('countCartSpu', $countCartSpu);
$template->display('product/spu/index.tpl');