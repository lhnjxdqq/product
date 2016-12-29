<?php
/**
 * 模型 规格值
 */
class   Spec_Value_List {

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, $orderBy = array(), $offset, $limit) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `spec_info` AS `si` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy,$condition);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sqlGroup       = ' GROUP BY `svi`.`spec_value_id` ';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Spec_Value_Info::query($sql);
    }

    /**
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT(`svi`.`spec_value_id`)) AS `cnt` FROM `spec_info` AS `si` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Spec_Value_Info::query($sql);
        $row            = current($data);

        return          $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();

        $sql[]      = self::_conditionSpecId($condition);
        $sql[]      = self::_conditionDeleteStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据报价单ID条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */

    static  private function _conditionSpecId (array $condition) {

        if (empty($condition['spec_id'])) {

            return  '';
        }

        return  "`si`.`spec_id` = '" . addslashes($condition['spec_id']) . "'";
    }

    /**
     * 根据报价单ID条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */

    static  private function _conditionDeleteStatus (array $condition) {

        if (!isset($condition['delete_status'])) {

            return  '';
        }

        return  "`svi`.`delete_status` = '" . addslashes($condition['delete_status']) . "'";
    }

    /**
     * 获取排序子句
     *
     * @param   array   $order  排序依据
     * @return  string          SQL排序子句
     */
    static  private function _order (array $order) {

        $sql    = array();

        foreach ($order as $fieldName => $sequence) {

            $fieldName  = str_replace('`', '', $fieldName);
            $sql[]      = '`svi`.`' . addslashes($fieldName) . '` ' . $sequence;
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables () {

        return  array(
            '`goods_type_spec_value_relationship` AS `gtsvr` ON `gtsvr`.`spec_id`=`si`.`spec_id`',
            '`spec_value_info` AS `svi` ON `gtsvr`.`spec_value_id`=`svi`.`spec_value_id`',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`svi`.`spec_value_id`',
			'`svi`.`spec_value_data`',
			'`si`.`spec_id`',
        );
    }
}