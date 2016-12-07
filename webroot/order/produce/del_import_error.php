<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_GET['produce_order_id'],'生产订单ID不能为空');
Validate::testNull($_GET['produce_order_arrive_id'],'入库单Id不能为空');

Produce_Order_Arrive_Info::delete($_GET['produce_order_arrive_id']);

Utility::redirect("/order/produce/order_storage.php?produce_order_id=".$_GET['produce_order_id']);