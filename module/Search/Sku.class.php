<?php
class Search_Sku {

    /**
     * 根据条件筛选数据
     *
     * @param array $conditon   条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array
     */
    static public function listByCondition (array $conditon, array $orderBy = array(), $offset = null, $limit = null) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `goods_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondtion    = self::_condition($conditon);
        $sqlGroup       = self::_group();
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondtion . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Goods_Info::query($sql);
    }

    /**
     * 根据条件统计数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT(`goods_info`.`goods_id`)) AS `cnt` FROM `goods_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Goods_Info::query($sql);
        $row            = current($data);

        return          (int) $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByCategoryId($condition);
        $sql[]      = self::_conditionByStyleId($condition);
        $sql[]      = self::_conditionBySupplierId($condition);
        $sql[]      = self::_conditionByWeightRange($condition);
        $sql[]      = self::_conditionByMaterialId($condition);
        $sql[]      = self::_conditionBySizeId($condition);
        $sql[]      = self::_conditionByColorId($condition);
        $sql[]      = self::_conditionBySearchType($condition);
        $sql[]      = self::_conditionBySpuId();
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sql[]      = self::_conditionByListGoodsId($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter)
                    ? ''
                    : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据一组skuId拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByListGoodsId (array $condition) {
        
        if(empty($condition['list_goods_id'])){
            
            return ;
        }
        $multiId    = array_map('intval', array_unique(array_filter($condition['list_goods_id'])));
        
        return  '`goods_info`.`goods_id` IN ("' . implode('","', $multiId) . '")';
    }

    /**
     * 根据分类ID条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByCategoryId (array $condition) {

        return  $condition['category_id']
                ? '`goods_info`.`category_id` = "' . (int) $condition['category_id'] . '"'
                : '';
    }

    /**
     * 根据款式拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByStyleId (array $condition) {

        return  $condition['style_id_lv2']
                ? '`goods_info`.`style_id` = "' . (int) $condition['style_id_lv2'] . '"'
                : '';
    }

    /**
     * 根据供应商拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySupplierId (array $condition) {

        return  $condition['supplier_id']
                ? '`source_info`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"'
                : '';
    }

    /**
     * 根据重量范围拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByWeightRange (array $condition) {

        $weightValueStart   = '0' === $condition['weight_value_start']
                              ? 0
                              : sprintf('%.2f', $condition['weight_value_start']);
        $weightValueEnd     = '0' === $condition['weight_value_end']
                              ? 0
                              : sprintf('%.2f', $condition['weight_value_end']);

        if ($weightValueStart == $weightValueEnd && 0 === $weightValueStart) {

            $specValueInfo  = Spec_Value_Info::getBySpecValueData('0.00');
            $specValueId    = $specValueInfo['spec_value_id'];
            return          '`weight_info`.`spec_value_id` = "' . (int) $specValueId . '"';
        }

        if ($weightValueEnd && ($weightValueEnd > 0) && ($weightValueEnd >= $weightValueStart)) {
            $weightRangeList    = range(floor($weightValueStart * 100), ceil($weightValueEnd * 100));
            $weightRangeList    = array_map(create_function('$value', 'return sprintf("%.2f", $value / 100);'), $weightRangeList);

            $listSpecValueInfo  = Spec_Value_Info::getByMultiValueData($weightRangeList);
            $listSpecValueId    = array_unique(ArrayUtility::listField($listSpecValueInfo, 'spec_value_id'));
            return              '`weight_info`.`spec_value_id` IN ("' . implode('","', $listSpecValueId) . '")';
        }
        return '';
    }

    /**
     * 根据材质拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionByMaterialId (array $condition) {

        return  $condition['material_value_id']
                ? '`material_info`.`spec_value_id` = "' . (int) $condition['material_value_id'] . '"'
                : '';
    }

    /**
     * 根据规格尺寸拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionBySizeId (array $condition) {

        return  $condition['size_value_id']
                ? '`size_info`.`spec_value_id`= "' . (int) $condition['size_value_id'] . '"'
                : '';
    }

    /**
     * 根据颜色拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionByColorId (array $condition) {

        return  $condition['color_value_id']
                ? '`color_info`.`spec_value_id` = "' . (int) $condition['color_value_id'] . '"'
                : '';
    }

    /**
     * 根据搜索类型拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySearchType (array $condition) {

        $searchType         = trim($condition['search_type']);
        $searchValueString  = trim($condition['search_value_list']);

        if (!$searchType || !$searchValueString) {

            return '';
        }
        $searchValueList    = explode(' ', $searchValueString);
        $searchValueList    = array_map('addslashes', array_map('trim', array_unique(array_filter($searchValueList))));
        $filed              = '';
        switch ($searchType) {
            case 'source_code' :
                $filed      = '`source_info`.`source_code`';
                break;
            case 'goods_sn' :
                $filed      = '`goods_info`.`goods_sn`';
                break;
            case 'spu_sn' :
                $filed      = '`spu_info`.`spu_sn`';
                break;
        }
        return              $filed
                            ? $filed . ' IN ("' . implode('","', $searchValueList) . '")'
                            : '';
    }

    /**
     * 拼接此WHERE子句主要是为了让语句使用索引
     *
     * @return string
     */
    static private function _conditionBySpuId () {

        return  '`spu_info`.`spu_id` > 0';
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  '`goods_info`.`delete_status`= "' . (int) $condition['delete_status'] . '"';
    }

    /**
     * 拼接GROUP BY 语句
     *
     * @return string
     */
    static private function _group () {

        return  ' GROUP BY `goods_info`.`goods_id`';
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
            "`goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`goods_info`.`goods_id` AND `material_info`.`spec_id`='1'",
            "`goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id`=`goods_info`.`goods_id` AND `size_info`.`spec_id`='2'",
            "`goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`goods_info`.`goods_id` AND `color_info`.`spec_id`='3'",
            "`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id`='4'",
            "`product_info` ON `product_info`.`goods_id`=`goods_info`.`goods_id`",
            "`source_info` ON `source_info`.`source_id`=`product_info`.`source_id`",
            "`spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id`=`goods_info`.`goods_id`",
            "`spu_info` ON `spu_info`.`spu_id`=`sgr`.`spu_id`",
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
            '`goods_info`.`category_id`',
            '`goods_info`.`style_id`',
            '`goods_info`.`self_cost`',
            '`goods_info`.`sale_cost`',
            '`goods_info`.`goods_remark`',
            '`goods_info`.`online_status`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`size_info`.`spec_value_id` AS `size_value_id`',
            '`color_info`.`spec_value_id` AS `color_value_id`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
        );
    }

    /**
     * 获取搜索类型
     *
     * @return array    数据
     */
    static public function getSearchType () {

        return array(
            'source_code'   => '买款ID',
            'goods_sn'      => 'SKU编号',
            'spu_sn'        => 'SPU编号'
        );
    }
}