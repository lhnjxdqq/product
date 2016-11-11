<?php

require dirname(__FILE__).'/../../../../init.inc.php';

$suppliesId = $_GET['supplies_id'];

$suppliesInfo   = Sales_Supplies_Info::getById($suppliesId);

//出货单中的产品
$salesSuppliesProductInfo   = Sales_Supplies_Product_Info::getBySuppliesId($suppliesId);
$indexProductId             = ArrayUtility::indexByField($salesSuppliesProductInfo,'product_id');
$listProductId              = ArrayUtility::listField($salesSuppliesProductInfo,'product_id');
$productInfo                = Product_Info::getByMultiId($listProductId);
$indexProductId             = ArrayUtility::indexByField($productInfo,'product_id');
$listGoodsId                = ArrayUtility::listField($productInfo, 'goods_id');
$indexGoodsId               = ArrayUtility::indexByField(Goods_Info::getByMultiId($listGoodsId),'goods_id');
$listSourceId               = ArrayUtility::listField($productInfo,'source_id');
$indexSourceId              = ArrayUtility::indexByField(Source_Info::getByMultiId($listSourceId),'source_id');

$suppliesProductInfo['total_quantity']      = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_quantity'));
$suppliesProductInfo['count_style']         = count($salesSuppliesProductInfo);
$suppliesProductInfo['total_weight']        = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_weight'));
$suppliesProductInfo['supplies_id']         = $suppliesId;

$mapProductImage    = Common_Product::getProductThumbnail($listProductId);

$mapGoodsSpuList    = Common_Spu::getGoodsSpu($listGoodsId);
$listGoodsSpecValue = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
$mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');

// 供应商信息
$supplierId         = $produceOrderInfo['supplier_id'];
$supplierInfo       = Supplier_Info::getById($supplierId);
// 销售订单信息
$salesOrderId       = $produceOrderInfo['sales_order_id'];
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
// 客户信息
$customerId         = $salesOrderInfo['customer_id'];
$customerInfo       = Customer_Info::getById($customerId);
// 用户信息
$listUserInfo       = User_Info::listAll();
$mapUserInfo        = ArrayUtility::indexByField($listUserInfo, 'user_id', 'username');
// 分类信息
$listCategoryInfo   = Category_Info::listAll();
$mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
// 款式信息
$listStyleInfo      = Style_Info::listAll();
$mapStyleInfo       = ArrayUtility::indexByField($listStyleInfo, 'style_id');

$condition['supplies_id']       = $suppliesId;
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;

// 分页
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Sales_Supplies_Product_Info::countByCondition($condition),
    PageList::OPT_URL       => '/order/sales/product_list.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSuppliesInfo   = Sales_Supplies_Product_Info::listByCondition($condition, array(), $page->getOffset(), $perpage);

foreach ($listSuppliesInfo as &$detail) {

    $goodsId        = $indexProductId[$detail['product_id']]['goods_id'];
    $productId      = $detail['product_id'];
    $categoryId     = $indexGoodsId[$goodsId]['category_id'];
    $childStyleId   = $indexGoodsId[$goodsId]['style_id'];
    $parentStyleId  = $mapStyleInfo[$childStyleId]['parent_id'];
    $detail['category_name']        = $mapCategoryInfo[$categoryId]['category_name'];
    $detail['parent_style_name']    = $mapStyleInfo[$parentStyleId]['style_name'];
    $detail['child_style_name']     = $mapStyleInfo[$childStyleId]['style_name'];
    $detail['weight_value_data']    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $detail['size_value_data']      = $mapGoodsSpecValue[$goodsId]['size_value_data'];
    $detail['color_value_data']     = $mapGoodsSpecValue[$goodsId]['color_value_data'];
    $detail['material_value_data']  = $mapGoodsSpecValue[$goodsId]['material_value_data'];
    $detail['product_sn']           = $indexProductId[$productId]['product_sn'];
    $detail['source_code']          = $indexSourceId[$indexProductId[$productId]['source_id']]['source_code'];
    $detail['goods_sn']             = $indexGoodsId[$goodsId]['goods_sn'];
    $detail['spu_list']             = $mapGoodsSpuList[$goodsId];
    $detail['image_url']            = $mapProductImage[$productId]['image_url'];
}

$wayInfo            = Sales_Supplies_WayStyle::getSuppliesWay();
$data['listSuppliesInfo']    = $listSuppliesInfo;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['mapOrderType']       = Produce_Order_Type::getOrderType();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('suppliesInfo', $suppliesInfo);
$template->assign('wayInfo', $wayInfo);
$template->assign('suppliesProductInfo', $suppliesProductInfo);
$template->display('order/sales/supplies/detail.tpl');