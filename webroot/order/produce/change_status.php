<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['produce_order_id'] || !$_GET['action']) {

    Utility::notice('缺少参数');
}

$produceOrderId = (int) $_GET['produce_order_id'];
$action         = trim($_GET['action']);

$statusCode     = Produce_Order_StatusCode::NEWLY_BUILT;

if ($action == 'verify') {

    // 审核通过后 变为已确认状态
    $statusCode = Produce_Order_StatusCode::CONFIRMED;
} elseif ($action == 'confirm') {

    // 工厂确认后 变为采购中状态
    $statusCode = Produce_Order_StatusCode::STOCKING;
}

if (Produce_Order_Info::changeStatus ($produceOrderId, $statusCode)) {

    Utility::notice('操作成功');
} else {

    Utility::notice('操作失败');
}