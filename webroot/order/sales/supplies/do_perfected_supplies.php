<?php

require dirname(__FILE__).'/../../../../init.inc.php';

$data   = $_POST;

if(empty($data['supplies_id'])){
    
    Utility::notice('出货单Id不能为空');
}
$data['supplies_status']    = 1;
Sales_Supplies_Info::update($data);

$salesSuppliesInfo  = Sales_Supplies_Info::getById($data['supplies_id']);

Utility::notice('保存成功','/order/sales/supplies/index.php?sales_order_id='.$salesSuppliesInfo['sales_order_id']);