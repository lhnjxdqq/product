<?php
/**
 * 模型 借版产品
 */
class   Borrow_Goods_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'borrow_goods_info';

    /**
     * 字段
     */
    const   FIELDS      = 'borrow_id,goods_id';
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
     * 根据借版ID删除
     *
     * @param   int   $borrowId   借版ID
     */
    static  public  function delete ($borrowId) {

        if(empty($borrowId)){
            
            return ;
        }
        $condition  = "`borrow_id` = " . $borrowId;

        self::_getStore()->delete(self::_tableName(), $condition);
    }
    
    /**
     * 根据借版ID样与板Id删除
     *
     * @param   array   $ids   借版ID与产品ID
     */
    static  public  function deleteByborrowIdAndGoodsId ($ids) {

        if(empty($ids['goods_id']) || empty($ids['borrow_id'])){
            
            throw   new ApplicationException('借版ID与产品ID不能为空');
        }
        
        $condition  = "`goods_id`=" . $ids['goods_id'] . " AND `borrow_id` = " . $ids['borrow_id'];
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'borrow_id,goods_id',
        );
        $condition  = "`borrow_id` = '" . addslashes($data['borrow_id']) . "' AND `goods_id` = '" . addslashes($data['goods_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
        /**
     * 根据借版获取数量
     *
     * @param   int $borrowId  借版id
     * @return  int            数量
     */
    static  public  function countByBorrow ($userId) {

        $sql    = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . "` WHERE `borrow_id` = '" . (int) $borrowId . "'";
        $row    = self::_getStore()->fetchOne($sql);

        return  $row['total'];
    }
        
    /**
     * 获取商品列表
     *
     * @param   int $borrowId  借版id
     * @return  array          数据
     */
     static  public function getByUserId ($userId) {

        $sql    = 'SELECT '. self::FIELDS .' FROM `' . self::_tableName() . "` WHERE `borrow_id` = '" . (int) $borrowId . "'";
        
        return self::_getStore()->fetchAll($sql);
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
     * 查询
     *
     * @param array $condition
     * @return mixed
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByBorrowId($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }
    
    static private function _conditionByBorrowId($condition){
        
        if(empty($condition['borrow_id'])){
            
            return ;
        }
        
        return 'borrow_id='.(int) $condition['borrow_id'];
        
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
    

}
