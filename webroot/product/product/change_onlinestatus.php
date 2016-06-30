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

    $listProductInfo    = Product_Info::getByMultiId($productIdList);
    $listGoodsId        = array_unique(ArrayUtility::listField($listProductInfo, 'goods_id'));
    if (trim($_GET['online_status']) == 'offline') {// 产品下架逻辑
        // 获取需要下架的SKU列表
        $listOffLineGoodsId = Common_Product::getOffLineGoods($listGoodsId);
        if (!empty($listOffLineGoodsId)) {
            // 执行SKU下架操作
            Goods_Info::setOnlineStatusByMultiGoodsId($listOffLineGoodsId, Goods_OnlineStatus::OFFLINE);
            foreach ($listOffLineGoodsId as $goodsId) {

                Goods_Push::changePushGoodsDataStatus($goodsId, 'offline');
            }

            // 查这些下架的SKU关联了哪些SPU
            $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($listOffLineGoodsId);
            $listSpuId          = array_unique(ArrayUtility::listField($listSpuGoods, 'spu_id'));
            // 获取需要下架的SPU列表, 并且对SPU进行下架
            $listOffLineSpuId   = Common_Product::getOffLineSpu($listSpuId);
            !empty($listOffLineSpuId) && Spu_Info::setOnlineStatusByMultiSpuId($listOffLineSpuId, Spu_OnlineStatus::OFFLINE);
        }
        Utility::notice('下架成功');
    } elseif (trim($_GET['online_status']) == 'online') {// 产品上架逻辑

        $listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
        // 获取需要上架的SKU
        $listOnlineGoodsInfo    = ArrayUtility::searchBy($listGoodsInfo, array('online_status'=>Goods_OnlineStatus::OFFLINE));
        $listOnlineGoodsId      = ArrayUtility::listField($listOnlineGoodsInfo, 'goods_id');
        if (!empty($listOnlineGoodsId)) {

            // 执行SKU上架操作
            Goods_Info::setOnlineStatusByMultiGoodsId($listOnlineGoodsId, Goods_OnlineStatus::ONLINE);
            foreach ($listOnlineSpuId as $goodsId) {

                Goods_Push::changePushGoodsDataStatus($goodsId, 'online');
            }

            // 查这些SKU关联了哪些SPU
            $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($listOnlineGoodsId);
            $listSpuId          = array_unique(ArrayUtility::listField($listSpuGoods, 'spu_id'));
            // 获取需要上架的SPU
            $listSpuInfo        = Spu_Info::getByMultiId($listSpuId);
            $listOnlineSpuInfo  = ArrayUtility::searchBy($listSpuInfo, array('online_status'=>Spu_OnlineStatus::OFFLINE));
            $listOnlineSpuId    = ArrayUtility::listField($listOnlineSpuInfo, 'spu_id');
            !empty($listOnlineSpuId) && Spu_Info::setOnlineStatusByMultiSpuId($listOnlineSpuId, Spu_OnlineStatus::ONLINE);
        }
        Utility::notice('上架成功');
    } else {

        Utility::notice('操作错误');
    }
} else {

    Utility::notice('操作失败');
}