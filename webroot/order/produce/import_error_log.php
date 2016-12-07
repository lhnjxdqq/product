<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$produceOrderArriveId       = $_GET['produce_order_arrive_id'];

Validate::testNull($produceOrderArriveId,'入库单ID不能为空');

$produceOrderArriveInfo     = Produce_Order_Arrive_Info::getById($produceOrderArriveId);

$errorLog			= json_decode($produceOrderArriveInfo['error_log'],true);

$template           = Template::getInstance();

$template->assign('errorLog', $errorLog);
$template->assign('produceOrderArriveId', $produceOrderArriveId);
$template->assign('produceOrderArriveInfo', $produceOrderArriveInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/produce/import_error_log.tpl');