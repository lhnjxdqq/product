<?php
class Produce_Order_Product_List {

    /**
     * 根据条件查询数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param $offset           位置
     * @param $limit            数量
     * @return array
     */
    static public function listByCondition (array $condition, array $orderBy, $offset, $limit) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `produce_order_product_info` AS `popi` LEFT JOIN';
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
     * @param array $condition
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `produce_order_product_info` AS `popi` LEFT JOIN';
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
        $sql[]      = self::_conditionByProduceOrderId($condition);
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sql[]      = self::_conditionByMultiProductId($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter)
                    ? ''
                    : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据生产订单ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByProduceOrderId (array $condition) {

        return  isset($condition['produce_order_id'])
                ? '`popi`.`produce_order_id` = "' . (int) $condition['produce_order_id'] . '"'
                : '';
    }

    /**
     * 根据产品ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiProductId (array $condition) {

        if(empty($condition['list_product_id'])){
            
            return ;
        }
        $multiProductId = array_map('intval', array_unique(array_filter($condition['list_product_id'])));
        
        return '`pi`.`product_id` IN ("' . implode('","', $multiProductId) . '")';
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  isset($condition['delete_status'])
                ? '`popi`.`delete_status` = "' . (int) $condition['delete_status'] . '"'
                : '';
    }

    /**
     * 拼接ORDER BY子句
     *
     * @param array $orderBy    排序
     * @return string
     */
    static private function _order (array $orderBy) {

        return  ' ORDER BY `popi`.`product_id` ASC';
    }

    /**
     * 拼接LIMIT子句
     *
     * @param $offset   位置
     * @param $limit    索引
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
            '`product_info` AS `pi` ON `pi`.`product_id`=`popi`.`product_id`',
            '`source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id`',
            '`goods_info` AS `gi` ON `gi`.`goods_id`=`pi`.`goods_id`',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`pi`.`product_id`',
            '`pi`.`product_sn`',
            '`pi`.`product_cost`',
            '`si`.`source_code`',
            '`gi`.`goods_id`',
            '`gi`.`goods_sn`',
            '`gi`.`goods_name`',
            '`gi`.`category_id`',
            '`gi`.`style_id`',
            '`popi`.`remark`',
            '`popi`.`quantity`',
        );
    }

    static private function _query ($sql) {

        return  Produce_Order_Product_Info::query($sql);
    }
}