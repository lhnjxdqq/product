<?php
class Common_ProduceOrder {

    /**
     * 查询生产订单的信息
     *
     * @param $produceOrderId
     * @return array
     */
    static public function getOrderDetail ($produceOrderId) {

        $produceOrderId = (int) $produceOrderId;
        $sql    =<<<SQL
SELECT 
  `poi`.`produce_order_id`,
  `popi`.`product_id`,
  `popi`.`quantity`,
  `popi`.`remark`,
  `pi`.`product_sn`,
  `pi`.`goods_id`,
  `pi`.`product_cost`,
  `pi`.`source_id`,
  `si`.`source_code`
FROM 
  `produce_order_info` AS `poi`
LEFT JOIN
  `produce_order_product_info` AS `popi` ON `poi`.`produce_order_id`=`popi`.`produce_order_id`
LEFT JOIN
  `product_info` AS `pi` ON `pi`.`product_id`=`popi`.`product_id`
LEFT JOIN
  `source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id`
WHERE
  `poi`.`produce_order_id`="{$produceOrderId}";
SQL;

        return  self::_query($sql);
    }
    /**
     * 查询生产订单的信息
     *
     * @param $produceOrderId
     * @return array
     */
    static public function getOrderSpuDetail ($produceOrderId) {

        $produceOrderId = (int) $produceOrderId;
        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias', 'spec_id');
        $weightId       = $mapSpecInfo['weight'];
        $sizeId         = $mapSpecInfo['size'];
        $colorId        = $mapSpecInfo['color'];
        $materialId     = $mapSpecInfo['material'];
        $assistantMaterial = $mapSpecInfo['assistant_material'];

        $sql    =<<<SQL
SELECT
    `pi`.`product_cost`,
    `gi`.`style_id`,
    `spu_info`.`spu_sn`,
    `si`.`source_code`,
    `gi`.`goods_id`,
    `color_info`.`spec_value_id` AS `color_value_id`,
    `assistant_material_info`.`spec_value_id` AS `assistant_material_value_id`,
    `size_info`.`spec_value_id` AS `size_value_id`,
    `weight_info`.`spec_value_id` AS `weight_value_id`,
    `material_info`.`spec_value_id` AS `material_value_id`,
    `gi`.`category_id`,
    `popi`.`produce_order_id`,
    SUM(`popi`.`quantity`) as `total_quantity`
FROM
    `produce_order_product_info` AS `popi`
LEFT JOIN `product_info` AS `pi` ON `pi`.`product_id` = `popi`.`product_id`
LEFT JOIN `source_info` AS `si` ON `si`.`source_id` = `pi`.`source_id`
LEFT JOIN `goods_info` AS `gi` ON `gi`.`goods_id` = `pi`.`goods_id`
LEFT JOIN `goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id` = `pi`.`goods_id`
AND `size_info`.`spec_id` = "{$sizeId}"
LEFT JOIN `goods_spec_value_relationship` AS `assistant_material_info` ON `assistant_material_info`.`goods_id` = `pi`.`goods_id`
AND `assistant_material_info`.`spec_id` = "{$assistantMaterial}"
LEFT JOIN `goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id` = `pi`.`goods_id`
AND `weight_info`.`spec_id` = "{$weightId}"
LEFT JOIN `goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id` = `pi`.`goods_id`
AND `color_info`.`spec_id` = "{$colorId}"
LEFT JOIN `goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id` = `pi`.`goods_id`
AND `material_info`.`spec_id` = "{$materialId}"
LEFT JOIN `spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id` = `gi`.`goods_id`
LEFT JOIN `spu_info` ON `spu_info`.`spu_id` = `sgr`.`spu_id`
WHERE
    `popi`.`produce_order_id` = "{$produceOrderId}"
GROUP BY
    `si`.`source_id`,
    `color_value_id`,
    `size_value_id`,
    `spu_info`.`spu_id`
ORDER BY
    `popi`.`product_id` ASC;
SQL;

        return  self::_query($sql);
    }

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}