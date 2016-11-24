<?php
class Search_SalesQuotationSpu {

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
        $sqlBase        = 'SELECT ' . $fields . ' FROM `sales_quotation_spu_info` AS `sq_spu_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy,$condition);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sqlGroup       = ' GROUP BY `sq_spu_info`.`spu_id` ';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Sales_Quotation_Spu_Info::query($sql);
    }

    /**
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT(`sq_spu_info`.`spu_id`)) AS `cnt` FROM `sales_quotation_spu_info` AS `sq_spu_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Sales_Quotation_Spu_Info::query($sql);
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

        $sql[]      = self::_conditionSalesQuotationId($condition);
        $sql[]      = self::_conditionBySearchType($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据报价单ID条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */

    static  private function _conditionSalesQuotationId (array $condition) {

        if (empty($condition['sales_quotation_id'])) {

            return  '';
        }

        return  "`sq_spu_info`.`sales_quotation_id` = '" . addslashes($condition['sales_quotation_id']) . "'";
    }

     /**
     * 根据搜索类型拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySearchType (array $condition) {

        $searchValueString  = trim($condition['search_value_list']);

        if (empty($searchValueString)) {

            return '';
        }
        return  '(`source_info`.`source_code` IN ("' . implode('","', explode(" ", $searchValueString)) . '") 
                OR `spu_info`.`spu_sn` IN ("' . implode('","', explode(" ",$searchValueString)) . '"))';
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
            $sql[]      = '`sq_spu_info`.`' . addslashes($fieldName) . '` ' . $sequence;
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables () {

        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');

        return  array(
            '`spu_info` AS `spu_info` ON `sq_spu_info`.`spu_id`=`spu_info`.`spu_id`',
            '`spu_goods_relationship` AS `sgr` ON `sgr`.`spu_id`=`spu_info`.`spu_id`',
            '`goods_info` AS `goods_info` ON `goods_info`.`goods_id`=`sgr`.`goods_id`',
            '`product_info` AS `product_info` ON `product_info`.`goods_id`=`goods_info`.`goods_id`',
            '`source_info` AS `source_info` ON `source_info`.`source_id`=`product_info`.`source_id`',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`sq_spu_info`.`sales_quotation_id`',
            '`sq_spu_info`.`spu_id`',
            '`sq_spu_info`.`cost`',
            '`sq_spu_info`.`color_id`',
            '`sq_spu_info`.`sales_quotation_remark`',
            '`sq_spu_info`.`is_red_bg`',
            '`sq_spu_info`.`identical_source_code_spu_num`',
            '`sq_spu_info`.`is_cart_join`',
        );
    }
}