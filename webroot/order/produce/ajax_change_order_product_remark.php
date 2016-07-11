<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'method error',
    ));
    exit;
}

$produceOrderId = (int) $_POST['produce_order_id'];
$productId      = (int) $_POST['product_id'];
$remark         = trim($_POST['remark']);

if (!$produceOrderId || !$productId) {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'value error',
    ));
    exit;
}

$data   = array(
    'produce_order_id'  => $produceOrderId,
    'product_id'        => $productId,
    'remark'            => $remark,
);

if (Produce_Order_Product_Info::update($data)) {

    echo json_encode(array(
        'statusCode'    => 0,
        'statusInfo'    => 'success',
    ));
    exit;
} else {

    echo json_encode(array(
        'statusCode'    => 1,
        'statusInfo'    => 'update error',
    ));
    exit;
}