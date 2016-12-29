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

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}