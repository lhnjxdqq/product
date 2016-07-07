<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId       = $_GET['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$template           = Template::getInstance();

$template->assign('salesOrderId', $salesOrderId);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/add_goods.tpl');