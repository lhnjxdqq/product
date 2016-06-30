<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$productIdList  = array();

if (isset($_GET['product_id'])) {

    $productIdList  = (array) $_GET['product_id'];
} elseif (isset($_GET['multi_product_id'])) {

    $productIdList  = explode(',', $_GET['multi_product_id']);
}

$onlineStatus   = trim($_GET['online_status']) == 'offline'
                  ? Product_OnlineStatus::OFFLINE
                  : Product_OnlineStatus::ONLINE;

if (Product_Info::setOnlineStatusByMultiProductId($productIdList, $onlineStatus)) {

    // 获取需要下架的SKU列表
    $listProductInfo    = Product_Info::getByMultiId($productIdList);
    $listGoodsId        = array_unique(ArrayUtility::listField($listProductInfo, 'goods_id'));
    $listOffLineGoodsId = Common_Product::getOffLineGoods($listGoodsId);
    if (!empty($listOffLineGoodsId)) {
        // 执行SKU下架操作
        Goods_Info::setOnlineStatusByMultiGoodsId($listOffLineGoodsId, Goods_OnlineStatus::OFFLINE);

        // 查这些下架的SKU关联了哪些SPU
        $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($listOffLineGoodsId);
        $listSpuId          = array_unique(ArrayUtility::listField($listSpuGoods, 'spu_id'));
        // 获取需要下架的SPU列表, 并且对SPU进行下架
        $listOffLineSpuId   = Common_Product::getOffLineSpu($listSpuId);
        !empty($listOffLineSpuId) && Spu_Info::setOnlineStatusByMultiSpuId($listOffLineSpuId, Spu_OnlineStatus::OFFLINE);
    }
}