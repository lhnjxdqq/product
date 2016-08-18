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
$dateFilter         = function ($text) {

    return  !empty($text) && strtotime($text) > 0;
};
$dateBorder         = function ($listDate, $direct = 'max') {

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

foreach ($listInfo as & $info) {

    $info['carry_sample_to_order']  = $dateFilter($info['carry_sample_date']) && $dateFilter($info['order_date'])
                                    ? $dayDiff($info['carry_sample_date'], $info['order_date'])
                                    : NULL;
    $info['order_to_supplier']      = $dateFilter($info['order_date_supplier']) && $dateFilter($info['order_date'])
                                    ? $dayDiff($info['order_date_supplier'], $info['order_date'])
                                    : NULL;
    $info['confirm_to_supplier']    = $dateFilter($info['order_date_supplier']) && $dateFilter($info['confirm_date_supplier'])
                                    ? $dayDiff($info['order_date_supplier'], $info['confirm_date_supplier'])
                                    : NULL;
    $info['delivery_to_supplier']   = $dateFilter($info['delivery_date_supplier']) && $dateFilter($info['confirm_date_supplier'])
                                    ? $dayDiff($info['delivery_date_supplier'], $info['confirm_date_supplier'])
                                    : NULL;
    $info['arrival_to_supplier']    = $dateFilter($info['delivery_date_supplier']) && $dateFilter($info['arrival_date_supplier'])
                                    ? $dayDiff($info['delivery_date_supplier'], $info['arrival_date_supplier'])
                                    : NULL;
    $info['arrival_to_warehousing'] = $dateFilter($info['warehousing_time']) && $dateFilter($info['arrival_date_supplier'])
                                    ? $dayDiff($info['warehousing_time'], $info['arrival_date_supplier'])
                                    : NULL;
    $info['warehousing_to_shipment']= $dateFilter($info['warehousing_time']) && $dateFilter($info['shipment_time'])
                                    ? $dayDiff($info['warehousing_time'], $info['shipment_time'])
                                    : NULL;
    $info['shipment_to_return_money']= $dateFilter($info['return_money_time']) && $dateFilter($info['shipment_time'])
                                    ? $dayDiff($info['return_money_time'], $info['shipment_time'])
                                    : NULL;
    $info['carry_to_shipment']      = $dateFilter($info['carry_sample_date']) && $dateFilter($info['shipment_time'])
                                    ? $dayDiff($info['carry_sample_date'], $info['shipment_time'])
                                    : NULL;
}

$groupInfoByOrder   = ArrayUtility::groupByField($listInfo, 'order_code');
$mapOrderAmount     = array();

foreach ($groupInfoByOrder as $orderCode => $listInfo) {

    $listOrderQuantity          = array_filter(ArrayUtility::listField($listInfo, 'order_quantity'));
    $listShipmentQuantity       = array_filter(ArrayUtility::listField($listInfo, 'shipment_quantity'));
    $listOrderStatus            = array_filter(ArrayUtility::listField($listInfo, 'order_status'));
    $groupInfoByBatchCode       = ArrayUtility::groupByField($listInfo, 'batch_code_supplier');
    $amountByBatch              = array();

    foreach ($groupInfoByBatchCode as $batchCode => $listBatchInfo) {

        $listOrderQuantityBatch         = array_filter(ArrayUtility::listField($listBatchInfo, 'order_quantity'));
        $listShipmentQuantityBatch      = array_filter(ArrayUtility::listField($listBatchInfo, 'shipment_quantity'));
        $listOrderStatusBatch           = array_filter(ArrayUtility::listField($listBatchInfo, 'order_status'));
        $listSupplierCodeBatch          = array_filter(ArrayUtility::listField($listBatchInfo, 'supplier_code'));
        $currentByBatch                 = array(
            'supplier_code'             => $listSupplierCodeBatch[0],
            'carry_sample_to_order'     => $dateBorder(ArrayUtility::listField($listBatchInfo, 'carry_sample_to_order')),
            'order_to_supplier'         => $dateBorder(ArrayUtility::listField($listBatchInfo, 'order_to_supplier')),
            'confirm_to_supplier'       => $dateBorder(ArrayUtility::listField($listBatchInfo, 'confirm_to_supplier')),
            'delivery_to_supplier'      => $dateBorder(ArrayUtility::listField($listBatchInfo, 'delivery_to_supplier')),
            'arrival_to_supplier'       => $dateBorder(ArrayUtility::listField($listBatchInfo, 'arrival_to_supplier')),
            'arrival_to_warehousing'    => $dateBorder(ArrayUtility::listField($listBatchInfo, 'arrival_to_warehousing')),
            'warehousing_to_shipment'   => $dateBorder(ArrayUtility::listField($listBatchInfo, 'warehousing_to_shipment')),
            'shipment_to_return_money'  => $dateBorder(ArrayUtility::listField($listBatchInfo, 'shipment_to_return_money')),
            'total_order_quantity'      => array_sum($listOrderQuantityBatch),
            'total_shipment_quantity'   => array_sum($listShipmentQuantityBatch),
            'carry_to_shipment'         => $dateBorder(ArrayUtility::listField($listBatchInfo, 'carry_to_shipment')),
            'order_status'              => $listOrderStatusBatch[0],
        );
        $amountByBatch[$batchCode]      = $currentByBatch;
    }

    $currentAmount          = array(
        'carry_sample_to_order'     => $dateBorder(ArrayUtility::listField($listInfo, 'carry_sample_to_order')),
        'order_to_supplier'         => $dateBorder(ArrayUtility::listField($listInfo, 'order_to_supplier')),
        'confirm_to_supplier'       => $dateBorder(ArrayUtility::listField($listInfo, 'confirm_to_supplier')),
        'delivery_to_supplier'      => $dateBorder(ArrayUtility::listField($listInfo, 'delivery_to_supplier')),
        'arrival_to_supplier'       => $dateBorder(ArrayUtility::listField($listInfo, 'arrival_to_supplier')),
        'arrival_to_warehousing'    => $dateBorder(ArrayUtility::listField($listInfo, 'arrival_to_warehousing')),
        'warehousing_to_shipment'   => $dateBorder(ArrayUtility::listField($listInfo, 'warehousing_to_shipment')),
        'shipment_to_return_money'  => $dateBorder(ArrayUtility::listField($listInfo, 'shipment_to_return_money')),
        'total_order_quantity'      => array_sum($listOrderQuantity),
        'total_shipment_quantity'   => array_sum($listShipmentQuantity),
        'carry_to_shipment'         => $dateBorder(ArrayUtility::listField($listInfo, 'carry_to_shipment')),
        'order_status'              => $listOrderStatus[0],
        'amount_by_batch'           => $amountByBatch,
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
