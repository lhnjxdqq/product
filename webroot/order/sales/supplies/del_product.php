<?php

require_once dirname(__FILE__).'/../../../../init.inc.php';

if(empty($_GET['product_id'] || $_GET['supplies_id'])){
    
    Utility::notice('参数错误','/order/sales/supplies/product_list.php?supplies_id='.$_GET['supplies_id']);
}

Sales_Supplies_Product_Info::delete($_GET['product_id'],$_GET['supplies_id']);

$salesSuppliesProductInfo   = Sales_Supplies_Product_Info::getBySuppliesId($_GET['supplies_id']);
$suppliesProductInfo['total_quantity']      = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_quantity'));
$suppliesProductInfo['count_style']         = count($salesSuppliesProductInfo);
$suppliesProductInfo['total_weight']        = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_weight'));

Sales_Supplies_Info::update(array(
    'supplies_id'               => $_GET['supplies_id'],
    'supplies_quantity'         => $suppliesProductInfo['count_style'],
    'supplies_quantity_total'  => $suppliesProductInfo['total_quantity'],
    'supplies_weight'           => $suppliesProductInfo['total_weight'],
));

Utility::notice('删除成功','/order/sales/supplies/product_list.php?supplies_id='.$_GET['supplies_id']);