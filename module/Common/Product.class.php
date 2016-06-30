<?php
class Common_Product {

    /**
     * 通过删除产品逻辑 -- 删除SKU && 删除 SPU和SKU的关联关系
     *
     * @param $multiProductId   一组产品ID
     */
    static public function deleteByMultiProductId ($multiProductId) {

        $multiProductId         = array_map('intval', array_unique(array_filter($multiProductId)));
        $listProductInfo        = Product_Info::getByMultiId($multiProductId);
        $listGoodsId            = ArrayUtility::listField($listProductInfo, 'goods_id');
        $listAllProductInfo     = Product_Info::getByMultiGoodsId($listGoodsId);
        $groupAllProductInfo    = ArrayUtility::groupByField($listAllProductInfo, 'goods_id');
        $deleteGoodsIdList      = array();
        foreach ($groupAllProductInfo as $goodsId => $productInfoList) {

            $deleteStatusList   = array_unique(ArrayUtility::listField($productInfoList, 'delete_status'));
            $deleteStatus       = current($deleteStatusList);
            if (count($deleteStatusList) == 1 && $deleteStatus == Product_DeleteStatus::DELETED) {

                $deleteGoodsIdList[]    = $goodsId;
            }
        }

        if ($deleteGoodsIdList) {

            Goods_Info::setDeleteStatusByMultiGoodsId($deleteGoodsIdList, Goods_DeleteStatus::DELETED);
            // 查询待删除的SPU
            $deleteSpuIdList    = array();
            $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($deleteGoodsIdList);
            $listSpuId          = array_unique(ArrayUtility::listField($listSpuGoods, 'spu_id'));
            $listAllSpuGoods    = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
            $groupAllSpuGoods   = ArrayUtility::groupByField($listAllSpuGoods, 'spu_id');
            foreach ($groupAllSpuGoods as $spuGoodsList) {

                $spuGoods       = count($spuGoodsList) == 1 ? current($spuGoodsList) : null;
                $deleteSpuId    = $spuGoods ? $spuGoods['spu_id'] : null;
                if (in_array($goodsId, $deleteGoodsIdList)) {

                    $deleteSpuIdList[] = $deleteSpuId;
                } 
            }
            Spu_Goods_RelationShip::deleteRelationShipByMultiGoodsId($deleteGoodsIdList);
            Spu_Info::setDeleteStatusByMultiSpuId($deleteSpuIdList, Spu_DeleteStatus::DELETED);
            // 只需请求选货工具删除SKU接口(选货工具有删除SKU是删除SPU和SKU关系的逻辑)
            foreach ($deleteGoodsIdList as $deleteGoodsId) {

                Goods_Push::deletePushGoodsData($deleteGoodsId);
            }
        }
    }

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
}