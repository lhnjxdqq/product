<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['produce_order_id'])) {

    Utility::notice('produce_order_id is missing');
}

// 生产订单
$produceOrderId     = (int) $_GET['produce_order_id'];
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);

if (!$produceOrderInfo) {

    Utility::notice('生产订单不存在');
}

$mapProduceOrderArriveInfo = Produce_Order_Arrive_Info::getByProduceOrderId($produceOrderId);
$productInfo               = Produce_Order_Product_Info::getByProduceOrderId($produceOrderId);
$countProduct              = count($productInfo);
$productTotal              = array_sum(ArrayUtility::listField($productInfo,'quantity'));
//到货重量
$weightTotal               = array_sum(ArrayUtility::listField($mapProduceOrderArriveInfo,'weight_total'));
//到货数量
$quantityTotal             = array_sum(ArrayUtility::listField($mapProduceOrderArriveInfo,'quantity_total'));
//到货款式
$countProductTotal         = array_sum(ArrayUtility::listField($mapProduceOrderArriveInfo,'count_product'));
//入库次数
$countStorage              = count(ArrayUtility::searchBy($mapProduceOrderArriveInfo,array('is_storage'=>1)));
// 生产订单详情

$listOrderProduct   = Produce_Order_List::getDetailByMultiProduceOrderId((array) $produceOrderId);

$listGoodsId        = ArrayUtility::listField($listOrderProduct, 'goods_id');
$listGoodsSpecValue = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
$mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
$produceOrderInfo['count_weight']   = 0;
foreach ($listOrderProduct as $orderProduct) {

    $goodsId            = $orderProduct['goods_id'];
    $quantity           = $orderProduct['quantity'];
    $weightValueData    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
    $countWeight+= $quantity * $weightValueData;
}


$template = Template::getInstance();

$template->assign('produceOrderId',$produceOrderId);
$template->assign('countWeight',$countWeight);
$template->assign('quantityTotal',$quantityTotal);
$template->assign('countProductTotal',$countProductTotal);
$template->assign('countStorage',$countStorage);
$template->assign('weightTotal',$weightTotal);
$template->assign('productTotal',$productTotal);
$template->assign('countProduct',$countProduct);
$template->assign('produceOrderInfo',$produceOrderInfo);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapProduceOrderArriveInfo', $mapProduceOrderArriveInfo);
$template->display('order/produce/storage.tpl');