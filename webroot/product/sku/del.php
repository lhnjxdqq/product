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

            Spu_Push::changePushSpuDataStatus($spuId, 'offline');
        }
    }
    if (!empty($pendingDeletedList)) {

        Spu_Info::setDeleteStatusByMultiSpuId($pendingDeletedList, Spu_DeleteStatus::DELETED);
        foreach ($pendingDeletedList as $spuId) {

            Spu_Push::changePushSpuDataStatus($spuId, 'delete');
        }
    }
    // 删除此SKU和SPU的关系
    Spu_Goods_RelationShip::deleteRelationShipByMultiGoodsId((array) $goodsId);

    Utility::notice('删除成功', '/product/sku/index.php');
} else {

    Utility::notice('删除失败');
}