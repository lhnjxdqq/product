<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['produce_order_id'])) {

    Utility::notice('produce_order_id is missing');
}

// 生产订单
$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}

if (isset($_GET['is_ok']) && trim($_GET['is_ok']) == 'ok') {

    $batchCode  = trim($_GET['batch_code']);
    if (!$batchCode) {

        Utility::notice('请输入工厂批次号');
    }
    $updateBatchCode    = Produce_Order_Info::update(array(
        'produce_order_id'  => $produceOrderId,
        'batch_code'        => $batchCode,
    ));
    $changeStatus       = Produce_Order_Info::changeStatus($produceOrderId, Produce_Order_StatusCode::STOCKING);
    if ($updateBatchCode && $changeStatus) {

        Utility::notice('确认成功','/order/produce/index.php');
    } else {

        Utility::notice('确认失败');
    }
}

// 生产订单详情
$listOrderProduct   = Produce_Order_List::getDetailByMultiProduceOrderId((array) $produceOrderId);
$listProductId      = ArrayUtility::listField($listOrderProduct, 'product_id');
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
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
// 分页
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Product_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/detail.php',
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

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/order_confirm.tpl');