<?php
class Search_Product {

    /**
     * 根据筛选条件获取数据
     *
     * @param array $condition  筛选条件
     * @param null $offset      位置
     * @param null $limit       数量
     */
    static public function listByCondition (array $condition, $offset = null, $limit = null) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `product_info` AS `pi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables($condition));
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = ' ORDER BY `pi`.`product_id` DESC ';
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlOrder . $sqlLimit;

        return          Product_Info::query($sql);

    }

    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `product_info` AS `pi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables($condition));
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = ' ORDER BY `pi`.`product_id` DESC ';
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlOrder . $sqlLimit;
        $data           = Product_Info::query($sql);
        $row            = current($data);

        return          $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  筛选条件
     * @return string           WHERE子句
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
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sql[]      = self::_conditionByOnlineStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据上下架状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByOnlineStatus (array $condition) {

        return  !isset($condition['online_status']) ? '' : '`pi`.`online_status` = "' . (int) $condition['online_status'] . '"';
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  !isset($condition['delete_status']) ? '' : '`pi`.`delete_status` = "' . (int) $condition['delete_status'] . '"';
    }

    /**
     * 根据三级分类拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByCategoryId (array $condition) {

        return  $condition['category_id']
                ? '`gi`.`category_id` = "' . (int) $condition['category_id'] . '"'
                : '';
    }

    /**
     * 根据子款式ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByStyleId (array $condition) {

        return  $condition['style_id_lv2']
                ? '`gi`.`style_id` = "' . (int) $condition['style_id_lv2'] . '"'
                : '';
    }

    /**
     * 根据供应商ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySupplierId (array $condition) {

        return  $condition['supplier_id']
                ? '`si`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"'
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

        return  $condition['spec_value_material_id']
                ? '`material_info`.`spec_value_id` = "' . (int) $condition['spec_value_material_id'] . '"'
                : '';
    }

    /**
     * 根据规格尺寸拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionBySizeId (array $condition) {

        return  $condition['spec_value_size_id']
                ? '`size_info`.`spec_value_id`= "' . (int) $condition['spec_value_size_id'] . '"'
                : '';
    }

    /**
     * 根据颜色拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionByColorId (array $condition) {

        return  $condition['spec_value_color_id']
                ? '`color_info`.`spec_value_id` = "' . (int) $condition['spec_value_color_id'] . '"'
                : '';
    }

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
                $filed      = '`si`.`source_code`';
            break;
            case 'goods_sn' :
                $filed      = '`gi`.`goods_sn`';
            break;
            case 'product_sn' :
                $filed      = '`pi`.`product_sn`';
            break;
            case 'spu_sn'   :
                $filed      = '`spu_info`.spu_sn';
            break;
        }
        return              $filed
                            ? $filed . ' IN ("' . implode('","', $searchValueList) . '")'
                            : '';
    }

    /**
     * 拼接LIMIT子句
     *
     * @param $offset   位置
     * @param $limit    数量
     * @return string
     */
    static private function _limit ($offset, $limit) {

        return  (null === $offset || null === $limit) ? '' : ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables (array $condition) {

        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias', 'spec_id');
        $weightId       = $mapSpecInfo['weight'];
        $sizeId         = $mapSpecInfo['size'];
        $colorId        = $mapSpecInfo['color'];
        $materialId     = $mapSpecInfo['material'];

        $joinSql        = array(
            '`goods_info` AS `gi` ON `gi`.`goods_id`=`pi`.`goods_id`',
            '`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`pi`.`goods_id` AND `weight_info`.`spec_id` = ' . $weightId,
            '`goods_spec_value_relationship` AS `size_info` ON `size_info`.`goods_id`=`pi`.`goods_id` AND `size_info`.`spec_id` = ' . $sizeId,
            '`goods_spec_value_relationship` AS `color_info` ON `color_info`.`goods_id`=`pi`.`goods_id` AND `color_info`.`spec_id` = ' . $colorId,
            '`goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`pi`.`goods_id` AND `material_info`.`spec_id` = ' . $materialId,
            '`source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id`',
        );
        
        if($condition['search_type'] == 'spu_sn'){
             
                $joinSql[] = "`spu_goods_relationship` AS `sgr` ON `sgr`.`goods_id`=`gi`.`goods_id`";  
                $joinSql[] = "`spu_info` ON `spu_info`.`spu_id`=`sgr`.`spu_id`";    
        }

        return  $joinSql;
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
            '`pi`.`product_name`',
            '`gi`.`goods_sn`',
            '`gi`.`category_id`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
            '`size_info`.`spec_value_id` AS `size_value_id`',
            '`color_info`.`spec_value_id` AS `color_value_id`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`si`.`supplier_id`',
            '`si`.`source_code`',
            '`pi`.`product_cost`',
            '`pi`.`goods_id`',
            '`pi`.`source_id`',
            '`pi`.`online_status`',
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
            'product_sn'    => '产品编号',
            'spu_sn'        => 'SPU编号',
        );
    }
}