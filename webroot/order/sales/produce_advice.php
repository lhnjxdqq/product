<?php
/**
 * 生产建议页面
 */
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id']) {

    Utility::notice('sales_order_id is missing');
}

$salesOrderId       = (int) $_GET['sales_order_id'];
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);

if (!$salesOrderInfo || $salesOrderInfo['sales_order_status'] == Sales_Order_Status::DELETE) {

    Utility::notice('销售订单不存在或已被删除');
}
// 查询该销售订单内的SKU, 查询这些SKU分别由哪些供应商生产
$listOrderGoods     = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
$mapOrderGoods      = ArrayUtility::indexByField($listOrderGoods, 'goods_id');
$listGoodsId        = ArrayUtility::listField($listOrderGoods, 'goods_id');
$listSupplierGoods  = Common_Product::getSkuSupplier($listGoodsId);
$listSupplierInfo   = array();
foreach ($listSupplierGoods as $supplierId => $supplierGoodsList) {

    $quantity_total = $weight_total = 0;
    foreach ($supplierGoodsList as $goods) {

        $goodsId    = $goods['goods_id'];
        $quantity   = $mapOrderGoods[$goodsId]['goods_number'];
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