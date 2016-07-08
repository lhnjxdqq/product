<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

$mapSalesperson = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$orderTypeInfo = Sales_Order_Type::getOrderType();

foreach($orderTypeInfo as $orderTypeId=>$orderName){
    
    $mapOrderStyle[$orderTypeId] = array(
        
        'order_type_id'     => $orderTypeId,
        'order_name'        => $orderName,
    );
}
$template           = Template::getInstance();

$template->assign('salesOrderId', $salesOrderId);
$template->assign('mapSalesperson', $mapSalesperson);
$template->assign('salesOrderInfo', $salesOrderInfo);
$template->assign('mapOrderStyle', $mapOrderStyle);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/perfected_sales_order.tpl');