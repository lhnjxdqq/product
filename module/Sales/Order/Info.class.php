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
    const   FIELDS      = 'sales_order_id,sales_order_sn,sales_order_status,sales_quotation_id,quantity_total,create_order_au_price,count_goods,order_amount,create_user_id,salesperson_id,order_time,create_time,update_time,transaction_amount,reference_amount,prepaid_amount,order_type_id,audit_person_id,order_remark,reference_weight,actual_weight,customer_id,order_file_status,import_error_log';
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
        if(!empty($newData['sales_order_status'])){
            
            self::_pushOrderStatus($data['sales_order_id'], $data['sales_order_status']);
        }
        
        Api_Controller_Order::getBySalesOrderInitRedis($data['sales_order_id']);
        
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
     *  推送销售订单Id和状态代码
     *  
     *  @param  int $salesOrderId   销售订单ID
     *  @param  int $orderStatus    状态代码
     */
    static private function _pushOrderStatus ($salesOrderId, $orderStatus){

        $config         = self::_getPushSpuApiConfig();
        $apiUrl         = $config['apiConfig']['sales_order_status'];
        $plApiUrl       = $config['apiConfig']['pl_sales_order_status'];

        $orderStatus    = array('salesOrderId'=>array('salesOrderId'=> $salesOrderId,'salesStatus'=> $orderStatus));

        if($plApiUrl){
        
            $res    = HttpRequest::getInstance($plApiUrl)->post($orderStatus);
        }
        
        if($apiUrl){
        
            $res    = HttpRequest::getInstance($apiUrl)->post($orderStatus);
        }
    }
    
    /**
     * 获取API配置
     *
     * @param string $appName
     * @return array
     * @throws Exception
     */
    static private function _getPushSpuApiConfig () {
        
        $appList    = Config::get('api|PHP', 'app_list');
        $apiList    = Config::get('api|PHP', 'api_list');
        return      array(
            'appConfig' => $appList['select'],
            'apiConfig' => $apiList['select'],
        );
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

		$length	= 7;
		$idLength	= strlen($sales_order_id);
		$randNumber	= $length - $idLength;
		
		return  date('Ymd',time()).$sales_order_id.rand(pow(10,$randNumber-1),pow(10,$randNumber));
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
        $sql[]      = self::_conditionLastOrderId($condition);
        $sql[]      = self::_conditionInSearchOrderId($condition);
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
        $sql[]      = self::_conditionrange(
            array(
                'fieldCondition'    => 'order_time',
                'paramA'            => 'order_date_start',
                'paramB'            => 'order_date_end',
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

    /**
     * 按照订单类型获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionOrderTypeId (array $condition) {

        if (empty($condition['order_type_id'])) {

            return  '';
        }

        return  "`order_type_id` = " . (int) $condition['order_type_id'];
    }
    
    /**
     * 按照文件状态获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionOrderFileStatus (array $condition) {

        if (empty($condition['order_file_status'])) {

            return  '';
        }

        return  "`order_file_status` = " . (int) $condition['order_file_status'];
    }
    
    /**
     * 按照订单获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionOrderStatusId (array $condition) {

        if (empty($condition['sales_order_status'])) {

            return  '';
        }

        return  "`sales_order_status` = " . (int) $condition['sales_order_status'];
    }
    
    /**
     * 按照一销售员ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionSalespersonId (array $condition) {

        if (empty($condition['salesperson_id'])) {

            return  '';
        }

        return  "`salesperson_id` = " . (int) $condition['salesperson_id'];
    }
    
    /**
     * 按照开始订单ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionLastOrderId (array $condition) {

        if (empty($condition['last_order_id'])) {

            return  '';
        }

        return  "`sales_order_id` < " . (int) $condition['last_order_id'];
    }

    /**
     * 按照客户获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */     
    static  private function _conditionCustomerId (array $condition) {

        if (empty($condition['customer_id'])) {

            return  '';
        }

        return  "`customer_id` = " . (int) $condition['customer_id'];
    }
    
    /**
     * 按照一组销售订单ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionInSearchOrderId (array $condition) {

        if (empty($condition['in_search_order_id'])) {

            return  '';
        }
        $multiSalesOrderId  = array_map('intval', $condition['in_search_order_id']);
    
        return '`sales_order_id` IN ("' . implode('","', $multiSalesOrderId) . '")';

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

    /**
     * 根据一组销售订单编号 查询销售订单数据
     *
     * @param array $multiSn
     * @return array
     */
    static public function getByMultiSn (array $multiSn) {

        $multiSn    = array_unique(array_filter(array_map('addslashes', array_map('trim', $multiSn))));
        $sql        = "SELECT " . self::FIELDS . " FROM `" . self::_tableName() . "` WHERE `sales_order_sn` IN ('" . implode("','", $multiSn) . "')";

        return      self::_getStore()->fetchAll($sql);
    }
}
