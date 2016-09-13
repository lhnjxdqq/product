<?php

require dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_POST['produce_order_arrive_id'],'到货单ID不能为空');
Validate::testNull($_POST['product_id'],'产品ID不能为空');

$productInfo                    = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveIdAndProductId($_POST['produce_order_arrive_id'],$_POST['product_id']);

if(empty($productInfo)){
    
    Produce_Order_Arrive_Product_Info::create($_POST);
}else{

    Produce_Order_Arrive_Product_Info::update($_POST);
}
$arriveProductInfo              = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($_POST['produce_order_arrive_id']);
$count = count($arriveProductInfo);
$quantityTotal              = array_sum(ArrayUtility::listField($arriveProductInfo,'quantity'));
$weightTotal                = array_sum(ArrayUtility::listField($arriveProductInfo,'weight'));
$storageWeightTotal         = array_sum(ArrayUtility::listField($arriveProductInfo,'storage_weight'));
$storageQuantityTotal        = array_sum(ArrayUtility::listField($arriveProductInfo,'storage_quantity'));

Produce_Order_Arrive_Info::update(array(
    'produce_order_arrive_id'   => $_POST['produce_order_arrive_id'],
    'count_product'             => $count,
    'quantity_total'            => $quantityTotal,
    'weight_total'              => $weightTotal,
    'storage_quantity_total'    => $storageQuantityTotal,
    'storage_weight'            => $storageWeightTotal,
));
echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count'         => $count,
        'weightTotal'   => $storageWeightTotal,
        'quantityTotal' => $storageQuantityTotal,
    ),
));
