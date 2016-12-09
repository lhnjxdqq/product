<?php
class Spu_Images_List {

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
        $sqlBase        = 'SELECT ' . $fields . ' FROM `spu_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = ' GROUP BY `spu_info`.`spu_id`';
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          Spu_Images_RelationShip::query($sql);
    }

    /**
     * 根据条件查询数据数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT(`spu_info`.`spu_id`)) AS `cnt` FROM `spu_info` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $data           = Spu_Images_RelationShip::query($sql);
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
        $sql[]      = self::_conditionByRecycleStatus($condition);
        $sql[]      = self::_conditionByListSpuSn($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  '`spu_info`.`delete_status` = ' . (int) $condition['delete_status'];
    }
    
    /**
     * 根据SPU编号拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByListSpuSn (array $condition) {

		if(empty($condition['list_spu_sn'])){
		
			return ;
		}
		$listSpuSn	= array_unique(explode(" ", $condition['list_spu_sn']));
		return '`spu_info`.`spu_sn` IN ("' . implode('","', $listSpuSn) . '")';
    }
    
    /**
     * 根据是否加入购物车状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByRecycleStatus (array $condition) {

		if(!empty($condition['recycle_status'])){
		
			return  '`sir`.`recycle_status` = ' . (int) $condition['recycle_status'];
		}
	}
    
    /**
     * 拼接ORDER BY语句
     *
     * @return string
     */
    static private function _order () {

        return  ' ORDER BY `spu_info`.`spu_id` DESC';
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

        return  array(
            '`spu_images_relationship` AS `sir` ON `sir`.`spu_id`=`spu_info`.`spu_id`',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`spu_info`.`spu_id`',
            '`spu_info`.`spu_sn`',
        );
    }
}