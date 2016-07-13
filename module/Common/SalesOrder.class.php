<?php
class Common_SalesOrder {

    /**
     * 获取一个生产订单中 已经生产了的SKU
     *
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

    /**
     * 查询销售订单中 已生产完成的SKU
     *
     * @param $salesOrderId
     * @return array
     */
    static public function getProducedGoods ($salesOrderId) {

        $listProduceOrder       = Produce_Order_Info::getBySalesOrderId($salesOrderId);
        $listProduceOrderId     = ArrayUtility::listField($listProduceOrder, 'produce_order_id');
        $listProduceOrderIdStr  = implode('","', $listProduceOrderId);
        $sql                    =<<<SQL
SELECT
  `pi`.`goods_id`,
  SUM(`popi`.`quantity`) AS `sum_quantity_produce`,
  `sogi`.`goods_quantity` AS `sales_goods_quantity`
FROM
  `product_info` AS `pi`
LEFT JOIN
  `produce_order_product_info` AS `popi` ON `popi`.`product_id`=`pi`.`product_id`
LEFT JOIN
  `sales_order_goods_info` AS `sogi` ON `sogi`.`goods_id`=`pi`.`goods_id`
WHERE
  `popi`.`produce_order_id` IN ("{$listProduceOrderIdStr}")
AND 
  `sogi`.`sales_order_id` = "{$salesOrderId}"
GROUP BY
  `pi`.`goods_id`
HAVING
  `sum_quantity_produce` >= `sales_goods_quantity`
SQL;
        return  self::_query($sql);
    }

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}