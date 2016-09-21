<?php

require  dirname(__FILE__).'/../../../init.inc.php';

$produceOrderId     = (int) $_GET['produce_order_id'];
Validate::testNull($produceOrderId,'生产订单Id不能为空');

//获取缺货产品
$listProduceOrderProduct    = Produce_Order_Product_Info::getShortByProduceOrderId($produceOrderId);

$indexProductId             = ArrayUtility::indexByField($listProduceOrderProduct,'product_id');
$listProductId              = ArrayUtility::listField($listProduceOrderProduct,'product_id');
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}
// 生产订单详情
$listOrderProduct   = Produce_Order_List::getDetailByMultiProduceOrderId((array) $produceOrderId);
$mapProductImage    = Common_Product::getProductThumbnail($listProductId);

$listGoodsId        = ArrayUtility::listField($listOrderProduct, 'goods_id');
$mapGoodsSpuList    = Common_Spu::getGoodsSpu($listGoodsId);
$listGoodsSpecValue = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
$mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
$produceOrderInfo['count_goods']    = count($listOrderProduct);
$produceOrderInfo['count_quantity'] = 0;
$produceOrderInfo['count_weight']   = 0;
foreach ($listOrderProduct as $orderProduct) {

    $goodsId            = $orderProduct['goods_id'];
    $quantity           = $orderProduct['quantity'];
    $weightValueData    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $produceOrderInfo['count_quantity'] += $quantity;
    $produceOrderInfo['count_weight']   += $quantity * $weightValueData;
}
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

$condition['produce_order_id']  = $produceOrderId;
$condition['delete_status']     = Produce_Order_DeleteStatus::NORMAL;
$condition['list_product_id']   = $listProductId;
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;

// 分页
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Product_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/arrive_detail.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listOrderDetail    = Produce_Order_Product_List::listByCondition($condition, array(), $page->getOffset(), $perpage);

foreach ($listOrderDetail as &$detail) {

    $goodsId        = $detail['goods_id'];
    $productId      = $detail['product_id'];
    $categoryId     = $detail['category_id'];
    $childStyleId   = $detail['style_id'];
    $parentStyleId  = $mapStyleInfo[$childStyleId]['parent_id'];
    $detail['category_name']        = $mapCategoryInfo[$categoryId]['category_name'];
    $detail['parent_style_name']    = $mapStyleInfo[$parentStyleId]['style_name'];
    $detail['child_style_name']     = $mapStyleInfo[$childStyleId]['style_name'];
    $detail['weight_value_data']    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $detail['size_value_data']      = $mapGoodsSpecValue[$goodsId]['size_value_data'];
    $detail['color_value_data']     = $mapGoodsSpecValue[$goodsId]['color_value_data'];
    $detail['material_value_data']  = $mapGoodsSpecValue[$goodsId]['material_value_data'];
    $detail['spu_list']             = $mapGoodsSpuList[$goodsId];
    $detail['image_url']            = $mapProductImage[$productId]['image_url'];
    
    $detail['order_weight']         = $mapGoodsSpecValue[$goodsId]['weight_value_data'] * $indexProductId[$productId]['quantity'];
    $detail['order_quantity']       = $indexProductId[$productId]['quantity'];
    $detail['short_quantity']       = $indexProductId[$productId]['short_quantity'];
    $detail['short_weight']         = $indexProductId[$productId]['short_weight'];
    $detail['storage_weight']       = $detail['order_weight'] - $indexProductId[$productId]['short_weight'];
    $detail['storage_quantity']     = $indexProductId[$productId]['quantity'] - $indexProductId[$productId]['short_quantity'];
    if(!empty($indexProductId[$productId]['quantity'])){

        $detail['short_ratio']          = sprintf('%.4f',($indexProductId[$productId]['short_quantity'])/($indexProductId[$productId]['quantity']));
        
    }
}

$data['produceOrderInfo']   = $produceOrderInfo;
$data['supplierInfo']       = $supplierInfo;
$data['customerInfo']       = $customerInfo;
$data['mapUserInfo']        = $mapUserInfo;
$data['listOrderDetail']    = $listOrderDetail;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['mapOrderType']       = Produce_Order_Type::getOrderType();
$data['listOrderType']      = array(
    'new_built'     => Produce_Order_StatusCode::NEWLY_BUILT,   // 新建
    'confirmed'     => Produce_Order_StatusCode::CONFIRMED,     // 已确认
    'stocking'      => Produce_Order_StatusCode::STOCKING,      // 采购中
);
$userInfo           = ArrayUtility::indexByField(User_Info::listAll(),'user_id');

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('userInfo', $userInfo);
$template->display('order/produce/shortages_list.tpl');