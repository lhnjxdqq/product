<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    Utility::notice('method error');
}

if (!$_POST['order-type']) {

    Utility::notice('请选择订单类型');
}

$salesOrderId       = (int) $_POST['sales-order-id'];
$supplierId         = (int) $_POST['supplier-id'];
$produceOrderCart   = Produce_Order_Cart::getBySalesOrderAndSupplier($salesOrderId, $supplierId);
$listProductId      = ArrayUtility::listField($produceOrderCart,'product_id');
$productInfo        = Product_Info::getByMultiId($listProductId);
$indexProductId     = ArrayUtility::indexByField($productInfo,'product_id','goods_id');
$listGoodsId        = ArrayUtility::listField($productInfo,'goods_id');
// SKU规格 规格值
$listGoodsSpecValue     = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
$mapGoodsSpecValue      = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');

if (!$produceOrderCart) {

    Utility::notice('数据出错');
}

$orderData  = array(
    'produce_order_sn'      => Produce_Order_Info::createOrderSn(),
    'produce_order_remark'  => trim($_POST['order-remark']),
    'sales_order_id'        => $salesOrderId,
    'supplier_id'           => $supplierId,
    'prepaid_amount'        => sprintf('%.2f', trim($_POST['prepaid-amount'])),
    'arrival_date'          => date('Y-m-d H:i:s', strtotime(trim($_POST['arrival-date']))),
    'order_type'            => (int) $_POST['order-type'],
    'create_user'           => $_SESSION['user_id'],
    'status_code'           => Produce_Order_StatusCode::NEWLY_BUILT,
);

$produceOrderId = Produce_Order_Info::create($orderData);
if ($produceOrderId) {

    foreach ($produceOrderCart as $item) {

        $data   = array(
            'produce_order_id'  => $produceOrderId,
            'product_id'        => $item['product_id'],
            'quantity'          => $item['quantity'],
            'remark'            => $item['remark'],
            'short_quantity'    => $item['quantity'],
            'short_weight'      => sprintf('%.2f',$item['quantity'] * $mapGoodsSpecValue[$indexProductId[$item['product_id']]]['weight_value_data']),
        );
        Produce_Order_Product_Info::create($data);
    }
    // 该销售订单生成第一个生产订单时, 更改销售订单状态为采购中
    $listProduceOrder   = Produce_Order_Info::getBySalesOrderId($salesOrderId);
    if (count($listProduceOrder) == 1) {
        Sales_Order_Info::update(array(
            'sales_order_id'        => $salesOrderId,
            'sales_order_status'    => Sales_Order_Status::PURCHASE,
        ));
    }
    // 清空购物车
    Produce_Order_Cart::deleteBySalesOrderAndSupplier($salesOrderId, $supplierId);
    Utility::notice('成功创建生产订单', '/order/sales/produce_advice.php?sales_order_id='.$salesOrderId);
} else {

    Utility::notice('创建失败');
}