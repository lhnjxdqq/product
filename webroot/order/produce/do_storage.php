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
$amount                     = array();

foreach($arriveProductInfo as $key=>$info){

    if((0 == $info['quantity'] && 0 != $info['weight']) || (0 != $info['quantity'] && 0 == $info['weight'])){
        
        $producrInfo        = Product_Info::getById($info['product_id']);
        Utility::notice('产品编号为'  . $producrInfo['product_sn'] .'的数据错误');
        exit;
    }
    if((0 == $info['storage_quantity'] && 0 != $info['storage_weight']) || (0 != $info['storage_quantity'] && 0 == $info['storage_weight'])){
        
        $producrInfo        = Product_Info::getById($info['product_id']);
        Utility::notice('产品编号为'  . $producrInfo['product_sn'] .'的数据错误');
        exit;
    }
    
    if(0 == $info['quantity'] && 0 == $info['weight']){

        unset($arriveProductInfo[$key]);
        continue;
    }
    
    if(0 == $info['storage_quantity'] && 0 == $info['storage_weight']){

        unset($arriveProductInfo[$key]);
        continue;
    }

    $amount[] = sprintf('%.2f',$info['storage_weight']*($auPrice+$indexProductId[$info['product_id']]['product_cost']));
}

Produce_Order_Arrive_Info::update(array(
    'produce_order_arrive_id'   => $arriveId,
    'transaction_amount'        => array_sum($amount),
    'storage_count_product'     => count($arriveProductInfo),
    'storage_weight'            => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_weight')),
    'storage_quantity_total'    => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_quantity')),
    'is_storage'                => Produce_Order_Arrive_IsStorage::YES,
    'au_price'                  => $auPrice,
    'storage_time'              => date('Y-m-d H:i:s',time()),
    'storage_user_id'           => $_SESSION['user_id'],
));

Utility::notice('入库成功','/order/produce/order_storage.php?produce_order_id='.$produceOrderId);
