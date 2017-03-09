<?php
class Produce_Order_Arrive_Spu_List {

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
        $sqlBase        = 'SELECT ' . $fields . ' FROM `produce_order_arrive_product_info` AS `poapi` LEFT JOIN';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sqlGroup       = ' GROUP BY `si`.`source_id`,`color_value_id`,`spu_info`.`spu_id`';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          self::_query($sql);
    }

    /**
     * 根据条件统计数量
     *
     * @param array $condition
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT `color_info`.`spec_value_id` AS `color_value_id`, COUNT(1) AS `cnt` FROM `produce_order_arrive_product_info` AS `poapi` LEFT JOIN';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = ' GROUP BY `si`.`source_id`,`color_value_id`,`spu_info`.`spu_id`';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition .$sqlGroup;
        $data           = self::_query($sql);

        return          count($data);
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByProduceOrderArriveId($condition);
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
    static private function _conditionByProduceOrderArriveId (array $condition) {

        return  isset($condition['produce_order_arrive_id'])
                ? '`poapi`.`produce_order_arrive_id` = "' . (int) $condition['produce_order_arrive_id'] . '"'
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
     * 拼接ORDER BY子句
     *
     * @param array $orderBy    排序
     * @return string
     */
    static private function _order (array $orderBy) {

        return  ' ORDER BY `poapi`.`product_id` ASC';
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

        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias', 'spec_id');
        $weightId       = $mapSpecInfo['weight'];
        $sizeId         = $mapSpecInfo['size'];
        $colorId        = $mapSpecInfo['color'];
        $materialId     = $mapSpecInfo['material'];

        return  array(
            '`product_info` AS `pi` ON `pi`.`product_id`=`poapi`.`product_id`',
            '`source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id`',
            '`goods_info` AS `gi` ON `gi`.`goods_id`=`pi`.`goods_id`',
            '`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`pi`.`goods_id` AND `weight_info`.`spec_id` = ' . $weightId,
            '`goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`pi`.`goods_id` AND `color_info`.`spec_id` = ' . $colorId,
            '`goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`pi`.`goods_id` AND `material_info`.`spec_id` = ' . $materialId,
            '`spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id`=`gi`.`goods_id`', 
            '`spu_info` ON `spu_info`.`spu_id`=`sgr`.`spu_id`', 
            
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`poapi`.`storage_cost`',
            '`si`.`source_code`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
            '`color_info`.`spec_value_id` AS `color_value_id`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`gi`.`category_id`',
            '`poapi`.`produce_order_arrive_id`',
            '`spu_info`.`spu_sn`',
            '`spu_info`.`spu_id`',
            'SUM(`poapi`.`quantity`) as `arrive_total_quantity`',
            'SUM(`poapi`.`weight`) as `total_weight`'
        );
    }

    static private function _query ($sql) {

        return  Produce_Order_Product_Info::query($sql);
    }
}