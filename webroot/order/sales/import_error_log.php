<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];

Validate::testNull($salesOrderId,'销售订单ID不能为空');

$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);

$errorLog			= json_decode($salesOrderInfo['import_error_log'],true);

$template           = Template::getInstance();

$template->assign('errorLog', $errorLog);
$template->assign('salesOrderId', $salesOrderId);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/import_error_log.tpl');