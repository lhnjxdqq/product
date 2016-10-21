<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {

    echo json_encode(array(
        'code'      => 1,
        'message'   => 'method error',
    ));
    exit;
}

$salesOrderId   = (int) $_POST['sales_order_id'];
$goodsId        = (int) $_POST['goods_id'];
$cost           = sprintf('%.2f', (float) trim($_POST['cost']));

if (!$salesOrderId || !$goodsId) {

    echo json_encode(array(
        'code'      => 1,
        'message'   => '参数值有误',
    ));
    exit;
}

$data           = array(
    'sales_order_id'    => $salesOrderId,
    'goods_id'          => $goodsId,
    'cost'              => $cost,
);

Sales_Order_Goods_Info::update($data);

echo            json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
));
exit;