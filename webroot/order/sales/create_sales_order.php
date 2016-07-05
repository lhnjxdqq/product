<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesQuotationId   = $_POST['sales_quotation_id'];
Validate::testNull($salesQuotationId,'销售报价单ID不能为空');
$salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);
Validate::testNull($salesQuotationInfo, '报价单不存在');

$content = array(
    'sales_order_sn'        => Sales_Order_Info::createOrderSn(),
    'sales_order_status'    => Sales_Order_Status::NEWS,
    'sales_quotation_id'    => $salesQuotationId,
    'create_user_id'        => $_SESSION['user_id'],
    'salesperson_id'        => '0',
    'create_time'           => date('Y-m-d H:i:s',time()),
    'update_time'           => date('Y-m-d H:i:s',time()),
    'order_type_id'         => Sales_Order_Type::ORDERED,
    'audit_person_id'       => $_SESSION['user_id'],
    'customer_id'           => $salesQuotationInfo['customer_id'],
);
$salesOrderId    = Sales_Order_Info::create($content);

Validate::testNull($salesOrderId,'报价单选择失败');

Utility::redirect("/order/sales/add_goods.php?sales_order_id=".$salesOrderId);