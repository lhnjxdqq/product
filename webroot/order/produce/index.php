<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;

$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime(date('Y-m')));
$condition['date_end']      = isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d', strtotime(date('Y-m-d')) + 24 * 3600);
$condition['delete_status'] = Produce_Order_DeleteStatus::NORMAL;

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$page       = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listProduceOrderInfo       = Produce_Order_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
$mapProduceOrderInfo        = ArrayUtility::indexByField($listProduceOrderInfo, 'produce_order_id');
// 统计每个订单的款数 件数 重量
$listProduceOrderId         = ArrayUtility::listField($listProduceOrderInfo, 'produce_order_id');
$listProduceOrderGoods      = Produce_Order_List::getDetailByMultiProduceOrderId($listProduceOrderId);
$listProduceOrderGoodsId    = ArrayUtility::listField($listProduceOrderGoods, 'goods_id');
$listGoodsSpecValue         = Common_Goods::getMultiGoodsSpecValue($listProduceOrderGoodsId);
$mapGoodsSpecValue          = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
$groupProduceOrderGoods     = ArrayUtility::groupByField($listProduceOrderGoods, 'produce_order_id');
$listSupplierInfo           = Supplier_Info::listAll();

foreach ($groupProduceOrderGoods as $produceOrderId => $goodsList) {

    $mapProduceOrderInfo[$produceOrderId]['count_goods']    = count($goodsList);
    $mapProduceOrderInfo[$produceOrderId]['count_quantity'] = array_sum(ArrayUtility::listField($goodsList, 'quantity'));
    $countWeight    = 0;
    foreach ($goodsList as $goods) {
        $goodsId        = $goods['goods_id'];
        $quantity       = $goods['quantity'];
        $weighValueData = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
        $countWeight    += $quantity * $weighValueData;
    }
    $mapProduceOrderInfo[$produceOrderId]['count_weight']   = $countWeight;
}

$data['condition']              = $condition;
$data['mapProduceOrderInfo']    = $mapProduceOrderInfo;
$data['mainMenu']               = Menu_Info::getMainMenu();
$data['pageViewData']           = $page->getViewData();
$data['listSupplierInfo']       = $listSupplierInfo;
$data['mapStatusCode']          = Produce_Order_StatusCode::getProduceOrderStatusList();
$data['listStatusCode']         = array(
    'new_built' => Produce_Order_StatusCode::NEWLY_BUILT,
    'confirmed' => Produce_Order_StatusCode::CONFIRMED,
    'stocking'  => Produce_Order_StatusCode::STOCKING,
    'arrival'   => Produce_Order_StatusCode::ARRIVAL,
    'finished'  => Produce_Order_StatusCode::FINISHED,
);

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/index.tpl');