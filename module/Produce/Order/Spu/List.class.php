<?php
class Produce_Order_Spu_List {

    /**
     * 根据条件查询数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param $offset           位置
     * @param $limit            数量
     * @return array
     */
    static public function listByCondition (array $condition, array $orderBy) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `produce_order_product_info` AS `popi` LEFT JOIN';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlGroup       = ' GROUP BY `si`.`source_id`,`color_value_id`,`spu_info`.`spu_id`';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder;

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
        $sqlGroup       = ' GROUP BY `si`.`source_id`,`color_value_id`,`spu_info`.`spu_id`';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition .$sqlGroup;
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
        $sql[]      = self::_conditionByMultiSpuId($condition);
        $sql[]      = self::_conditionByMultiProduceOrderId($condition);
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
     * 根据SPUID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiSpuId (array $condition) {

        if(empty($condition['list_spu_id'])){
            
            return ;
        }
        $multiSpuId = array_map('intval', array_unique(array_filter($condition['list_spu_id'])));
        
        return '`spu_info`.`spu_id` IN ("' . implode('","', $multiSpuId) . '")';
    }

    /**
     * 根据生产订单ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiProduceOrderId (array $condition) {

        if(empty($condition['list_produce_order_id'])){
            
            return ;
        }
        $multiProduceOrderId = array_map('intval', array_unique(array_filter($condition['list_produce_order_id'])));
        
        return '`popi`.`produce_order_id` IN ("' . implode('","', $multiProduceOrderId) . '")';
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

        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias', 'spec_id');
        $weightId       = $mapSpecInfo['weight'];
        $sizeId         = $mapSpecInfo['size'];
        $colorId        = $mapSpecInfo['color'];
        $materialId     = $mapSpecInfo['material'];

        return  array(
            '`product_info` AS `pi` ON `pi`.`product_id`=`popi`.`product_id`',
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
            '`pi`.`product_cost`',
            '`si`.`source_code`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
            '`color_info`.`spec_value_id` AS `color_value_id`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`gi`.`category_id`',
            '`popi`.`produce_order_id`',
            '`popi`.`remark`',
            '`spu_info`.`spu_sn`',
            'SUM(`popi`.`quantity`) as `total_quantity`'
        );
    }

    static private function _query ($sql) {

        return  Produce_Order_Product_Info::query($sql);
    }
}