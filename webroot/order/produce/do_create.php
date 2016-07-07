<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    Utility::notice('method error');
}

$salesOrderId       = (int) $_POST['sales-order-id'];
$supplierId         = (int) $_POST['supplier-id'];
$produceOrderCart   = Produce_Order_Cart::getBySalesOrderAndSupplier($salesOrderId, $supplierId);

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
        );
        Produce_Order_Product_Info::create($data);
    }
    Utility::notice('成功创建生产订单', '/order/produce/index.php');
} else {

    Utility::notice('创建失败');
}