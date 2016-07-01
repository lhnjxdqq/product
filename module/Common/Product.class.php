<?php
class Common_Product {

    /**
     * 检测一组SKUID中需要下架的SKU
     *
     * @param $multiGoodsId 一组SKUID
     * @return array        需要下架的SKU
     */
    static public function getOffLineGoods ($multiGoodsId) {

        // 先查出这些SKU下所有的产品, 按SKUID分组
        $listProductInfo    = Product_Info::getByMultiGoodsId($multiGoodsId);
        $groupProductInfo   = ArrayUtility::groupByField($listProductInfo, 'goods_id');
        // 分别检查每个SKU下的产品, 如果没有状态正常的产品(未下架并且未删除), 那么该SKU下架
        $offLineGoodsList   = array();
        foreach ($groupProductInfo as $goodsId => $productInfoList) {

            $normalGoodsList    = ArrayUtility::searchBy($productInfoList, array(
                'online_status' => Product_OnlineStatus::ONLINE,
                'delete_status' => Product_DeleteStatus::NORMAL,
            ));
            empty($normalGoodsList) && $offLineGoodsList[]  = $goodsId;
        }
        return  $offLineGoodsList;
    }

    /**
     * 检测一组SPUID中需要下架的SPU
     *
     * @param $multiSpuId   一组SPUID
     * @param array         需要下架的SPU
     */
    static public function getOffLineSpu ($multiSpuId) {

        // 先查这些SPU关联的SKU
        $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($multiSpuId);
        $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
        $listGoodsId    = array_unique(ArrayUtility::listField($listSpuGoods, 'goods_id'));
        // 查出SKU的信息 如果SKU状态正常(非下架且未删除), 则status=1 否则status=0
        $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
        foreach ($listGoodsInfo as &$goodsInfo) {

            $status = ($goodsInfo['online_status'] == Goods_OnlineStatus::ONLINE) && ($goodsInfo['delete_status'] == Goods_DeleteStatus::NORMAL)
                      ? 1
                      : 0;
            $goodsInfo['status']    = $status;
        }
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        // 遍历每个SPU下的SKU状态, 如果SPU下的所有SKU status=0 那么SPU下架
        $offLineSpuList = array();
        foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

            foreach ($spuGoodsList as $key => $spuGoods) {

                $goodsId    = $spuGoods['goods_id'];
                $spuGoodsList[$key]['status']   = $mapGoodsInfo[$goodsId]['status'];
            }
            $normalList = ArrayUtility::searchBy($spuGoodsList, array('status'=>1));
            empty($normalList) && $offLineSpuList[] = $spuId;
        }
        return  $offLineSpuList;
    }

    /**
     * 根据一组SPU内SKU的状态 获取该组SPU的操作状态
     *
     * @param array $multiSpuId
     * @return array
     */
    static public function getSpuPendingStatus (array $multiSpuId) {

        $listSpuGoods       = Spu_Goods_RelationShip::getByMultiSpuId($multiSpuId);
        $groupSpuGoods      = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
        $listGoodsId        = array_unique(ArrayUtility::listField($listSpuGoods, 'goods_id'));
        $listGoodsInfo      = Goods_Info::getByMultiId($listGoodsId);
        $mapGoodsInfo       = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $listToOfflineSpu   = array();
        $listToDeletedSpu   = array();
        foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

            $count              = count($spuGoodsList);
            $listNormalGoods    = array();
            $listDeletedGoods   = array();
            foreach ($spuGoodsList as $spuGoods) {

                $goodsId    = $spuGoods['goods_id'];
                $isDeleted  = $mapGoodsInfo[$goodsId]['delete_status'] == Goods_DeleteStatus::DELETED;
                $isNormal   = ($mapGoodsInfo[$goodsId]['delete_status'] == Goods_DeleteStatus::NORMAL)
                              &&
                              ($mapGoodsInfo[$goodsId]['online_status'] == Goods_OnlineStatus::ONLINE);
                $isDeleted  && $listDeletedGoods[]  = $goodsId;
                $isNormal   && $listNormalGoods[]   = $goodsId;
            }
            empty($listNormalGoods)                 &&  $listToOfflineSpu[] = $spuId;
            (count($listDeletedGoods) == $count)    &&  $listToDeletedSpu[] = $spuId;
        }
        return  array(
            'pendingOfflineList' => $listToOfflineSpu,
            'pendingDeletedList' => $listToDeletedSpu,
        );
    }
}