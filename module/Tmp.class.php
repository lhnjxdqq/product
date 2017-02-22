<?php
/**
 * 临时表 模型
 */
class   Tmp {

    use Base_MiniModel;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'tmp';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_id,category_id,style_id,spec_size_id,spec_weight_id,spec_color_id,spec_material_id,spec_assistant_material_id';

    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 数据分组
     */
    static public function groupData () {

        $sqlBase    = 'SELECT count(1) as count, ' . self::FIELDS . ' FROM `' . self::TABLE_NAME . '` ';
        $sqlGroup   = ' GROUP BY `category_id`,`style_id`,`spec_size_id`,`spec_weight_id`,`spec_color_id`,`spec_material_id`,`spec_assistant_material_id`';
        $sqlHaving  = ' HAVING count>1';
        $sql = $sqlBase . $sqlGroup . $sqlHaving;

        return self::_getStore()->fetchAll($sql);
    }


    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $order      排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, array $order = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionEqIsExistField($condition, 'category_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'style_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'spec_size_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'spec_weight_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'spec_color_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'spec_material_id');
        $sql[]          = self::_conditionEqIsExistField($condition, 'spec_assistant_material_id');
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
    * 根据条件获取SQL子句
    * @desc 以字段是否存在 等于查询
    * @param   array   $condition  条件
    * @param    string  $field      字段名
    * @param    string  $message    提示信息
    * @return  string              条件SQL子句
    */
    static  private function _conditionEqIsExistField (array $condition,$field) {
    
        if(!isset($condition[$field])) {
        
            return '';
        }
    
        return "`{$field}`=" . $condition[$field];
    }

    /**
     * 拼接排序ORDER子句
     *
     * @param array $order  排序规则
     * @return string       ORDER子句
     */
    static private function _order (array $order) {

        if (!$order) {

            return '';
        }

        $sql = array();
        foreach ($order as $field => $direction) {

            $field  = str_replace('`' , '', $field);
            $sql[]  = '`' . addslashes($field) . '` ' . $direction;
        }

        return empty($sql) ? '' : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 拼接分页LIMIT子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string       LIMIT子句
     */
    static private function _limit ($offset = null, $limit = null) {

        if ($offset === null || $limit === null) {

            return '';
        }

        return ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 根据条件获取数据条数
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(*) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

}
