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

    static public function getSpuBySourceCode ($sourceCode, array $multiColorValueId) {

        $colorSpecInfo          = Spec_Info::getByAlias('color');
        $colorSpecId            = $colorSpecInfo['spec_id'];
        $sourceCode             = addslashes(trim($sourceCode));
        $multiColorValueId      = array_map('intval', array_unique($multiColorValueId));
        $multiColorValueIdStr   = implode('","', $multiColorValueId);
        $deleteStatus           = Spu_DeleteStatus::NORMAL;
        $onlineStatus           = Spu_OnlineStatus::ONLINE;
        $sql                    =<<<SQL
SELECT
    *
FROM
(SELECT
    `source_info`.`source_code`,
    `color_info`.`spec_value_id` AS `color_value_id`,
    `spu_info`.`spu_id`,
    `product_info`.`product_cost`
FROM
    `product_info`
LEFT JOIN
    `source_info` ON `source_info`.`source_id`=`product_info`.`source_id`
LEFT JOIN
    `goods_info` ON `goods_info`.`goods_id`=`product_info`.`goods_id`
LEFT JOIN
    `goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`goods_info`.`goods_id` AND `color_info`.`spec_id`="{$colorSpecId}"
LEFT JOIN
    `spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id`=`product_info`.`goods_id`
LEFT JOIN
    `spu_info` ON `spu_info`.`spu_id`=`sgr`.`spu_id`
WHERE
    `source_info`.`source_code` = "{$sourceCode}"
AND
    `color_info`.`spec_value_id` IN ("{$multiColorValueIdStr}")
AND
    `spu_info`.`delete_status`="{$deleteStatus}"
AND
    `spu_info`.`online_status`="{$onlineStatus}"
ORDER BY
    `product_info`.`product_cost` ASC
) AS `alias`
GROUP BY
    `spu_id`,`color_value_id`
SQL;

        return                  self::_query($sql);
    }

    /**
     * 获取SPU详情
     *
     * @param $spuId
     * @return array
     */
    static public function getSpuDetailById ($spuId) {

        $spuId  = (int) $spuId;
        $sql    =<<<SQL
SELECT
    `spu_info`.`spu_id`,
    `spu_info`.`spu_sn`,
    `spu_info`.`spu_name`,
    `spu_info`.`spu_remark`,
    `category_info`.`category_id`,
    `category_info`.`category_name`,
    `weight_info`.`spec_value_id` AS `weight_value_id`,
    `weight_value_info`.`spec_value_data` AS `weight_value_data`,
    `size_info`.`spec_value_id` AS `size_value_id`,
    `size_value_info`.`spec_value_data` AS `size_value_data`,
    `material_info`.`spec_value_id` AS `material_value_id`,
    `material_value_info`.`spec_value_data` AS `material_value_data`
FROM
    `spu_info`
LEFT JOIN
    `spu_goods_relationship` AS `sgr` ON `sgr`.`spu_id`=`spu_info`.`spu_id`
LEFT JOIN
    `goods_info` ON `goods_info`.`goods_id`=`sgr`.`goods_id`
LEFT JOIN
    `category_info` ON `category_info`.`category_id`=`goods_info`.`category_id`
LEFT JOIN
    `goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id`=4
LEFT JOIN
    `goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id`=`goods_info`.`goods_id` AND `size_info`.`spec_id`=2
LEFT JOIN
    `goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`goods_info`.`goods_id` AND `material_info`.`spec_id`=1
LEFT JOIN
    `spec_value_info` AS `weight_value_info` ON `weight_value_info`.`spec_value_id`=`weight_info`.`spec_value_id`
LEFT JOIN
    `spec_value_info` AS `size_value_info` ON `size_value_info`.`spec_value_id`=`size_info`.`spec_value_id`
LEFT JOIN
    `spec_value_info` AS `material_value_info` ON `material_value_info`.`spec_value_id`=`material_info`.`spec_value_id`
WHERE
    `spu_info`.`spu_id` = "{$spuId}"
SQL;

        $spuDetail      = self::_query($sql);
        $current        = current($spuDetail);
        if (!$current) {

            return      array();
        }
        $result         = array(
            'spu_id'                    => $current['spu_id'],
            'spu_sn'                    => $current['spu_sn'],
            'spu_name'                  => $current['spu_name'],
            'spu_remark'                => $current['spu_remark'],
            'category_id'               => $current['category_id'],
            'category_name'             => $current['category_name'],
            'weight_value_data_list'    => array(),
            'size_value_data_list'      => array(),
            'material_value_data_list'  => array(),
        );
        foreach ($spuDetail as $detail) {

            if (!in_array($detail['weight_value_data'], $result['weight_value_data_list'])) {

                $result['weight_value_data_list'][]     = $detail['weight_value_data'];
            }

            if (!in_array($detail['size_value_data'], $result['size_value_data_list'])) {

                $result['size_value_data_list'][]       = $detail['size_value_data'];
            }

            if (!in_array($detail['material_value_data'], $result['material_value_data_list'])) {

                $result['material_value_data_list'][]   = $detail['material_value_data'];
            }
        }
        $result['image_url'] = '';
        $mapSpuImage    = self::getGoodsThumbnail((array) $spuId);
        if ($mapSpuImage[$spuId]) {

            $result['image_url'] = $mapSpuImage[$spuId]['image_url'];
        }
        return  $result;
    }

    /**
     * 取一组SPU的缩略图 (主图)
     *
     * @param array $multiSpuId
     * @return array|void
     */
    static public function getGoodsThumbnail (array $multiSpuId) {

        if (empty($multiSpuId)) {

            return;
        }
        $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($multiSpuId);
        $groupSpuImages = ArrayUtility::groupByField($listSpuImages, 'spu_id');
        $result         = array();
        foreach ($groupSpuImages as $spuId => $spuImagesList) {

            if(!empty($spuImagesList)){
                
                $spuThumb = ArrayUtility::searchBy($spuImagesList,array('is_first_picture' => 1));
            }
            if(!empty($spuThumb) && count($spuThumb) ==1){
                
                $info = current($spuThumb);
                $spuThumb['image_url']  = !empty($spuThumb)
                    ? AliyunOSS::getInstance('images-spu')->url($info['image_key'])
                    : '';       
            }else{

                $spuThumb = Sort_Image::sortImage($spuImagesList);

                $spuThumb['image_url']  = !empty($spuThumb)
                    ? AliyunOSS::getInstance('images-spu')->url($spuThumb[0]['image_key'])
                    : '';     
            }
            $result[$spuId]         = $spuThumb;
        }
        return  $result;
    }

    static private function _query ($sql) {

        return  Spu_Info::query($sql);
    }
}