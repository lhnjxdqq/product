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
}else{
    
    $content['supplies_status'] = Sales_Supplies_Status::NO_REVIEWED;
    $content['review_explain']  = $_POST['explain'];
}
Sales_Supplies_Info::update($content);

Utility::notice('审核成功','/order/sales/supplies/index.php?sales_order_id='.$salesSuppliesInfo['sales_order_id']);