<?php

require_once dirname(__FILE__).'/../../../../init.inc.php';

if(empty($_GET['product_id']) || empty($_POST['supplies_id'])){
        
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '数据有误',
        'data'      => array(),
    ));
    exit;   
}

$condition  = $_GET;
$condition  +=array(
    'supplies_id'   => $_POST['supplies_id'],
);
Sales_Supplies_Product_Info::update($condition);

$salesSuppliesProductInfo   = Sales_Supplies_Product_Info::getBySuppliesId($_POST['supplies_id']);
$suppliesProductInfo['total_quantity']      = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_quantity'));
$suppliesProductInfo['count_style']         = count($salesSuppliesProductInfo);
$suppliesProductInfo['total_weight']        = array_sum(ArrayUtility::listField($salesSuppliesProductInfo,'supplies_weight'));

Sales_Supplies_Info::update(array(
    'supplies_id'               => $_POST['supplies_id'],
    'supplies_quantity'         => $suppliesProductInfo['count_style'],
    'supplies_quantity_total'   => $suppliesProductInfo['total_quantity'],
    'supplies_weight'           => $suppliesProductInfo['total_weight'],
));

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'total_quantity'    => $suppliesProductInfo['total_quantity'],
        'count_style'       => $suppliesProductInfo['count_style'],
        'total_weight'      => $suppliesProductInfo['total_weight'],
    ),
));