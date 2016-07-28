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

    static public function getSpuSourceCodeList (array $multiSpuId) {

        $multiSpuId     = array_map('intval', array_unique(array_filter($multiSpuId)));
        $multiSpuIdStr  = implode('","', $multiSpuId);
        $sql            =<<<SQL
SELECT
  `spi`.`spu_id`,
  `soi`.`source_code`
FROM
  `source_info` AS `soi`
LEFT JOIN
  `product_info` AS `pi` ON `pi`.`source_id`=`soi`.`source_id`
LEFT JOIN
  `spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id`=`pi`.`goods_id`
LEFT JOIN
  `spu_info` AS `spi` ON `spi`.`spu_id`=`sgr`.`spu_id`
WHERE
  `spi`.`spu_id` IN ("{$multiSpuIdStr}")
GROUP BY
  `soi`.`source_id`,`spi`.`spu_id`
SQL;

        return      self::_query($sql);
    }

    static private function _query ($sql) {

        return  Spu_Info::query($sql);
    }
}