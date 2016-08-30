<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$ListSalesQuotationId   = $_POST['sales_quotation_id'];
Validate::testNull($ListSalesQuotationId,'销售报价单ID不能为空');

//最多5个
if ( count($ListSalesQuotationId) > 5 ) {
    Validate::testNull($ListSalesQuotationId, '报价单最多只能选择5个');
}

$listCustomerId = array();
foreach ($ListSalesQuotationId as $salesQuotationId) {

    $salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);
    // print_r($salesQuotationInfo);exit;
    Validate::testNull($salesQuotationInfo, '报价单不存在');
    $listCustomerId[] = $salesQuotationInfo['customer_id'];
}

$customerId             = array_unique($listCustomerId);
if ( count($customerId) > 1 ) {

    Validate::testNull(false, '客户不一致');
}


$ListSalesQuotationId   = implode(',', $ListSalesQuotationId);

$content = array(
    'sales_order_sn'        => Sales_Order_Info::createOrderSn(),
    'sales_order_status'    => Sales_Order_Status::NEWS,
    'sales_quotation_id'    => $ListSalesQuotationId,
    'create_user_id'        => $_SESSION['user_id'],
    'salesperson_id'        => '0',
    'order_time'            => date('Y-m-d',time()),
    'create_time'           => date('Y-m-d H:i:s',time()),
    'update_time'           => date('Y-m-d H:i:s',time()),
    'order_type_id'         => Sales_Order_Type::ORDERED,
    'audit_person_id'       => $_SESSION['user_id'],
    'customer_id'           => array_values($customerId)[0],
);

$salesOrderId    = Sales_Order_Info::create($content);

Validate::testNull($salesOrderId,'报价单选择失败');

Utility::redirect("/order/sales/add_goods.php?sales_order_id=".$salesOrderId);