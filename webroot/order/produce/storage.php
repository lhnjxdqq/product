<?php

require dirname(__FILE__).'/../../../init.inc.php';

$arriveId                   = $_GET['arrive_id'];
$auPrice                    = $_GET['au_price'];
Validate::testNull($arriveId,'到货表ID不能为空');
Validate::testNull($auPrice,'金价不能为空');
//到货单信息
$produceOrderArriveInfo     = Produce_Order_Arrive_Info::getById($arriveId);
Validate::testNull($produceOrderArriveInfo,'不存在的到货单');
$produceOrderId             = $produceOrderArriveInfo['produce_order_id'];
//到货单中的产品
$arriveProductInfo          = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($arriveId);
$indexProductId             = ArrayUtility::indexByField($arriveProductInfo,'product_id');

$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
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
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
// 分页
$page               = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_Product_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/storage.php',
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
    if(!empty($indexProductId[$productId])){
        
        $detail['is_arrive']        = 1;
        $detail['arrive_weight']    = $indexProductId[$productId]['weight'];
        $detail['storage_weight']   = $indexProductId[$productId]['storage_weight'];
        $detail['arrive_quantity']  = $indexProductId[$productId]['quantity'];
        $detail['storage_quantity'] = $indexProductId[$productId]['storage_quantity'];
    }else{
        $detail['is_arrive']        = 2;
    }
    $listIsArrive[] = $detail['is_arrive'];
}

foreach($listOrderDetail as $key=>$info){

    $isArrive[$key] = $info['is_arrive'];
}

array_multisort($isArrive,SORT_ASC,$listOrderDetail);
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
$template->assign('produceOrderArriveInfo', $produceOrderArriveInfo);
$template->display('order/produce/do_storage.tpl');