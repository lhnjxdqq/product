<?php

require dirname(__FILE__).'/../../../../init.inc.php';

$data = $_GET;

if(empty($data['supplies_id']) || empty($data['result'])){

    Utility::notice('data error');
}
$salesSuppliesInfo  = Sales_Supplies_Info::getById($data['supplies_id']);

$content    = array('supplies_id' => $data['supplies_id']);

if($data['result'] == 'OK'){
    
    $content['supplies_status'] = Sales_Supplies_Status::DELIVREED;

    //获取出货单商品详情
    $listSuppliesProductInfo    = Sales_Supplies_Product_Info::getBySuppliesId($data['supplies_id']);

    $listProductOrderArriveId   = array_unique(ArrayUtility::listField($listSuppliesProductInfo,'product_order_arrive_id'));
    
    foreach($listProductOrderArriveId as $key => $val){
        
        $orderArriveIdList  = explode(",",$val);
        
        foreach($orderArriveIdList as $val){
            
            $orderArriveId[]    = $val;
        }
    }
    foreach($orderArriveId as $id){
        
        Produce_Order_Arrive_Info::update(array(
            'produce_order_arrive_id'   => $id,
            'is_supplies_operation'     => 1,
        ));
    }
    
    Sales_Supplies_Info::update($content);
    
    $salesSuppliesInfo          = Sales_Supplies_Info::getById($data['supplies_id']);
    $salesSupplesProductInfo    = ArrayUtility::searchBy(Sales_Supplies_Info::getBySalesOrderId($salesSuppliesInfo['sales_order_id']),array('supplies_status'=>Sales_Supplies_Status::DELIVREED));
    $totalPrice                 = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'total_price'));

    Sales_Order_Info::update(array(
        'sales_order_id'    => $salesSuppliesInfo['sales_order_id'],
        'transaction_amount'=> $totalPrice,
    ));
}else{
    
    $content['supplies_status'] = Sales_Supplies_Status::NO_REVIEWED;
    $content['review_explain']  = $_POST['explain'];
    
    Sales_Supplies_Info::update($content);
}

Utility::notice('审核成功','/order/sales/supplies/index.php?sales_order_id='.$salesSuppliesInfo['sales_order_id']);