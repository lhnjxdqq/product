<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['goods_id'], 'goods_id is missing', '/product/sku/index.php');

$goodsId    = (int) $_GET['goods_id'];

$data   = array(
    'goods_id'      => $goodsId,
    'delete_status' => Goods_DeleteStatus::DELETED,
);

if (Goods_Info::update($data)) {

    $listSpuGoods       = Spu_Goods_RelationShip::getByGoodsId($goodsId);
    $listSpuId          = ArrayUtility::listField($listSpuGoods, 'spu_id');
    Sync::queueSkuData($goodsId);
    Goods_Push::changePushGoodsDataStatus($goodsId, 'delete');
    // 获取SPU的状态操作
    $listPendingSpu     = Common_Product::getSpuPendingStatus($listSpuId);
    $pendingOfflineList = $listPendingSpu['pendingOfflineList'];
    $pendingDeletedList = $listPendingSpu['pendingDeletedList'];
    // 对于同时需要下架又需要删除的SPU, 只推删除即可
    $pendingOfflineList = array_diff($pendingOfflineList, array_intersect($pendingOfflineList, $pendingDeletedList));

    if (!empty($pendingOfflineList)) {

        Spu_Info::setOnlineStatusByMultiSpuId($pendingOfflineList, 'offline');
        foreach ($pendingOfflineList as $spuId) {

            Sync::queueSpuData($spuId);
            $spuInfo = Spu_Info::getById($spuId);
            Spu_Push::pushTagsListSpuSn(array($spuInfo['spu_sn']), array('onlineStatus'=>Spu_OnlineStatus::OFFLINE));
            Spu_Push::changePushSpuDataStatus($spuId, 'offline');
        }
    }
    if (!empty($pendingDeletedList)) {

        Spu_Info::setDeleteStatusByMultiSpuId($pendingDeletedList, Spu_DeleteStatus::DELETED);
        foreach ($pendingDeletedList as $spuId) {

            Sync::queueSpuData($spuId);
            $spuInfo = Spu_Info::getById($spuId);
            Spu_Push::pushTagsListSpuSn(array($spuInfo['spu_sn']), array('onlineStatus'=>Spu_OnlineStatus::OFFLINE));
            Spu_Push::changePushSpuDataStatus($spuId, 'delete');
        }
    }
    // 删除此SKU和SPU的关系
    Spu_Goods_RelationShip::deleteRelationShipByMultiGoodsId((array) $goodsId);

    Sync::queueSkuData($goodsId);
    Utility::notice('删除成功', '/product/sku/index.php');
} else {

    Utility::notice('删除失败');
}