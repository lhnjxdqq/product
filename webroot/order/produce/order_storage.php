<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['produce_order_id'])) {

    Utility::notice('produce_order_id is missing');
}

// 生产订单
$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}

$template = Template::getInstance();
$template->assign('produceOrderId',$produceOrderId);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->display('order/produce/storage.tpl');