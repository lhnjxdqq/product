<?php
/**
 * 模型 借版
 */
class   Borrow_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'borrow_info';

    /**
     * 字段
     */
    const   FIELDS      = 'borrow_id,salesperson_id,create_time,end_time,start_time,return_time,sample_quantity,customer_id,status_id,remark,sales_quotation_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'borrow_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );
        self::_getStore()->insert(self::_tableName(), $newData);
                        
        return      self::_getStore()->lastInsertId();
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
        $sql[]          = self::_conditionByCustomerId($condition);
        $sql[]          = self::_conditionBySalespersonId($condition);
        $sql[]          = self::_conditionByStatusId($condition);
        $sql[]      = self::_conditionrange(
            array(
                'fieldCondition'    => 'create_time',
                'paramA'            => 'create_date_start',
                'paramB'            => 'create_date_end',
                'condition'         => $condition,
            )
        );
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }
    /**
     *  根据顾客获取sql
     */
    static private function _conditionByCustomerId($condition){
        
        if(empty($condition['customer_id'])){
            
            return ;
        }
        
        return 'customer_id='.(int) $condition['customer_id'];
        
    }
    
    /**
     * 条件 抽象方法 当前实体模型 范围
     *
     * @param   array   $params 参数
     * @return  string          条件SQL子句
     */
    static  private function _conditionRange ($params) {

        extract($params);

        if (empty($condition[$paramB]) && !is_numeric($condition[$paramB])) {

            return  '';
        }

        return  "`" . $fieldCondition . "` BETWEEN '" . addslashes($condition[$paramA]) . "' AND '" . addslashes($condition[$paramB]) . "'";
    }
    
    /**
     *  根据销售员条件获取sql
     */    
    static private function _conditionBySalespersonId($condition){
        
        if(empty($condition['salesperson_id'])){
            
            return ;
        }
        
        return 'salesperson_id='.(int) $condition['salesperson_id'];
        
    }   
    
    /**
     * 根据状态获取sql
     */
    static private function _conditionByStatusId($condition){
        
        if(empty($condition['status_id'])){
            
            return ;
        }
        
        return 'status_id='.(int) $condition['status_id'];
        
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
     *  根据ID获取数据
     *
     *  @param  int $borrowId  借版ID
     *  @return array          数据         
     */
    static  public function getByBorrowId($borrowId){
        
        Validate::testNull($borrowId,'借版Id不能为空');
        
        $sql = "SELECT " . self::FIELDS . " FROM " . self::TABLE_NAME . " WHERE `borrow_id`=" . $borrowId;
        
        return self::_getStore()->fetchOne($sql);
    }

    /**
     * 删除
     *
     * @param   array   $borrowId   借版ID
     */
    static  public  function delete ($borrowId) {

        if(empty($borrowId)){
            
            return ;
        }
        $condition  = "`borrow_id` = " . $borrowId;

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
            'filter'    => 'borrow_id',
        );
        $condition  = "`borrow_id` = '" . addslashes($data['borrow_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
