<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    Utility::notice('method error');
}

$produceOrderId = (int) $_POST['produce-order-id'];
$prepaidAmount  = sprintf('%.2f', $_POST['prepaid-amount']);
$orderType      = (int) $_POST['order-type'];
$arrivalDate    = date('Y-m-d H:i:s', strtotime(trim($_POST['arrival-date'])));
$orderRemark    = trim($_POST['order-remark']);

if (!$orderType) {

    Utility::notice('订单类型必填');
}

if (!$produceOrderId) {

    Utility::notice('生产订单ID错误');
}

$data   = array(
    'produce_order_id'      => $produceOrderId,
    'prepaid_amout'         => $prepaidAmount,
    'arrival_date'          => $arrivalDate,
    'order_type'            => $orderType,
    'produce_order_remark'  => $orderRemark,
);

if (Produce_Order_Info::update($data)) {

    Utility::notice('编辑成功');
} else {

    Utility::dump('编辑失败');
}