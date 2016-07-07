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

$listSupplierCart       = Produce_Order_Cart::getSupplierGoodsDetail($salesOrderId, $supplierId);
$listSupplierGoodsId    = ArrayUtility::listField($listSupplierCart, 'goods_id');
// SKU规格 规格值
$listGoodsSpecValue     = Common_Goods::getMultiGoodsSpecValue($listSupplierGoodsId);
$mapGoodsSpecValue      = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
// SKU-SPU
$mapGoodsSpuList        = Common_Spu::getGoodsSpu($listSupplierGoodsId);
// SKU缩略图
$mapGoodsThumb          = Common_Goods::getGoodsThumbnail($listSupplierGoodsId);
// 统计款数 件数 重量
$listSupplierCart       = Produce_Order_Cart::getSupplierGoodsDetail($salesOrderId, $supplierId);
$countSupplierCart      = array(
    'count_goods'       => count($listSupplierCart),
    'count_quantity'    => 0,
    'count_weight'      => 0,
);
foreach ($listSupplierCart as $cartGoods) {

    $goodId             = $cartGoods['goods_id'];
    $quantity           = $cartGoods['quantity'];
    $weightValueData    = $mapGoodsSpecValue[$goodId]['weight_value_data'];
    $countSupplierCart['count_quantity']    += $quantity;
    $countSupplierCart['count_weight']      += $quantity * $weightValueData;
}

$condition              = array(
    'sales_order_id'    => $salesOrderId,
    'supplier_id'       => $supplierId,
);

// 分页
$perpage                = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$page                   = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Cart::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/confirm.php',
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
$data['countSupplierCart']  = $countSupplierCart;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/confirm.tpl');