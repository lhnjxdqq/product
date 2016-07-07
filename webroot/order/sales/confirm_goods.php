<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
echo $salesOrderId;