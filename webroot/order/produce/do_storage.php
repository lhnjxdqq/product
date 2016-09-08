<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$arriveId                   = $_GET['arrive_id'];
$auPrice                    = $_GET['au_price'];
Validate::testNull($arriveId,'到货表ID不能为空');
Validate::testNull($auPrice,'金价不能为空');

//到货单信息
$produceOrderArriveInfo     = Produce_Order_Arrive_Info::getById($arriveId);
Validate::testNull($produceOrderArriveInfo,'不存在的到货单');
$produceOrderId             = $produceOrderArriveInfo['produce_order_id'];
//到货单中的产品
$arriveProductInfo          = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($arriveId);
$listProductId              = ArrayUtility::listField($arriveProductInfo,'product_id');
$listProductInfo            = Product_Info::getByMultiId($listProductId);
$indexProductId             = ArrayUtility::indexByField($listProductInfo,'product_id');

foreach($arriveProductInfo as &$info){
    
    $amount[] = sprintf('%.2f',$info['storage_weight']*($auPrice+$indexProductId[$info['product_id']]['product_cost']));
}
Produce_Order_Arrive_Info::update(array(
    'produce_order_arrive_id'   => $arriveId,
    'transaction_amount'        => array_sum($amount),
    'is_storage'                => Produce_Order_Arrive_IsStorage::YES,
    'au_price'                  => $auPrice,
    'storage_time'              => date('Y-m-d H:i:s',time()),
    'storage_user_id'           => $_SESSION['user_id'],
));
Utility::notice('入库成功','/order/produce/order_storage.php?produce_order_id='.$produceOrderId);
