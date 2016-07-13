<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['sales_order_id'],'销售订单ID不能为空');

Sales_Order_Info::update(array(
        'sales_order_id'    => $_GET['sales_order_id'],
        'sales_order_status'=> Sales_Order_Status::CONFIRM,
    )
);

Utility::notice('订单审核成功','/order/sales/index.php');