<?php
class Common_Spu {

    /**
     * 获取一组SKU的 SKU-SPU关系
     *
     * @param array $multiGoodsId
     * @return array|void
     */
    static public function getGoodsSpu (array $multiGoodsId) {

        if (empty($multiGoodsId)) {

            return;
        }
        $listGoodsSpu   = Spu_Goods_RelationShip::getByMultiGoodsId($multiGoodsId);
        $listSpuId      = array_unique(ArrayUtility::listField($listGoodsSpu, 'spu_id'));
        $listSpuInfo    = Spu_Info::getByMultiId($listSpuId);
        $mapSpuInfo     = ArrayUtility::indexByField($listSpuInfo, 'spu_id');
        $groupGoodsSpu  = ArrayUtility::groupByField($listGoodsSpu, 'goods_id');
        $result         = array();
        foreach ($groupGoodsSpu as $goodsId => $goodsSpuList) {

            $temp   = array();
            foreach ($goodsSpuList as $goodsSpu) {

                $spuId  = $goodsSpu['spu_id'];
                $temp[] = $mapSpuInfo[$spuId];
            }
            $result[$goodsId]   = $temp;
        }
        return  $result;
    }

    static private function _query ($sql) {

        return  Spu_Info::query($sql);
    }
}