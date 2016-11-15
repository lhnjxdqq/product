<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['customer_id'])) {

    Utility::notice('客户ID不能为空');
}
$condition  = array(
    'customer_id'   => $_GET['customer_id'],
);
$countSalesOrder        = Sales_Order_Info::countByCondition($condition);
$countSalesQuotation    = Sales_Quotation_Info::countByCondition($condition);

if($countSalesOrder > 0){

    Utility::notice('该客户关联销售订单，请清空相关数据后再操作');
}

if($countSalesQuotation > 0){

    Utility::notice('该客户关联销售报价单，请清空相关数据后再操作');
}
$data       = array(
    'customer_id'           => (int) $_GET['customer_id'],
    'delete_status'         => Customer_DeleteStatus::DELETED,
);

Customer_Info::update($data);
Utility::notice('删除客户成功');
