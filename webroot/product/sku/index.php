<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition              = $_GET;
$listCategoryInfo       = Category_Info::listAll();
$listCategoryInfoLv3    = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
$mapCategoryInfoLv3     = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

// 排序
$sortBy     = isset($_GET['sortby']) ? $_GET['sortby'] : 'goods_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortBy => $direction,
);

// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countProduct   = Goods_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countProduct,
    PageList::OPT_URL       => '/product/sku/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$condition['delete_status'] = Goods_DeleteStatus::NORMAL;

$listGoodsInfo      = Goods_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$listGoodsId        = ArrayUtility::listField($listGoodsInfo, 'goods_id');

$listGoodsSpecValue = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$listSpecInfo       = Spec_Info::getByMulitId(ArrayUtility::listField($listGoodsSpecValue, 'spec_id'));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo  = Spec_Value_Info::getByMulitId(ArrayUtility::listField($listGoodsSpecValue, 'spec_value_id'));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
foreach ($listGoodsSpecValue as &$specValue) {

    $specValue['spec_name']         = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
    $specValue['spec_value_data']   = $mapSpecValueInfo[$specValue['spec_value_id']]['spec_value_data'];
    $specValue['spec_unit']         = $mapSpecInfo[$specValue['spec_id']]['spec_unit'];
}
$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');
$mapGoodsSpecValue      = array();
foreach ($groupGoodsSpecValue as $goodsId => $specValueList) {

    $specValueList                  = ArrayUtility::indexByField($specValueList, 'spec_name');
    $mapGoodsSpecValue[$goodsId]    = $specValueList;
}

$data['mapCategoryInfoLv3'] = $mapCategoryInfoLv3;
$data['listGoodsInfo']      = $listGoodsInfo;
$data['mapGoodsSpecValue']  = $mapGoodsSpecValue;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/sku/index.tpl');