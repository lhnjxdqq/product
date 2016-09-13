<?php
/**
 * 模型 买款
 */
class   Source_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'source_info';

    /**
     * 字段
     */
    const   FIELDS      = 'source_id,source_code,supplier_id,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'source_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);
        return      self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'source_id',
        );
        $condition  = "`source_id` = '" . addslashes($data['source_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param $limit            数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, array $orderBy = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionBySourceCode($condition);
        $sql[]          = self::_conditionBySupplierId($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
     * 根据来款代码拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionBySourceCode ($condition) {

        return !$condition['source_code'] ? '' : '`source_code` = "' . addslashes(trim($condition['source_code'])) . '"';
    }

    /**
     * 根据供应商ID拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionBySupplierId ($condition) {

        return !$condition['supplier_id'] ? '' : '`supplier_id` = "' . (int) $condition['supplier_id'] . '"';
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
     * 根据一组source_id 获取该组来款信息
     *
     * @param array $multiId    一组source_id
     * @return array            该组来款信息
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据source_id获取买款信息
     *
     * @param $sourceId source_id
     * @return array    买款信息
     */
    static public function getById ($sourceId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_id` = "' . (int) $sourceId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据买款代码查询买款信息
     *
     * @param $sourceCode   买款代码
     * @return array|void   买款信息
     */
    static public function getBySourceCode ($sourceCode) {

        if (empty($sourceCode)) {

            return;
        }

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_code` = "' . addslashes(trim($sourceCode)) . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据多个买款ID查询买款信息
     *
     * @param $multiSourceCode
     * @return array
     */
    static public function getByMultiSourceCode ($multiSourceCode) {

        $multiCode  = array_map('trim', array_map('addslashes', array_unique(array_filter($multiSourceCode))));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_code` IN ("' . implode('","', $multiCode) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组sourceId 获取来款信息
     *
     * @param array $multiSourceId
     * @return array
     */
    static public function getByMultiSourceId (array $multiSourceId) {

        $multiSourceId  = array_map('intval', array_unique(array_filter($multiSourceId)));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_id` IN ("' . implode('","', $multiSourceId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据买款代码和厂商获取信息
     *
     *  @param  string $sourceCode  买款代码
     *  @param  int    $supplierId  厂商Id
     *
     *  @return array               数据
     */
    static public function getBySourceCodeAndSupplierId($sourceCode, $supplierId){
         
         if(empty($sourceCode) || empty($supplierId)){
             
             return array();
         }
         $sql   = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_code` = "'. $sourceCode .'" AND `supplier_id` = "'. $supplierId .'"';

        return          self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据买款代码和厂商获取信息
     *
     *  @param  array  $multiSourceCode  买款代码
     *  @param  int    $supplierId       厂商Id
     *
     *  @return array               数据
     */
    static public function getBySourceCodeAndMulitSupplierId($multiSourceCode, $supplierId){
         
        if(empty($multiSourceCode) || empty($supplierId)){
             
            return array();
        }
         
        $multiCode  = array_map('trim', array_map('addslashes', array_unique(array_filter($multiSourceCode))));

        $sql   = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_code` IN ("' . implode('","', $multiCode) . '") AND `supplier_id` = "'. $supplierId .'"';

        return          self::_getStore()->fetchAll($sql);
    }
}
