<?php
class Common_Goods {

    /**
     * 查询一组SKU的具体规格和规格值
     *
     * @param array $multiGoodsId
     * @return array
     */
    static public function getMultiGoodsSpecValue (array $multiGoodsId) {

        $multiGoodsId       = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $multiGoodsIdStr    = implode('","', $multiGoodsId);
        $sql                =<<<SQL
SELECT
    `goods_info`.`goods_id`,
    `goods_info`.`goods_sn`,
    `goods_info`.`goods_name`,
    `material_info`.`spec_value_id` AS `material_value_id`,
    `size_info`.`spec_value_id` AS `size_value_id`,
    `color_info`.`spec_value_id` AS `color_value_id`,
    `weight_info`.`spec_value_id` AS `weight_value_id`,
    `material_value_info`.`spec_value_data` AS `material_value_data`,
    `size_value_info`.`spec_value_data` AS `size_value_data`,
    `color_value_info`.`spec_value_data` AS `color_value_data`,
    `weight_value_info`.`spec_value_data` AS `weight_value_data`
FROM
    `goods_info`
LEFT JOIN `goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`goods_info`.`goods_id` AND `material_info`.`spec_id`=1
LEFT JOIN `goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id`=`goods_info`.`goods_id` AND `size_info`.`spec_id`=2
LEFT JOIN `goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`goods_info`.`goods_id` AND `color_info`.`spec_id`=3
LEFT JOIN `goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id`=4
LEFT JOIN `spec_value_info` AS `material_value_info` ON `material_value_info`.`spec_value_id`=`material_info`.`spec_value_id`
LEFT JOIN `spec_value_info` AS `size_value_info` ON `size_value_info`.`spec_value_id`=`size_info`.`spec_value_id`
LEFT JOIN `spec_value_info` AS `color_value_info` ON `color_value_info`.`spec_value_id`=`color_info`.`spec_value_id`
LEFT JOIN `spec_value_info` AS `weight_value_info` ON `weight_value_info`.`spec_value_id`=`weight_info`.`spec_value_id`
WHERE
    `goods_info`.`goods_id` IN ("{$multiGoodsIdStr}")
SQL;

        return      self::_query($sql);
    }

    static public function getGoodsSourceCodeList (array $multiGoodsId) {

        $multiGoodsId     = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $multiGoodsIdStr  = implode('","', $multiGoodsId);
        $sql            =<<<SQL
SELECT
  `gi`.`goods_sn`,
  `gi`.`goods_id`,
  `soi`.`source_code`
FROM
  `source_info` AS `soi`
LEFT JOIN
  `product_info` AS `pi` ON `pi`.`source_id`=`soi`.`source_id`
LEFT JOIN
  `goods_info`  AS `gi` ON  `gi`.`goods_id`=`pi`.`goods_id`
WHERE
  `gi`.`goods_id` IN ("{$multiGoodsIdStr}") 
GROUP BY
	`gi`.`goods_id`
SQL;

        return      self::_query($sql);
    }

    /**
     * 获取一组SKU的分类和款式信息
     *
     * @param array $multiGoodsId
     * @return array
     */
    static public function getMultiGoodsDetail (array $multiGoodsId) {

        $multiGoodsId       = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $multiGoodsIdStr    = implode('","', $multiGoodsId);
        $sql                =<<<SQL
SELECT
  `goods_info`.`goods_id`,
  `goods_info`.`goods_sn`,
  `goods_info`.`goods_name`,
  `goods_info`.`category_id`,
  `goods_info`.`style_id`,
  `goods_info`.`self_cost`,
  `goods_info`.`sale_cost`,
  `category_info`.`category_name`,
  `style_info`.`style_name`
FROM
  `goods_info`
LEFT JOIN
  `category_info` ON `category_info`.`category_id`=`goods_info`.`category_id`
LEFT JOIN 
  `style_info` ON `style_info`.`style_id`=`goods_info`.`style_id`
WHERE
  `goods_info`.`goods_id` IN ("{$multiGoodsIdStr}")
SQL;

        return              self::_query($sql);
    }

    /**
     * 查询一组SKU 分别可以由哪些供应商来生产
     *
     * @param array $multiGoodsId   一组SKUID
     * @return array
     */
    static public function getSkuSupplier (array $multiGoodsId) {

        $multiGoodsId       = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $multiGoodsIdStr    = implode('","', $multiGoodsId);
        $sql                =<<<SQL
SELECT
  `supplier_info`.`supplier_id`,
  `supplier_info`.`supplier_code`,
  `goods_info`.`goods_id`
FROM
  `supplier_info`
LEFT JOIN
  `source_info` ON `source_info`.`supplier_id`=`supplier_info`.`supplier_id`
LEFT JOIN
  `product_info` ON `product_info`.`source_id`=`source_info`.`source_id`
LEFT JOIN
  `goods_info` ON `goods_info`.`goods_id`=`product_info`.`goods_id`
WHERE
  `goods_info`.`goods_id` IN ("{$multiGoodsIdStr}")
ORDER BY
  `supplier_info`.`supplier_sort` DESC
SQL;
        $data               = Goods_Info::query($sql);
        $result             = ArrayUtility::groupByField($data, 'supplier_id');
        return              $result;
    }

    /**
     * 取一组SKU的缩略图 (最后一张)
     *
     * @param array $multiGoodsId
     * @return array|void
     */
    static public function getGoodsThumbnail (array $multiGoodsId) {

        if (empty($multiGoodsId)) {

            return;
        }
        $listGoodsImages    = Goods_Images_RelationShip::getByMultiGoodsId($multiGoodsId);
        $groupGoodsImages   = ArrayUtility::groupByField($listGoodsImages, 'goods_id');
        $result             = array();
        foreach ($groupGoodsImages as $goodsId => $goodsImagesList) {

            $goodsThumb = array_pop($goodsImagesList);
            $imageKey   = $goodsThumb['image_key'];
            $imageUrl   = $imageKey ? AliyunOSS::getInstance('images-sku')->url($imageKey) : '';
            $goodsThumb['image_url']    = $imageUrl;
            $result[$goodsId]           = $goodsThumb;
        }
        return  $result;
    }

    static private function _query ($sql) {

        return  Goods_Info::query($sql);
    }
}