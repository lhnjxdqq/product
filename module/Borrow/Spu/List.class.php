<?php
class Borrow_Spu_List {

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, $orderBy = array(), $offset = null, $limit = null) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `borrow_spu_info` AS `bsi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = self::_group();
        $sqlOrder       = self::_order($orderBy,$condition);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Spu_Info::query($sql);
    }

    /**
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT count(1) `cnt` FROM `borrow_spu_info` AS `bsi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = self::_group();
        $sql            = $sqlBase . $sqlJoin . $sqlCondition .$sqlGroup;
        $data           = Spu_Info::query($sql);

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
        $sql[]      = self::_conditionByCategoryId($condition);
        $sql[]      = self::_conditionByStyleId($condition);
        $sql[]      = self::_conditionBySupplierId($condition);
        $sql[]      = self::_conditionByWeightRange($condition);
        $sql[]      = self::_conditionBySearchType($condition);
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sql[]      = self::_conditionByOnlineStatus($condition);
        $sql[]      = self::_conditionByBorrowId($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据产品分类条件拼接WHERE子句
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
     * 根据样借板ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByBorrowId (array $condition) {

        return  $condition['borrow_id']
                ? '`bsi`.`borrow_id` = "' . (int) $condition['borrow_id'] . '"'
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
                ? '`ssi`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"'
                : '';
    }
    
    /**
     * 拼接GROUP子句
     *
     * @return string
     */
    static private function _group () {

        return  ' GROUP BY `bsi`.`spu_id`,`bsi`.`sample_storage_id`';
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
     * 按SPU删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  '`spu_info`.`delete_status` = "' . (int) $condition['delete_status'] . '"';
    }
    
    /**
     * 按SPU上架状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByOnlineStatus (array $condition) {

        if(empty($condition['online_status'])){
            
            return ;
        }
        return  '`spu_info`.`online_status` = "' . (int) $condition['online_status'] . '"';
    }

    /**
     * 按SPU有无图片状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByImageStatus (array $condition) {

        if(empty($condition['image_status'])){
            
            return ;
        }
        if( $condition['image_status'] == 1 ){
            
            return ' `spu_info`.`image_total` > 0 ';
        }else{

            return  '`spu_info`.`image_total` = 0 ';  
        }
    }

    /**
     * 拼接ORDER BY 子句
     *
     * @param $order
     * @return string
     */
    static private function _order ($order, array $condition) {
        
        $searchType         = trim($condition['search_type']);
        $searchValueString  = trim($condition['search_value_list']);

        if (empty($searchType) || empty($searchValueString)) {

            return  ' ORDER BY `source_info`.`source_code` ASC,`spu_info`.`spu_id` DESC';
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
        return  ' ORDER BY find_in_set('. $filed .', "' . implode(',', $searchValueList) . '")';
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

        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');

        return  array(
			'`sample_storage_info` AS `ssi` ON `ssi`.`sample_storage_id` = `bsi`.`sample_storage_id`',
            '`spu_info` AS `spu_info` ON `bsi`.`spu_id`=`spu_info`.`spu_id`',
            '`spu_goods_relationship` AS `sgr` ON `sgr`.`spu_id`=`spu_info`.`spu_id`',
            '`goods_info` AS `goods_info` ON `goods_info`.`goods_id`=`sgr`.`goods_id`',
            '`goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id` = ' . $mapSpecInfo['weight']['spec_id'],
            '`goods_spec_value_relationship` AS `material_info` ON `material_info`.`goods_id`=`goods_info`.`goods_id` AND `material_info`.`spec_id` = ' . $mapSpecInfo['material']['spec_id'],
            '`goods_spec_value_relationship` AS `assistant_material_info` ON `assistant_material_info`.`goods_id`=`goods_info`.`goods_id` AND `assistant_material_info`.`spec_id` = ' . $mapSpecInfo['assistant_material']['spec_id'],
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
            '`bsi`.`spu_id`',
            '`bsi`.`borrow_quantity`',
            '`bsi`.`sample_storage_id`',
            '`bsi`.`shipment_cost`',
            '`spu_info`.`spu_sn`',
            '`spu_info`.`valuation_type`',
            '`spu_info`.`spu_name`',
            '`spu_info`.`spu_remark`',
            '`spu_info`.`online_status`',
            '`goods_info`.`goods_id`',
            '`goods_info`.`category_id`',
            '`goods_info`.`style_id`',
            '`source_info`.`source_code`',
            '`weight_info`.`spec_value_id` AS `weight_value_id`',
            '`material_info`.`spec_value_id` AS `material_value_id`',
            '`assistant_material_info`.`spec_value_id` AS `assistant_material_value_id`',
            '`ssi`.`supplier_id`'
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