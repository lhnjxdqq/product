<?php
class Goods_List {

    /**
     * 根据条件查询数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array
     */
    static public function listByCondition (array $condition, array $orderBy = array(), $offset = null, $limit = null) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `goods_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlOrder . $sqlLimit;

        return          Goods_Info::query($sql);
    }

    /**
     * 根据条件统计数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `goods_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Goods_Info::query($sql);
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

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据SKU删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  '`goods_info`.`delete_status` = "' . (int) $condition['delete_status'] . '"';
    }

    /**
     * 拼接ORDER BY子句
     *
     * @param array $order
     * @return string
     */
    static private function _order (array $order) {

        return  ' ORDER BY `goods_info`.`goods_id` DESC';
    }

    /**
     * 拼接LIMIT子句
     *
     * @param $offset   位置
     * @param $limit    数量
     * @return string
     */
    static private function _limit ($offset, $limit) {

        return  (null === $offset || null === $limit)
                ? ''
                : ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables () {

        return  array(
            '`goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`goods_info`.`goods_id` AND `material_info`.`spec_id`=1',
            '`goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id`=`goods_info`.`goods_id` AND `size_info`.`spec_id`=2',
            '`goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`goods_info`.`goods_id` AND `color_info`.`spec_id`=3',
            '`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id`=4',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`goods_info`.`goods_id`',
            '`goods_info`.`goods_sn`',
            '`goods_info`.`goods_name`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`size_info`.`spec_value_id` AS `size_value_id`',
            '`color_info`.`spec_value_id` AS `color_value_id`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
            '`goods_info`.`category_id`',
            '`goods_info`.`self_cost`',
            '`goods_info`.`sale_cost`',
        );
    }
}