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
}