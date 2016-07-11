<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOderData              = $_POST;

$salesOrderId       = $_POST['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

$json                       = json_decode($salesOderData['sales_order_data'], true);
$data                       = array();
foreach ($json as $key => $item) {
    
    $key    = substr($key,8);

    if (0 < $pos = strpos($key, '[')) {
        $attr           = substr($key, 0, $pos);
        $data[$attr]    = isset($data[$attr])   ? $data[$attr]  : array();
        $subAttr        = substr($key, $pos + 1, strlen($key) - $pos - 2);
        $data[$attr][$subAttr]  = $item;
    } else {
        $data[$key] = $item;
    }
}

foreach($data as $goodsId=> $info){
    
    $content    = array(
        'sales_order_id'    => $salesOrderId,
        'goods_id'          => $goodsId,
        'goods_quantity'    => (int) $info['quantity'],
        'reference_weight'  => ((int) $info['quantity']) * $info['weight'],
        'remark'            => $info['goods_remark'],
    );

    Sales_Order_Goods_Info::update($content);
}

$salesSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);

Sales_Order_Info::update(array(
        'sales_order_id'    => $salesOrderId,
        'count_goods'       => count($salesSkuInfo),    
        'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
        'update_time'       => date('Y-m-d H:i:s', time()),
        'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
    )
);

$salesOrderId       = $_POST['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单ID不能为空');
$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');

$mapSalesperson = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$orderTypeInfo = Sales_Order_Type::getOrderType();

foreach($orderTypeInfo as $orderTypeId=>$orderName){
    
    $mapOrderStyle[$orderTypeId] = array(
        
        'order_type_id'     => $orderTypeId,
        'order_name'        => $orderName,
    );
}
$template           = Template::getInstance();

$template->assign('salesOrderId', $salesOrderId);
$template->assign('mapSalesperson', $mapSalesperson);
$template->assign('salesOrderInfo', $salesOrderInfo);
$template->assign('mapOrderStyle', $mapOrderStyle);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/perfected_sales_order.tpl');