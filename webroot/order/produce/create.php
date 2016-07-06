<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id'] || !$_GET['supplier_id']) {

    Utility::notice('缺少必要参数');
}

$salesOrderId           = (int) $_GET['sales_order_id'];
$supplierId             = (int) $_GET['supplier_id'];

$salesOrderInfo         = Sales_Order_Info::getById($salesOrderId);
$supplierInfo           = Supplier_Info::getById($supplierId);

$listCategoryInfo       = Category_Info::listAll();
$mapCategoryInfo        = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
$listStyleInfo          = Style_Info::listAll();
$mapStyleInfo           = ArrayUtility::indexByField($listStyleInfo, 'style_id');

// 销售订单中所有SKU
$listSalesOrderGoods    = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
$salesOrderGoodsIdList  = ArrayUtility::listField($listSalesOrderGoods, 'goods_id');
// 销售订单中已生产完成的SKU
$producedGoodsList      = Common_SalesOrder::getProducedGoods($salesOrderId);
$producedGoodsIdList    = array_unique(ArrayUtility::listField($producedGoodsList, 'goods_id'));
// 取未完成生产的SKU 查询当前供应商能生产的SKU
$listSalesOrderGoodsId  = array_diff($salesOrderGoodsIdList, $producedGoodsIdList);
$mapSupplierGoodsList   = Common_Goods::getSkuSupplier($listSalesOrderGoodsId);
$listSupplierGoods      = $mapSupplierGoodsList[$supplierId];
$listSupplierGoodsId    = ArrayUtility::listField($listSupplierGoods, 'goods_id');
// SKU规格 规格值
$listGoodsSpecValue     = Common_Goods::getMultiGoodsSpecValue($listSupplierGoodsId);
$mapGoodsSpecValue      = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
// SKU-SPU
$mapGoodsSpuList        = Common_Spu::getGoodsSpu($listSupplierGoodsId);
// SKU缩略图
$mapGoodsThumb          = Common_Goods::getGoodsThumbnail($listSupplierGoodsId);

$condition              = array(
    'sales_order_id'    => $salesOrderId,
    'supplier_id'       => $supplierId,
);

// 分页
$perpage                = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$page                   = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Cart::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/create.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listProduceProduct     = Produce_Order_Cart::listByCondition($condition, array(), $page->getOffset(), $perpage);

foreach ($listProduceProduct as &$productInfo) {

    $goodsId        = $productInfo['goods_id'];
    $categoryId     = $productInfo['category_id'];
    $childStyleId   = $productInfo['style_id'];
    $parentStyleId  = $childStyleId ? $mapStyleInfo[$childStyleId]['parent_id'] : 0;
    $productInfo['category_name']       = $mapCategoryInfo[$categoryId]['category_name'];
    $productInfo['parent_style_name']   = $mapStyleInfo[$parentStyleId]['style_name'];
    $productInfo['child_style_name']    = $mapStyleInfo[$childStyleId]['style_name'];
    $productInfo['material_value_data'] = $mapGoodsSpecValue[$goodsId]['material_value_data'];
    $productInfo['size_value_data']     = $mapGoodsSpecValue[$goodsId]['size_value_data'];
    $productInfo['color_value_data']    = $mapGoodsSpecValue[$goodsId]['color_value_data'];
    $productInfo['weight_value_data']   = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $productInfo['image_url']           = $mapGoodsThumb[$goodsId]['image_url'];
    $productInfo['spu_list']            = $mapGoodsSpuList[$goodsId];
}

$data['supplierInfo']       = $supplierInfo;
$data['listProduceProduct'] = $listProduceProduct;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/create.tpl');