<?php
class Common_Goods {

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

    static private function _query ($sql) {

        return  Goods_Info::query($sql);
    }
}