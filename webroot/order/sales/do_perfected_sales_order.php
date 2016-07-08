<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$data               = $_POST;
$salesOrderId       = $data['sales_order_id'];;
$data['prepaid_amount'] = (float) $data['prepaid_amount'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
Validate::testNull($data['salesperson_id'],'销售员不能为空');
Validate::testNull($data['order_type_id'],'销售类型不能为空');

Sales_Order_Info::update($data);

Utility::notice("订单创建成功","/order/sales/index.php");