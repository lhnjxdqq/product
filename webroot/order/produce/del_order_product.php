<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';


$produceOrderId = (int) $_GET['produce_order_id'];
$productIdList  = array();
if (isset($_GET['product_id'])) {

    $productIdList      = (array) intval($_GET['product_id']);
}

if (isset($_GET['multi_product_id'])) {

    $productIdString    = trim($_GET['multi_product_id']);
    $productIdList      = explode(',', $productIdString);
}

if (!$produceOrderId || empty($productIdList)) {

    Utility::notice('参数错误');
}

$status = Produce_Order_ExportStatus::GENERATING;
$taskInfo   = Produce_Order_Export_Task::getByProduceOrderId($produceOrderId);
if (!empty($taskInfo) && $taskInfo['export_status'] == $status) {
    
    Utility::notice("该订单正在生成下载文件，无法操作",'/order/produce/index.php');
    exit;
}else if(!empty($taskInfo)){
    Produce_Order_Export_Task::update(array(
        'task_id'           => $taskInfo['task_id'],
        'export_status'     => Sales_Order_ExportStatus::WAITING,
    ));
}

$delete = Produce_Order_Product_Info::deleteByMultiProductId($produceOrderId, $productIdList);
if ($delete) {

    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}