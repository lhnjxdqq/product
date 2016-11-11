<?php

require dirname(__FILE__).'/../../../../init.inc.php';

if (!isset($_GET['sales_order_id'])) {

    Utility::notice('销售订单Id不能为空');
}
// 生产订单
$salesOrderId     = (int) $_GET['sales_order_id'];
$salesOrderInfo   = Sales_Order_Info::getById($salesOrderId);

if (!$salesOrderInfo) {

    Utility::notice('销售订单不存在');
}

//获取供应商列表
$listSupplier       = Supplier_Info::listAll();
$indexSupplierId    = ArrayUtility::indexByField($listSupplier ,'supplier_id');

//获取生产订单
$produceOrderInfo   = Produce_Order_Info::getBySalesOrderId($salesOrderInfo['sales_order_id']);
$listProduceOrderId = ArrayUtility::listField($produceOrderInfo,'produce_order_id');
$indexProduceOrderId    = ArrayUtility::indexByField($produceOrderInfo,'produce_order_id');

//获取入库记录
$mapProduceOrderArriveInfo = ArrayUtility::searchBy(Produce_Order_Arrive_Info::getByMultiProduceOrderId($listProduceOrderId),array('is_supplies_operation'=>1));

if(empty($mapProduceOrderArriveInfo)){
    
    Utility::notice('无入库记录或者,入库单正在操作,请检查后再操作');
}
$mainMenu = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('mapProduceOrderArriveInfo', $mapProduceOrderArriveInfo);
$template->assign('indexSupplierId', $indexSupplierId);
$template->assign('indexProduceOrderId', $indexProduceOrderId);
$template->assign('salesOrderInfo', $salesOrderInfo);
$template->assign('mainMenu', $mainMenu);
$template->display('order/sales/supplies/storage_list.tpl');