<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id']) {

    Utility::notice('sales_order_id is missing');
}

$salesOrderId       = (int) $_GET['sales_order_id'];
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);

if (!$salesOrderInfo || $salesOrderInfo['sales_order_status'] == Sales_Order_Status::DELETE) {

    Utility::notice('销售订单不存在或已被删除');
}

// 查询该销售订单的生产订单
$listOrderProduct   = Common_SalesOrder::getProduceOrderDetail($salesOrderId);
// 已生产次数
$mapProduceOrder    = ArrayUtility::groupByField($listOrderProduct, 'produce_order_id');
$salesOrderInfo['produce_order_count']  = count($mapProduceOrder);
// 已生产款数
$groupProduceGoods  = ArrayUtility::groupByField($listOrderProduct, 'goods_id');
$salesOrderInfo['produce_goods_count']  = count($groupProduceGoods);
// 统计每个SKU已生产数量, 再根据规格重量统计SKU累计重量
$listProducedGoods  = array();
foreach ($groupProduceGoods as $goodsId => $produceGoodsList) {
    $listProducedGoods[$goodsId]    = array_sum(ArrayUtility::listField($produceGoodsList, 'quantity'));
}
$producedGoodsId    = array_keys($listProducedGoods);
$goodsSpecValueList = Common_Goods::getMultiGoodsSpecValue($producedGoodsId);
$mapGoodsWeightData = ArrayUtility::indexByField($goodsSpecValueList, 'goods_id', 'weight_value_data');
foreach ($listProducedGoods as $goodsId => $quantity) {

    $weightValueData    = $mapGoodsWeightData[$goodsId];
    $salesOrderInfo['produce_weight_count'] += $quantity * $weightValueData;
}
// 已生产数量
foreach ($listOrderProduct as $orderProduct) {

    $salesOrderInfo['produce_quantity_total']   += $orderProduct['quantity'];
}
// 相关订单
foreach ($mapProduceOrder as $produceProductList) {

    $produceOrder = current($produceProductList);
    $salesOrderInfo['produce_order_list'][] = array(
        'produce_order_id'  => $produceOrder['produce_order_id'],
        'produce_order_sn'  => $produceOrder['produce_order_sn'],
    );
}

// 查询该销售订单内的SKU, 查询这些SKU分别由哪些供应商生产
$listOrderGoods     = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
$mapOrderGoods      = ArrayUtility::indexByField($listOrderGoods, 'goods_id');
$listGoodsId        = ArrayUtility::listField($listOrderGoods, 'goods_id');

$producedGoodsList  = Common_SalesOrder::getProducedGoods($salesOrderId);
$producedGoodsList  = ArrayUtility::listField($producedGoodsList, 'goods_id');

$filterGoodsIdList  = array_diff($listGoodsId, $producedGoodsList);
$listSupplierGoods  = Common_Goods::getSkuSupplier($filterGoodsIdList);

$listSupplierInfo   = array();
foreach ($listSupplierGoods as $supplierId => $supplierGoodsList) {

    $quantity_total = $weight_total = 0;
    foreach ($supplierGoodsList as $goods) {

        $goodsId    = $goods['goods_id'];
        $quantity   = $mapOrderGoods[$goodsId]['goods_quantity'];
        $weight     = $mapOrderGoods[$goodsId]['reference_weight'];
        $quantity_total += $quantity;
        $weight_total   += $weight;
    }
    $supplier       = current($supplierGoodsList);
    $listSupplierInfo[$supplierId]  = array(
        'supplier_id'       => $supplierId,
        'supplier_code'     => $supplier['supplier_code'],
        'count_goods'       => count($supplierGoodsList),
        'quantity_total'    => $quantity_total,
        'weight_total'      => $weight_total,
    );
}

$data['salesOrderInfo']     = $salesOrderInfo;
$data['listSupplierInfo']   = $listSupplierInfo;
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/sales/produce_advice.tpl');