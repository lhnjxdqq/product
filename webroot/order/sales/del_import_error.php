<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];

Validate::testNull($salesOrderId,'销售订单ID不能为空');

Sales_Order_Info::update(array(
	'sales_order_id'	=> $salesOrderId,
	'order_file_status' => 0,
	'import_error_log'	=> '',
));
Utility::redirect("/order/sales/index.php");