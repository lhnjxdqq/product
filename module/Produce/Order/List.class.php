<?php
class Produce_Order_List {

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param $offset           位置
     * @param $limit            数量
     * @return array
     */
    static public function listByCondition (array $condition, array $orderBy, $offset, $limit) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `produce_order_info` AS `poi` LEFT JOIN';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_query($sql);
    }

    /**
     * 根据条件统计数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `produce_order_info` AS `poi` LEFT JOIN';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = self::_query($sql);
        $row            = current($data);

        return          (int) $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter)
                    ? ''
                    : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据生产订单的删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  isset($condition['delete_status'])
                ? '`poi`.`delete_status`="' . (int) $condition['delete_status'] . '"'
                : '';
    }

    /**
     * 拼接排序语句
     *
     * @param array $orderBy    排序
     * @return string
     */
    static private function _order (array $orderBy) {

        return  ' ORDER BY `poi`.`produce_order_id` DESC';
    }

    /**
     * 拼接LIMIT 子句
     *
     * @param $offset   位置
     * @param $limit    数量
     * @return string
     */
    static private function _limit ($offset, $limit) {

        return  ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables () {

        return  array(
            '`sales_order_info` AS `soi` ON `soi`.`sales_order_id`=`poi`.`sales_order_id`',
            '`supplier_info` AS `si` ON `si`.`supplier_id`=`poi`.`supplier_id`',
        );
    }

    /**
     * 根据
     *
     * @param array $multiProducedOrderId
     * @return array
     */
    static public function getDetailByMultiProduceOrderId (array $multiProducedOrderId) {

        $multiProducedOrderId       = array_map('intval', array_unique(array_filter($multiProducedOrderId)));
        $multiProducedOrderIdStr    = implode('","', $multiProducedOrderId);

        $sql                        =<<<SQL
SELECT
  `poi`.`produce_order_id`,
  `popi`.`product_id`,
  `popi`.`quantity`,
  `pi`.`goods_id`
FROM
  `produce_order_info` AS `poi`
LEFT JOIN
  `produce_order_product_info` AS `popi` ON `popi`.`produce_order_id`=`poi`.`produce_order_id`
LEFT JOIN
  `product_info` AS `pi` ON `pi`.`product_id`=`popi`.`product_id`
WHERE
  `poi`.`produce_order_id` IN ("{$multiProducedOrderIdStr}");
SQL;

        return                      self::_query($sql);
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`poi`.`produce_order_id`',
            '`poi`.`produce_order_sn`',
            '`poi`.`supplier_id`',
            '`poi`.`status_code`',
            '`poi`.`create_time`',
            '`soi`.`sales_order_id`',
            '`soi`.`sales_order_sn`',
            '`si`.`supplier_code`',
        );
    }

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}