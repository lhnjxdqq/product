<?php

require_once  dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_GET['produce_order_id'],'订单ID不能为空');
Produce_Order_Info::update(array(
    'produce_order_id'  => $_GET['produce_order_id'],
    'status_code'       => Produce_Order_StatusCode::FINISHED,
));
Utility::notice('订单已完成','/order/produce/index.php');