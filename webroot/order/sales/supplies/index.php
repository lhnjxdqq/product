<?php
require_once dirname(__FILE__) . '/../../../../init.inc.php';

if (!isset($_GET['sales_order_id'])) {

    Utility::notice('销售订单Id不能为空');
}

// 生产订单
$salesOrderId               = (int) $_GET['sales_order_id'];
$salesOrderInfo             = Sales_Order_Info::getById($salesOrderId);
$salesSupplesProductInfo    = ArrayUtility::searchBy(Sales_Supplies_Info::getBySalesOrderId($salesOrderId),array('supplies_status'=>Sales_Supplies_Status::DELIVREED));

$totalQuantity  = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'supplies_quantity_total'));
$totalWeight    = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'supplies_weight'));
$totalPrice     = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'total_price'));

if (!$salesOrderInfo) {

    Utility::notice('销售订单不存在');
}
$suppliesStatusInfo       = Sales_Supplies_Status::getSuppliesStatus();

$suppliesInfo       = Sales_Supplies_Info::getBySalesOrderId($salesOrderId);

$mapCustomer        = ArrayUtility::indexByField(ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL)),'customer_id');

$mapUser            = ArrayUtility::indexByField(User_Info::listAll(),'user_id');

$mapSalesperson = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$statusList = Sales_Order_Status::getOrderStatus();

foreach($statusList as $statusId=>$statusName){
    
    $mapOrderStatus[$statusId] = array(
        
        'status_id'     => $statusId,
        'status_name'   => $statusName,
    );
}

$orderTypeInfo = Sales_Order_Type::getOrderType();

foreach($orderTypeInfo as $orderTypeId=>$orderName){
    
    $mapOrderStyle[$orderTypeId] = array(
        
        'order_type_id'     => $orderTypeId,
        'order_type_name'        => $orderName,
    );
}

$template = Template::getInstance();

$template->assign('salesOrderInfo',$salesOrderInfo);
$template->assign('mapCustomer',$mapCustomer);
$template->assign('mapUser',$mapUser);
$template->assign('mapOrderStatus',$mapOrderStatus);
$template->assign('totalQuantity',$totalQuantity);
$template->assign('totalWeight',$totalWeight);
$template->assign('totalPrice',$totalPrice);
$template->assign('mapOrderStyle',$mapOrderStyle);
$template->assign('mapSalesperson',$mapSalesperson);
$template->assign('suppliesInfo',$suppliesInfo);
$template->assign('suppliesStatusInfo',$suppliesStatusInfo);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->display('order/sales/supplies/index.tpl');