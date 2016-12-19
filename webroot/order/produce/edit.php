<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['produce_order_id']) {

    Utility::notice('produce_order_id is missing');
}

$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);

if (!$produceOrderInfo || $produceOrderInfo['delete_status'] == Produce_Order_DeleteStatus::DELETED) {

    Utility::notice('订单不存在或已删除');
}

$condition['produce_order_id']  = $produceOrderId;
$condition['delete_status']     = Produce_Order_Product_DeleteStatus::NORMAL;

// 分页
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Product_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/edit.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listOrderProduct       = Produce_Order_Product_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
// 三级分类
$listOrderCategoryId    = array_unique(ArrayUtility::listField($listOrderProduct, 'category_id'));
$listCategoryInfo       = Category_Info::getByMultiId($listOrderCategoryId);
$mapCategoryInfo        = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
// 规格 规格值
$listOrderGoodsId       = array_unique(ArrayUtility::listField($listOrderProduct, 'goods_id'));
$listGoodsSpecValue     = Common_Goods::getMultiGoodsSpecValue($listOrderGoodsId);
$mapGoodsSpecValue      = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
// 款式
$listStyleInfo          = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
$mapStyleInfo           = ArrayUtility::indexByField($listStyleInfo, 'style_id');
// 产品对应的SPU
$mapGoodsSpuList        = Common_Spu::getGoodsSpu($listOrderGoodsId);
// 产品图片
$listOrderProductId     = ArrayUtility::listField($listOrderProduct, 'product_id');
$mapProductThumbnail    = Common_Product::getProductThumbnail($listOrderProductId);

foreach ($listOrderProduct as &$orderProduct) {

    $categoryId     = $orderProduct['category_id'];
    $goodsId        = $orderProduct['goods_id'];
    $productId      = $orderProduct['product_id'];
    $childStyleId   = $orderProduct['style_id'];
    $parentStyleId  = $mapStyleInfo[$childStyleId]['parent_id'];
    $orderProduct['category_name']          = $mapCategoryInfo[$categoryId]['category_name'];
    $orderProduct['parent_style_name']      = $mapStyleInfo[$parentStyleId]['style_name'];
    $orderProduct['child_style_name']       = $mapStyleInfo[$childStyleId]['style_name'];
    $orderProduct['weight_value_data']      = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $orderProduct['size_value_data']        = $mapGoodsSpecValue[$goodsId]['size_value_data'];
    $orderProduct['color_value_data']       = $mapGoodsSpecValue[$goodsId]['color_value_data'];
    $orderProduct['material_value_data']    = $mapGoodsSpecValue[$goodsId]['material_value_data'];
    $orderProduct['spu_list']               = $mapGoodsSpuList[$goodsId];
    $orderProduct['image_url']              = $mapProductThumbnail[$productId]['image_url'];
}

$data['produceOrderInfo']   = $produceOrderInfo;
$data['listOrderProduct']   = $listOrderProduct;
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['pageViewData']       = $page->getViewData();
$data['mapOrderType']       = Produce_Order_Type::getOrderType();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/edit.tpl');