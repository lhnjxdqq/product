<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId      = (int) $_GET['sales_order_id'];
Validate::testNull($salesOrderId, "销售订单ID不能为空");
Sales_Order_Info::delete($salesOrderId);
Sales_Order_Goods_Info::delete($salesOrderId);

Utility::notice("删除成功","/order/sales/index.php");