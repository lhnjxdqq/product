<?php
class Spu_List {

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
        $sqlBase        = 'SELECT ' . $fields . ' FROM `spu_info` AS `si` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = ' GROUP BY `si`.`spu_id`';
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Spu_Info::query($sql);
    }

    /**
     * 根据条件查询数据数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT(`si`.`spu_id`)) AS `cnt` FROM `spu_info` AS `si` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Spu_Info::query($sql);
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
        $sql[]      = self::_conditionByOnlineStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 查询某个SPU下的SKU信息
     *
     * @param $spuId    SPUID
     * @return array
     */
    static public function listSpuGoodsInfo ($spuId) {

        $spuGoodsList   = Spu_Goods_RelationShip::getBySpuId($spuId);
        $spuGoodsIdList = ArrayUtility::listField($spuGoodsList, 'goods_id');
        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');

        $fields         = '`gi`.`goods_id`,`color_info`.`spec_value_id`,`gi`.`sale_cost`,`si`.`supplier_id`';
        $sql            = 'SELECT ' . $fields . ' FROM `goods_info` AS `gi` '
                        . 'LEFT JOIN `goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`gi`.`goods_id` AND `color_info`.`spec_id`=' . $mapSpecInfo['color']['spec_id'] . ' '
                        . 'LEFT JOIN `product_info` AS `pi` ON `gi`.`goods_id`=`pi`.`goods_id` '
                        . 'LEFT JOIN `source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id` '
                        . 'WHERE `gi`.`goods_id` IN ("' . implode('","', $spuGoodsIdList) . '")';

        return          Spu_Info::query($sql);
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  '`si`.`delete_status` = ' . (int) $condition['delete_status'];
    }
    
    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByOnlineStatus (array $condition) {

        if(empty($condition['online_status'])){
            
            return ;
        }
        return  '`si`.`online_status` = ' . (int) $condition['online_status'];
    }

    /**
     * 拼接ORDER BY语句
     *
     * @return string
     */
    static private function _order () {

        return  ' ORDER BY `si`.`spu_id` DESC';
    }

    /**
     * 拼接分页LIMIT子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string       LIMIT子句
     */
    static private function _limit ($offset, $limit) {

        return  ($offset === null || $limit === null)
                ? ''
                : ' LIMIT ' . (int) $offset . ',' . (int) $limit;
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
            '`spu_goods_relationship` AS `sgr` ON `sgr`.`spu_id`=`si`.`spu_id`',
            '`goods_info` AS `gi` ON `gi`.`goods_id`=`sgr`.`goods_id`',
            '`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`gi`.`goods_id` AND `weight_info`.`spec_id`=' . $mapSpecInfo['weight']['spec_id'],
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`si`.`spu_id`',
            '`si`.`spu_sn`',
            '`si`.`spu_name`',
            '`si`.`spu_remark`',
            '`si`.`online_status`',
            '`gi`.`category_id`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
        );
    }
}