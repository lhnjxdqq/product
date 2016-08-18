<?php
/**
 * 订单跟踪统计
 */
require_once dirname(__FILE__) . '/../../../init.inc.php';

$listCustomerName   = array_filter(ArrayUtility::listField(Order_Track_Info::listCustomerName(), 'customer_name'));
$listSalesName      = array_filter(ArrayUtility::listField(Order_Track_Info::listSalesName(), 'sales_name'));
$totalOrderCode     = Order_Track_Info::countOrderCodeByCondition($_GET);
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$page               = new PageList(array(
    PageList::OPT_TOTAL     => $totalOrderCode,
    PageList::OPT_URL       => '/order/track/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));
$listOrderCode      = array_filter(ArrayUtility::listField(Order_Track_Info::groupOrderCodeByCondition($_GET, array('order_date' => 'DESC'), $page->getOffset(), $perpage), 'order_code'));
$listInfo           = Order_Track_Info::listByCondition(array('order_code'=>$listOrderCode));
$groupInfoByOrder   = ArrayUtility::groupByField($listInfo, 'order_code');
$dateFilter         = function ($text) {

    return  !empty($text) && strtotime($text) > 0;
};
$dateBorder         = function ($listDate, $direct) {

    if (empty($listDate)) {

        return  NULL;
    }

    switch ($direct) {
        case    'min' :
            return  min($listDate);

        case    'max' :
            return  max($listDate);
    }

    return  NULL;
};
$dayDiff            = function ($dateA, $dateB) {

    if (empty($dateA) || empty($dateB)) {

        return  NULL;
    }

    return  ceil(abs(strtotime($dateA) - strtotime($dateB)) / 86400);
};

$mapOrderAmount     = array();

foreach ($groupInfoByOrder as $orderCode => $listInfo) {

    $listCarrySampleDate        = array_filter(ArrayUtility::listField($listInfo, 'carry_sample_date'), $dateFilter);
    $listOrderDate              = array_filter(ArrayUtility::listField($listInfo, 'order_date'), $dateFilter);
    $listOrderSupplierDate      = array_filter(ArrayUtility::listField($listInfo, 'order_date_supplier'), $dateFilter);
    $listConfirmSupplierDate    = array_filter(ArrayUtility::listField($listInfo, 'confirm_date_supplier'), $dateFilter);
    $listDeliverySupplierDate   = array_filter(ArrayUtility::listField($listInfo, 'delivery_date_supplier'), $dateFilter);
    $listArrivalSupplierDate    = array_filter(ArrayUtility::listField($listInfo, 'arrival_date_supplier'), $dateFilter);
    $listWarehousingDate        = array_filter(ArrayUtility::listField($listInfo, 'warehousing_time'), $dateFilter);
    $listShipmentDate           = array_filter(ArrayUtility::listField($listInfo, 'shipment_time'), $dateFilter);
    $listReturnMoneyDate        = array_filter(ArrayUtility::listField($listInfo, 'return_money_time'), $dateFilter);
    $listOrderQuantity          = array_filter(ArrayUtility::listField($listInfo, 'order_quantity'));
    $listShipmentQuantity       = array_filter(ArrayUtility::listField($listInfo, 'shipment_quantity'));
    $listOrderStatus            = array_filter(ArrayUtility::listField($listInfo, 'order_status'));
    $minCarrySampleDate         = $dateBorder($listCarrySampleDate, 'min');
    $maxOrderDate               = $dateBorder($listOrderDate, 'max');
    $maxOrderSupplierDate       = $dateBorder($listOrderSupplierDate, 'max');
    $maxConfirmSupplierDate     = $dateBorder($listConfirmSupplierDate, 'max');
    $maxDeliverySupplierDate    = $dateBorder($listDeliverySupplierDate, 'max');
    $maxArrivalSupplierDate     = $dateBorder($listArrivalSupplierDate, 'max');
    $maxWarehousingDate         = $dateBorder($listWarehousingDate, 'max');
    $maxShipmentDate            = $dateBorder($listShipmentDate, 'max');
    $maxReturnMoneyDate         = $dateBorder($listReturnMoneyDate, 'max');
    $groupInfoByBatchCode       = ArrayUtility::groupByField($listInfo, 'batch_code_supplier');
    $amountByBatch              = array();

    foreach ($groupInfoByBatchCode as $batchCode => $listBatchInfo) {

        $listCarrySampleDateBatch       = array_filter(ArrayUtility::listField($listBatchInfo, 'carry_sample_date'), $dateFilter);
        $listOrderDateBatch             = array_filter(ArrayUtility::listField($listBatchInfo, 'order_date'), $dateFilter);
        $listOrderSupplierDateBatch     = array_filter(ArrayUtility::listField($listBatchInfo, 'order_date_supplier'), $dateFilter);
        $listConfirmSupplierDateBatch   = array_filter(ArrayUtility::listField($listBatchInfo, 'confirm_date_supplier'), $dateFilter);
        $listDeliverySupplierDateBatch  = array_filter(ArrayUtility::listField($listBatchInfo, 'delivery_date_supplier'), $dateFilter);
        $listArrivalSupplierDateBatch   = array_filter(ArrayUtility::listField($listBatchInfo, 'arrival_date_supplier'), $dateFilter);
        $listWarehousingDateBatch       = array_filter(ArrayUtility::listField($listBatchInfo, 'warehousing_time'), $dateFilter);
        $listShipmentDateBatch          = array_filter(ArrayUtility::listField($listBatchInfo, 'shipment_time'), $dateFilter);
        $listReturnMoneyDateBatch       = array_filter(ArrayUtility::listField($listBatchInfo, 'return_money_time'), $dateFilter);
        $listOrderQuantityBatch         = array_filter(ArrayUtility::listField($listBatchInfo, 'order_quantity'));
        $listShipmentQuantityBatch      = array_filter(ArrayUtility::listField($listBatchInfo, 'shipment_quantity'));
        $listOrderStatusBatch           = array_filter(ArrayUtility::listField($listBatchInfo, 'order_status'));
        $minCarrySampleDateBatch        = $dateBorder($listCarrySampleDate, 'min');
        $maxOrderDateBatch              = $dateBorder($listOrderDate, 'max');
        $maxOrderSupplierDateBatch      = $dateBorder($listOrderSupplierDate, 'max');
        $maxConfirmSupplierDateBatch    = $dateBorder($listConfirmSupplierDate, 'max');
        $maxDeliverySupplierDateBatch   = $dateBorder($listDeliverySupplierDate, 'max');
        $maxArrivalSupplierDateBatch    = $dateBorder($listArrivalSupplierDate, 'max');
        $maxWarehousingDateBatch        = $dateBorder($listWarehousingDate, 'max');
        $maxShipmentDateBatch           = $dateBorder($listShipmentDate, 'max');
        $maxReturnMoneyDateBatch        = $dateBorder($listReturnMoneyDate, 'max');
        $currentByBatch                 = array(
            'carry_sample_to_order'     => $dayDiff($minCarraySampleDateBatch, $maxOrderDateBatch),
            'order_to_supplier'         => $dayDiff($maxOrderSupplierDateBatch, $maxOrderDateBatch),
            'confirm_to_supplier'       => $dayDiff($maxConfirmSupplierDateBatch, $maxOrderSupplierDateBatch),
            'delivery_to_supplier'      => $dayDiff($maxConfirmSupplierDateBatch, $maxDeliverySupplierDateBatch),
            'arrival_to_supplier'       => $dayDiff($maxArrivalSupplierDateBatch, $maxDeliverySupplierDateBatch),
            'arrival_to_warehousing'    => $dayDiff($maxArrivalSupplierDateBatch, $maxWarehousingDate),
            'warehousing_to_shipment'   => $dayDiff($maxShipmentDateBatch, $maxWarehousingDateBatch),
            'shipment_to_return_money'  => $dayDiff($maxShipmentDateBatch, $maxReturnMoneyDateBatch),
            'total_order_quantity'      => array_sum($listOrderQuantityBatch),
            'total_shipment_quantity'   => array_sum($listShipmentQuantityBatch),
            'carry_to_shipment'         => $dayDiff($minCarraySampleDateBatch, $maxShipmentDateBatch),
            'order_status'              => $listOrderStatusBatch[0],
        );
        $amountByBatch[$batchCode]      = $currentByBatch;
    }

    $currentAmount          = array(
        'carry_sample_to_order'     => $dayDiff($minCarraySampleDate, $maxOrderDate),
        'order_to_supplier'         => $dayDiff($maxOrderSupplierDate, $maxOrderDate),
        'confirm_to_supplier'       => $dayDiff($maxConfirmSupplierDate, $maxOrderSupplierDate),
        'delivery_to_supplier'      => $dayDiff($maxConfirmSupplierDate, $maxDeliverySupplierDate),
        'arrival_to_supplier'       => $dayDiff($maxArrivalSupplierDate, $maxDeliverySupplierDate),
        'arrival_to_warehousing'    => $dayDiff($maxArrivalSupplierDate, $maxWarehousingDate),
        'warehousing_to_shipment'   => $dayDiff($maxShipmentDate, $maxWarehousingDate),
        'shipment_to_return_money'  => $dayDiff($maxShipmentDate, $maxReturnMoneyDate),
        'total_order_quantity'      => array_sum($listOrderQuantity),
        'total_shipment_quantity'   => array_sum($listShipmentQuantity),
        'carry_to_shipment'         => $dayDiff($minCarraySampleDate, $maxShipmentDate),
        'order_status'              => $listOrderStatus[0],
        'amount_by_batch'             => $amountByBatch,
    );
    $mapOrderAmount[$orderCode] = $currentAmount;
}
$mapOrderStatusLang = array(
    0   => '未完成',
    1   => '已完成',
);
$standard           = array(
    'carry_sample_to_order'     => 4,
    'order_to_supplier'         => 1,
    'confirm_to_supplier'       => 1,
    'delivery_to_supplier'      => 15,
    'arrival_to_supplier'       => 3,
    'arrival_to_warehousing'    => 1,
    'warehousing_to_shipment'   => 1,
    'shipment_to_return_money'  => 5,
);
$data   = array(
    'mainMenu'      => Menu_Info::getMainMenu()
);

$template           = Template::getInstance();
$template->assign('data', $data);
$template->assign('listCustomerName', $listCustomerName);
$template->assign('listSalesName', $listSalesName);
$template->assign('listOrderCode', $listOrderCode);
$template->assign('mapOrderAmount', $mapOrderAmount);
$template->assign('mapOrderStatusLang', $mapOrderStatusLang);
$template->assign('standard', $standard);
$template->assign('pageViewData', $page->getViewData());
$template->display('order/track/index.tpl');
