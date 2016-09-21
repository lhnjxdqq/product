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
        $sql[]      = self::_conditionByCreateTimeRange($condition);
        $sql[]      = self::_conditionBySupplierId($condition);
        $sql[]      = self::_conditionByStatusCode($condition);
        $sql[]      = self::_conditionByProduceOrderSn($condition);
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter)
                    ? ''
                    : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据生产订单创建时间拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByCreateTimeRange (array $condition) {

        return  (!$condition['date_start'] || !$condition['date_end'])
                ? ''
                : "`poi`.`create_time` BETWEEN '{$condition['date_start']}' AND '{$condition['date_end']}'";
    }

    /**
     * 根据供应商ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySupplierId (array $condition) {

        return  !$condition['supplier_id']
                ? ''
                : '`si`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"';
    }

    /**
     * 根据生产订单状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByStatusCode (array $condition) {

        return  !$condition['order_status_code']
                ? ''
                : '`poi`.`status_code` = "' . (int) $condition['order_status_code'] . '"';
    }

    /**
     * 根据生产订单编号拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByProduceOrderSn (array $condition) {

        $multiProduceOrderSn    = !$condition['produce_order_sn'] ? array() : explode(' ', trim($condition['produce_order_sn']));
        $multiProduceOrderSn    = array_map('addslashes', array_unique(array_filter($multiProduceOrderSn)));

        return                  empty($multiProduceOrderSn)
                                ? ''
                                : '`poi`.`produce_order_sn` IN ("' . implode('","', $multiProduceOrderSn) . '")';
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
            '`produce_order_export_task` AS `poet` ON `poet`.`produce_order_id`=`poi`.`produce_order_id`',
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
  `pi`.`goods_id`,
  `popi`.`short_quantity`
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
            '`poet`.`export_status`',
            '`poet`.`export_filepath`',
            '`soi`.`sales_order_id`',
            '`soi`.`sales_order_sn`',
            '`si`.`supplier_code`',
        );
    }

    static private function _query ($sql) {

        return  Produce_Order_Info::query($sql);
    }
}