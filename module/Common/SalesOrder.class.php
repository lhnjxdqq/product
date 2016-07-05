<?php
class Common_SalesOrder {

    /**
     * @param $salesOrderId
     * @return array
     */
    static public function getProduceOrderDetail ($salesOrderId) {

        $sql    =<<<SQL
SELECT
  `soi`.`sales_order_id`,
  `soi`.`sales_order_sn`,
  `poi`.`produce_order_id`,
  `poi`.`produce_order_sn`,
  `popi`.`product_id`,
  `popi`.`quantity`,
  `popi`.`remark`,
  `pi`.`goods_id`
FROM
  `sales_order_info` AS `soi`
LEFT JOIN
  `produce_order_info` AS `poi` ON `poi`.`sales_order_id`=`soi`.`sales_order_id`
LEFT JOIN
  `produce_order_product_info` AS `popi` ON `popi`.`produce_order_id`=`poi`.`produce_order_id`
LEFT JOIN
  `product_info` AS `pi` ON `pi`.`product_id`=`popi`.`product_id`
WHERE
  `soi`.`sales_order_id`="{$salesOrderId}"
ORDER BY
  `poi`.`produce_order_id` ASC
SQL;

        return  self::_query($sql);
    }

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}