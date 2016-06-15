<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$multiGoodsId   = isset($_GET['multi_goods_id']) ? explode(',', $_GET['multi_goods_id']) : array();
if (!$multiGoodsId) {

    Utility::notice('请先选择SKU');
}

// 验证所选SKU的分类和规格重量是否相同
$listSpecInfo           = Spec_Info::listAll();
$mapSpecInfo            = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo      = Spec_Value_Info::listAll();
$mapSpecValueInfo       = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

$listGoodsInfo          = Goods_Info::getByMultiId($multiGoodsId);
$mapGoodsInfo           = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($multiGoodsId);
$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');

$mapGoodsWeigth         = array();
$weightValueId          = 0;
foreach ($groupGoodsSpecValue as $goodsId => $specValueList) {

    foreach ($specValueList as $specValue) {

        $specId         = $specValue['spec_id'];
        $specValueId    = $specValue['spec_value_id'];
        $specValueData  = $mapSpecValueInfo[$specValueId]['spec_value_data'];
        $specUnit       = $mapSpecInfo[$specId]['spec_unit'];
        switch ($mapSpecInfo[$specId]['spec_name']) {
            case '主料材质' :
                $mapGoodsInfo[$goodsId]['material'] = $specValueData . $specUnit;
            break;
            case '规格重量' :
                $mapGoodsInfo[$goodsId]['weight'] = $specValueData . $specUnit;
            break;
            case '规格尺寸' :
                $mapGoodsInfo[$goodsId]['size'] = $specValueData . $specUnit;
            break;
            case '颜色' :
                $mapGoodsInfo[$goodsId]['color'] = $specValueData . $specUnit;
            break;
        }

        if ($mapSpecInfo[$specId]['spec_name'] == '规格重量') {

            $mapGoodsWeigth[$goodsId]   = $specValueId;
            $weightValueId              = $specValueId;
        }
    }
}

$spuParams  = array();
foreach ($mapGoodsInfo as $goodsId => $goodsInfo) {

    $categoryId             = $goodsInfo['category_id'];
    $spuParams[$goodsId]    = (string) $categoryId . (string) $mapGoodsWeigth[$goodsId];
}

if (count(array_unique($spuParams)) != 1) {

    Utility::notice('所选SPU三级分类和规格重量不同, 无法创建SPU');
}

$listCategoryInfo   = Category_Info::listAll();
$mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
foreach ($mapGoodsInfo as &$goodsInfo) {

    $goodsInfo['category_name'] = $mapCategoryInfo[$goodsInfo['category_id']]['category_name'];
}

$categoryId         = current($listGoodsInfo)['category_id'];
$categoryInfo       = $mapCategoryInfo[$categoryId];
$spuSn              = Spu_Info::createSpuSn($categoryInfo['category_sn']);

$listGoodsImages    = Goods_Images_RelationShip::getByMultiGoodsId($multiGoodsId);
$mapGoodsImages     = ArrayUtility::indexByField($listGoodsImages, 'goods_id');
foreach ($mapGoodsImages as $goodsId => &$goodsImage) {

    $imageKey                   = $goodsImage['image_key'];
    $goodsImage['image_url']    = AliyunOSS::getInstance('images-sku')->url($imageKey);
}

$data['spuSn']          = $spuSn;
$data['weightValueId']  = $weightValueId;
$data['mapGoodsInfo']   = $mapGoodsInfo;
$data['mapGoodsImages'] = $mapGoodsImages;
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/spu/add.tpl');