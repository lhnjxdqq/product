<?php
class Search_BorrowSample {

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

        $fields         = implode(',', self::_getQueryFields($condition));
        $sqlBase        = 'SELECT ' . $fields . ' FROM `sample_storage_spu_info` AS `stsi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables($condition));
        $sqlCondition   = self::_condition($condition);
        $keywordSql     = '';
        if(!empty($condition['keyword_list'])){

            $keywordSql = self::_keywordCondition($condition);
        }
        $sqlGroup       = self::_group();
        $sqlLimit       = self::_limit($offset, $limit);
        $sqlHaving      = ' HAVING (`stsi`.`quantity` > sum_borrow_quantity OR sum_borrow_quantity IS NULL) ';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $keywordSql . self::_subqueryByCondition($condition) . $sqlGroup . $sqlHaving . $sqlLimit;

        return          Spu_Info::query($sql);
    }

    /**
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT `stsi`.`spu_id` AS `cnt`, SUM(
                IF (
                    `bsi`.`start_time` <= "'. $condition['end_time'] .'"
                    AND `bsi`.`estimate_time` >= " '.$condition['start_time'].'"
                    AND `bsi`.`borrow_id` != "'.$condition['borrow_id'] .'",
                    `bsi`.`borrow_quantity`,
                    0
                )
            ) AS sum_borrow_quantity ,`stsi`.`quantity` FROM `sample_storage_spu_info` AS `stsi` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables($condition));
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = self::_group();
        $keywordSql     = '';
        if(!empty($condition['keyword_list'])){

            $keywordSql = self::_keywordCondition($condition);
        }
        $sqlHaving      = ' HAVING (`stsi`.`quantity` > sum_borrow_quantity OR sum_borrow_quantity IS NULL) ';
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $keywordSql . self::_subqueryByCondition($condition) . $sqlGroup . $sqlHaving;
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
        $sql[]      = self::_conditionByBorrowTime($condition);
        $sql[]      = self::_conditionByStartTime($condition);
        $sql[]      = self::_conditionByEndTime($condition);
        $sql[]      = self::_conditionBySampleType($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? ' WHERE 1=1 ' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _keywordCondition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByMultiElementId($condition);
        $sql[]      = self::_conditionByMultiShapeId($condition);
        $sql[]      = self::_conditionByMultiTechnicId($condition);
        $sql[]      = self::_conditionByMultiMainstoneId($condition);

        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : " AND (" . implode(' OR ', $sqlFilter) .")";
    }

    /**
     * 按元素拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiTechnicId (array $condition) {
        
        if(empty($condition['attr_list']['technicId'])){
            
            return ;
        }
        return  '`str`.`trchnic_id` IN ("' . implode('","', $condition['attr_list']['technicId']) . '")';
    }
    
    /**
     * 按元素拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiMainstoneId (array $condition) {
        
        if(empty($condition['attr_list']['mainstoneId'])){
            
            return ;
        }
        return  '`smr`.`mainstone_id` IN ("' . implode('","', $condition['attr_list']['mainstoneId']) . '")';
    }
    
    /**
     * 按元素拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiElementId (array $condition) {
        
        if(empty($condition['attr_list']['elementId'])){
            
            return ;
        }
        return  '`ser`.`element_id` IN ("' . implode('","', $condition['attr_list']['elementId']) . '")';
    }
    
    /**
     * 按元素拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByMultiShapeId (array $condition) {
        
        if(empty($condition['attr_list']['shapeId'])){
            
            return ;
        }
        return  '`ssr`.`shape_id` IN ("' . implode('","', $condition['attr_list']['shapeId']) . '")';
    }
    
    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _subqueryCondition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByCategoryId($condition);
        $sql[]      = self::_conditionByStyleId($condition);
        $sql[]      = self::_conditionBySupplierId($condition);
        $sql[]      = self::_conditionByWeightRange($condition);
        $sql[]      = self::_conditionBySearchType($condition);
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sql[]      = self::_conditionByOnlineStatus($condition);

        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '  WHERE  1=1' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _subqueryKeywordCondition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionBySpuSn($condition);
        $sql[]      = self::_conditionBybrandId($condition);
        $sql[]      = self::_conditionBySpuStyleId($condition);//风格
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : " AND (" . implode(' OR ', $sqlFilter) .")";
    }

    /**
     * 根据分类ID条件拼接WHERE子句
     *
     * @param   array $condition sql子条件
     * @return  string      
     */
    static private function _subqueryByCondition(array $condition){
        
        
        $listSpecInfo   = Spec_Info::listAll();
        $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');

        $sql = 'SELECT DISTINCT(`sgr`.`spu_id`)
            FROM `spu_goods_relationship` `sgr`
         LEFT JOIN `spu_info` AS `spu_info` ON `sgr`.`spu_id`=`spu_info`.`spu_id`
         LEFT JOIN `goods_info` AS `goods_info` ON `goods_info`.`goods_id`=`sgr`.`goods_id`
         LEFT JOIN `goods_spec_value_relationship` AS `weight_info` ON `weight_info`.`goods_id`=`goods_info`.`goods_id` AND `weight_info`.`spec_id` = ' . $mapSpecInfo["weight"]["spec_id"] . '
         LEFT JOIN `product_info` AS `product_info` ON `product_info`.`goods_id`=`goods_info`.`goods_id`
         LEFT JOIN `source_info` AS `source_info` ON `source_info`.`source_id`=`product_info`.`source_id`';
        
        $sql .= self::_subqueryCondition($condition);
        
        if(!empty($condition['keyword_list'])){

            $sql .= self::_subqueryKeywordCondition($condition);
        }
        return ' AND `stsi`.`spu_id` IN('. $sql .')';
    }
    
    /**
     * 根据分类ID条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByStartTime (array $condition) {

        return  $condition['create_start_time']
                ? '`stsi`.`create_time` >= "' . $condition['create_start_time'] .' 00:00:00' . '"'
                : '';
    }

    /**
     * 根据到样板类型条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySampleType (array $condition) {

        if(empty($condition['sample_type'])){
            
            return ;
        }
        if($condition['sample_type'] == 2){
            
            return '`stsi`.`sample_type` = ' . (int) $condition['sample_type'];
        }
        return '`stsi`.`sample_type` != 2';
    }

    /**
     * 根据样板类型条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByEndTime (array $condition) {

        return  $condition['create_end_time']
                ? '`stsi`.`create_time` <= "' .  $condition['create_end_time'] . '23:59:59'. '"'
                : '';
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
     * 根据用板时间拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByBorrowTime (array $condition) {

        return  '(`stsi`.`estimate_return_time` > 
                    "'. $condition['end_time'] .'" OR `stsi`.`estimate_return_time` IS NULL)';
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

        return  ' GROUP BY `stsi`.`spu_id`,`stsi`.`sample_storage_id`';
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
     * 按SPUSn拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySpuSn (array $condition) {
        
        if(empty($condition['attr_list']['spuSn'])){
            
            return ;
        }
        return  '`spu_info`.`spu_sn` IN ("' . implode('","', $condition['attr_list']['spuSn']) . '")';
    }
    
    /**
     * 按品牌拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBybrandId (array $condition) {
        
        if(empty($condition['attr_list']['brandId'])){
            
            return ;
        }
        return  '`spu_info`.`brand_id` IN ("' . implode('","', $condition['attr_list']['brandId']) . '")';
    }
    
    /**
     * 按风格拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionBySpuStyleId (array $condition) {
        
        if(empty($condition['attr_list']['styleId'])){
            
            return ;
        }
        return  '`spu_info`.`style_id` IN ("' . implode('","', $condition['attr_list']['styleId']) . '")';
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
    static private function _getJoinTables (array $condition) {
        
        $joinSql = array(
            '`sample_storage_info` AS `ssi` ON `ssi`.`sample_storage_id` = `stsi`.`sample_storage_id`',
            '`borrow_spu_info` AS `bsi` ON `bsi`.`spu_id` = `stsi`.`spu_id`  AND `stsi`.`sample_storage_id`=`bsi`.`sample_storage_id`',
        );

        if (!empty($condition['attr_list']['elementId'])) {
            
            $joinSql[] = '`spu_element_relationship` as `ser` ON `ser`.`spu_id` = `stsi`.`spu_id`';
        }
        
        if(!empty($condition['attr_list']['shapeId'])) {

            $joinSql[] = '`spu_shape_relationship` as `ssr` ON `ssr`.`spu_id` = `stsi`.`spu_id`';
        }
        
        if(!empty($condition['attr_list']['technicId'])) {
            
            $joinSql[] = '`spu_technic_relationship` as `str` ON `str`.`spu_id` = `stsi`.`spu_id`';
        }
        
        if(!empty($condition['attr_list']['mainstoneId'])) {
            
            $joinSql[] = '`spu_mainstone_relationship` as `smr` ON `smr`.`spu_id` = `stsi`.`spu_id`';
        }
        return  $joinSql;
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields (array $condition) {

        return  array(
            '`stsi`.`spu_id`',
            '`stsi`.create_time',
            '`stsi`.`sample_type`',
            '`stsi`.`quantity`',
            '`bsi`.`borrow_quantity`',
            '`stsi`.`sample_storage_id`',
            '`ssi`.`supplier_id`',
            ' SUM(
                IF (
                    `bsi`.`start_time` <= "'. $condition['end_time'] .'"
                    AND `bsi`.`estimate_time` >= " '.$condition['start_time'].'"
                    AND `bsi`.`borrow_id` != "'.$condition['borrow_id'] .'",
                    `bsi`.`borrow_quantity`,
                    0
                )
            ) AS sum_borrow_quantity ',
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