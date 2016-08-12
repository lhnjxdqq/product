<?php
/**
 * 模型 销售订单
 */
class   Sales_Order_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_order_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_order_id,sales_order_sn,sales_order_status,sales_quotation_id,quantity_total,count_goods,order_amount,create_user_id,salesperson_id,order_time,create_time,update_time,transaction_amount,reference_amount,prepaid_amount,order_type_id,audit_person_id,order_remark,reference_weight,actual_weight,customer_id,order_file_status';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_order_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
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
            'filter'    => 'sales_order_id',
        );
        $condition  = "`sales_order_id` = '" . addslashes($data['sales_order_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据销售订单ID 查询销售订单信息
     *
     * @param $salesOrderId 销售订单ID
     * @return array        销售订单信息
     */
    static public function getById ($salesOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 生成订单编号
     *
     * @param $categorySn   品类编号
     * @return string       SPU编号
     */
    static public function createOrderSn () {

        $sql    = 'SELECT MAX(`sales_order_id`) as `sales_order_id` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        $sales_order_id = $row['sales_order_id']+1;
        $code   = substr($sales_order_id,strlen($sales_order_id)-1);

        return  date('YmdHis',time()).$code;
    }
    /**
     * 根据条件获取数据列表
     *
     * @param   array   $condition  条件
     * @param   array   $order      排序依据
     * @param   int     $offset     位置
     * @param   int     $limit      数量
     * @return  array               列表
     */
    static  public  function listByCondition (array $condition, array $order, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据总数
     *
     * @param   array   $condition  条件
     * @return  int                 总数
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionKeywords($condition);
        $sql[]      = self::_conditionCustomerId($condition);
        $sql[]      = self::_conditionOrderTypeId($condition);  //订单类型
        $sql[]      = self::_conditionSalespersonId($condition);
        $sql[]      = self::_conditionOrderStatusId($condition);
        $sql[]      = self::_conditionOrderFileStatus($condition);
        $sql[]      = self::_conditionrange(
            array(
                'fieldCondition'    => 'create_time',
                'paramA'            => 'date_start',
                'paramB'            => 'date_end',
                'condition'         => $condition,
            )
        );
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
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

    static  private function _conditionOrderTypeId (array $condition) {

        if (empty($condition['order_type_id'])) {

            return  '';
        }

        return  "`order_type_id` = " . (int) $condition['order_type_id'];
    }
    
    static  private function _conditionOrderFileStatus (array $condition) {

        if (empty($condition['order_file_status'])) {

            return  '';
        }

        return  "`order_file_status` = " . (int) $condition['order_file_status'];
    }
    static  private function _conditionOrderStatusId (array $condition) {

        if (empty($condition['sales_order_status'])) {

            return  '';
        }

        return  "`sales_order_status` = " . (int) $condition['sales_order_status'];
    }
    
    static  private function _conditionSalespersonId (array $condition) {

        if (empty($condition['salesperson_id'])) {

            return  '';
        }

        return  "`salesperson_id` = " . (int) $condition['salesperson_id'];
    }
    
    static  private function _conditionCustomerId (array $condition) {

        if (empty($condition['customer_id'])) {

            return  '';
        }

        return  "`customer_id` = " . (int) $condition['customer_id'];
    }
    /**
     * 按关键词检索
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */
    static  private function _conditionKeywords (array $condition) {

        if (empty($condition['keyword'])) {

            return  '';
        }

        $keyword   = preg_replace('~[%_]~', "/$0", $condition['keyword']);

        return  "`sales_order_sn` LIKE '%" . addslashes($keyword) . "%' ESCAPE '/'";
    }

    /**
     * 获取排序子句
     *
     * @param   array   $order  排序依据
     * @return  string          SQL排序子句
     */
    static  private function _order (array $order) {

        $sql    = array();

        foreach ($order as $fieldName => $sequence) {

            $fieldName  = str_replace('`', '', $fieldName);
            $sql[]      = '`' . addslashes($fieldName) . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 获取排序方向
     *
     * @param   string  $sequence   排序方向
     * @return  string              排序方向
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }
    
    /**
     * 根据销售订单ID删除订单 
     *
     * @param   string $salesOrderId  报价单ID
     */
    static public function delete($salesOrderId) {
    
        Validate::testNull($salesOrderId,"销售订单ID不能为空");
        
        $condition = " `sales_order_id` = " . $salesOrderId;
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
    
    /**
     * 根据一组销售订单ID 查询销售订单信息
     *
     * @param array $multiSalesOrderId  一组销售订单ID
     * @return array                    销售订单信息
     */
    static public function getByMultiId (array $multiSalesOrderId) {

        $multiSalesOrderId  = array_map('intval', $multiSalesOrderId);

        $sql                = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` IN ("' . implode('","', $multiSalesOrderId) . '")';

        return              self::_getStore()->fetchAll($sql);
    }
}
