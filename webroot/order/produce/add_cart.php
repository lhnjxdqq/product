<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id'] || !$_GET['supplier_id']) {

    Utility::notice('缺少必要参数');
}

$salesOrderId           = (int) $_GET['sales_order_id'];
$supplierId             = (int) $_GET['supplier_id'];

// 清理生产订单购物车数据
Produce_Order_Cart::deleteBySalesOrderAndSupplier($salesOrderId, $supplierId);
$salesOrderInfo         = Sales_Order_Info::getById($salesOrderId);

// 销售订单中所有SKU
$listSalesOrderGoods    = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
$mapSalesOrderGoods     = ArrayUtility::indexByField($listSalesOrderGoods, 'goods_id');
$salesOrderGoodsIdList  = ArrayUtility::listField($listSalesOrderGoods, 'goods_id');
// 销售订单中已生产完成的SKU
$producedGoodsList      = Common_SalesOrder::getProducedGoods($salesOrderId);
$producedGoodsIdList    = array_unique(ArrayUtility::listField($producedGoodsList, 'goods_id'));
// 取未完成生产的SKU 查询当前供应商能生产的SKU
$listSalesOrderGoodsId  = array_diff($salesOrderGoodsIdList, $producedGoodsIdList);
$mapSupplierGoodsList   = Common_Goods::getSkuSupplier($listSalesOrderGoodsId);
$listSupplierGoods      = $mapSupplierGoodsList[$supplierId];
$listSupplierGoodsId    = ArrayUtility::listField($listSupplierGoods, 'goods_id');
$listProduceProduct     = Common_Product::getProduceOrderProductList($listSupplierGoodsId, $supplierId);
foreach ($listProduceProduct as $productInfo) {

    $goodsId    = $productInfo['goods_id'];
    $cartData   = array(
        'sales_order_id'        => $salesOrderId,
        'supplier_id'           => $supplierId,
        'product_id'            => $productInfo['product_id'],
        'quantity'              => $mapSalesOrderGoods[$goodsId]['goods_quantity'],
        'remark'                => $productInfo['product_remark'],
    );
    Produce_Order_Cart::create($cartData);
}

$redirect   = '/order/produce/confirm.php?sales_order_id=' . $salesOrderId . '&supplier_id=' . $supplierId;
Utility::redirect($redirect);