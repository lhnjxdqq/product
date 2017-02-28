<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$data      = $_POST;

Validate::testNull($data['sales_order_id'], "销售订单ID不能为空");
Validate::testNull($data['goods_id'], "skuId不能为空不能为空");

$listProductInfo    = Product_Info::getByMultiGoodsId($data['goods_id']);
Validate::testNull($listProductInfo, "参数错误");
$listProductId      = ArrayUtility::listField($listProductInfo,'product_id');
$listProduceOrderInfo       = Produce_Order_Info::getBySalesOrderId($data['sales_order_id']);

$status = Sales_Order_ExportStatus::GENERATING;
$taskInfo   = Sales_Order_Export_Task::getBySalesOrderId($data['sales_order_id']);
if (!empty($taskInfo) && $taskInfo['export_status'] == $status) {
        
    echo    json_encode(array(
        'code'      => 1,
        'message'   => "该订单正在生成下载文件，无法操作",
        'data'      => array(
        ),
    ));
    exit;

}else if(!empty($taskInfo)){
    Sales_Order_Export_Task::update(array(
        'task_id'           => $taskInfo['task_id'],
        'export_status'     => Sales_Order_ExportStatus::WAITING,
    ));
}

if(!empty($listProduceOrderInfo)){
    
    $condition['list_product_id']   = $listProductId;
    $condition['list_produce_order_id'] = ArrayUtility::listField($listProduceOrderInfo,'produce_order_id');
    $produceOrderProductInfo = Produce_Order_Product_List::listByCondition($condition,array(),0,1);
    
    if(!empty($produceOrderProductInfo)){
        $produceOrderId = $produceOrderProductInfo[0]['produce_order_id'];
        $indexProduceOrderId    = ArrayUtility::indexByField($listProduceOrderInfo,'produce_order_id');

        echo    json_encode(array(
            'code'      => 1,
            'message'   => "本产品已创建生产订单，无法删除。关联生产订单编号：". $indexProduceOrderId[$produceOrderId]['produce_order_sn'] ."，请将产品从生产订单中删除，或删除生产订单",
            'data'      => array(
            ),
        ));
        exit;
    }
}

foreach($data['goods_id'] as $id){

    Sales_Order_Goods_Info::getBySkuIdAndSalesOrderIdDelete($data['sales_order_id'],$id);
}

$salesSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($data['sales_order_id']);

Sales_Order_Info::update(array(
        'sales_order_id'    => $data['sales_order_id'],
        'count_goods'       => count($salesSkuInfo),
        'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
        'update_time'       => date('Y-m-d H:i:s', time()),
        'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
    )
);
echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'count' => count($salesSkuInfo),
    ),
));