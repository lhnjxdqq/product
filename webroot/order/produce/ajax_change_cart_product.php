<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId   = (int) $_POST['sales_order_id'];
$supplierId     = (int) $_POST['supplier_id'];
$productId      = (int) $_POST['product_id'];
$quantity       = (int) $_POST['quantity'];

if (!$salesOrderId || !$supplierId || !$productId || $quantity <= 0) {

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
    'quantity'          => $quantity,
);

if (Produce_Order_Cart::update($data)) {

    $listSupplierGoodsDetail    = Produce_Order_Cart::getSupplierGoodsDetail($salesOrderId, $supplierId);
    $listGoodsId                = ArrayUtility::listField($listSupplierGoodsDetail, 'goods_id');
    $listGoodsInfo              = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
    $mapGoodsWeigth             = ArrayUtility::indexByField($listGoodsInfo, 'goods_id', 'weight_value_data');
    $data                       = array(
        'count_goods'       => count($listSupplierGoodsDetail),
        'count_quantity'    => 0,
        'count_weight'      => 0,
    );
    foreach ($listSupplierGoodsDetail as $goods) {

        $goodId             = $goods['goods_id'];
        $quantity           = $goods['quantity'];
        $weightValueData    = $mapGoodsWeigth[$goodId];
        $data['count_quantity'] += $quantity;
        $data['count_weight']   += $quantity * $weightValueData;
    }
    $result = array(
        'statusCode'    => 0,
        'statusInfo'    => 'success',
        'resultData'    => $data,
    );
} else {

    $result = array(
        'statusCode'    => 1,
        'statusInfo'    => 'update error',
    );
}

echo json_encode($result);
exit;