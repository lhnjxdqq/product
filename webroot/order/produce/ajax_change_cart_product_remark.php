<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId   = (int) $_POST['sales_order_id'];
$supplierId     = (int) $_POST['supplier_id'];
$productId      = (int) $_POST['product_id'];
$remark         = trim($_POST['remark']);

if (!$salesOrderId || !$supplierId || !$productId) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'value error',
    ));
    exit;
}

$data   = array(
    'sales_order_id'    => $salesOrderId,
    'supplier_id'       => $supplierId,
    'product_id'        => $productId,
    'remark'            => $remark,
);

if (Produce_Order_Cart::update($data)) {

    $result = array(
        'statusCode'    => 0,
        'statusInfo'    => 'success'
    );
} else {

    $result = array(
        'statusCode'    => 1,
        'statusInfo'    => 'update error'
    );
}

echo json_encode($result);exit;