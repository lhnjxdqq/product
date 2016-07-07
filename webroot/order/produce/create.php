<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['sales_order_id'] || !$_GET['supplier_id']) {

    Utility::notice('缺少参数');
}

$salesOrderId   = (int) $_GET['sales_order_id'];
$supplierId     = (int) $_GET['supplier_id'];

$supplierInfo   = Supplier_Info::getById($supplierId);

$data['supplierInfo']   = $supplierInfo;
$data['mainMenu']       = Menu_Info::getMainMenu();
$data['listOrderType']  = Produce_Order_Type::getOrderType();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/create.tpl');