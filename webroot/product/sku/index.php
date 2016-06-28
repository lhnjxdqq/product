<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition              = $_GET;
$condition['delete_status'] = Goods_DeleteStatus::NORMAL;

$page                   = new PageList(array(
    PageList::OPT_TOTAL     => Goods_List::countByCondition($condition),
    PageList::OPT_URL       => '/product/sku/index.php',
    PageList::OPT_PERPAGE   => 20,
));

$listCategoryInfo       = Category_Info::listAll();
$mapCategoryInfo        = ArrayUtility::indexByField($listCategoryInfo, 'category_id');

$listGoodsInfo          = Goods_List::listByCondition($condition, array(), $page->getOffset(), 20);
$listGoodsId            = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsImages        = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
$mapGoodsImages         = ArrayUtility::indexByField($listGoodsImages, 'goods_id');

$listMaterialValueId    = ArrayUtility::listField($listGoodsInfo, 'material_value_id');
$listSizeValueId        = ArrayUtility::listField($listGoodsInfo, 'size_value_id');
$listColorValueId       = ArrayUtility::listField($listGoodsInfo, 'color_value_id');
$listWeightValueId      = ArrayUtility::listField($listGoodsInfo, 'weight_value_id');
$listSpecValueId        = array_unique(array_merge(
    $listMaterialValueId,
    $listSizeValueId,
    $listColorValueId,
    $listWeightValueId
));
$listSpecValueInfo      = Spec_Value_Info::getByMulitId($listSpecValueId);
$mapSpecValueInfo       = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

foreach ($listGoodsInfo as &$goodsInfo) {

    $goodsId    = $goodsInfo['goods_id'];
    $imageKey   = $mapGoodsImages[$goodsId]['image_key'];
    $goodsInfo['image_url']     = $imageKey
        ? AliyunOSS::getInstance('images-sku')->url($imageKey)
        : '';
}

$data['mapCategoryInfo']    = $mapCategoryInfo;
$data['mapSpecValueInfo']   = $mapSpecValueInfo;
$data['listGoodsInfo']      = $listGoodsInfo;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/sku/index.tpl');