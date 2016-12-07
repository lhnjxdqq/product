<?php
/**
 * 模型 到货
 */
class   Produce_Order_Arrive_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_arrive_info';

    /**
     * 字段
     */
    const   FIELDS      = 'is_supplies_operation,is_whole_supplies,produce_order_arrive_id,produce_order_id,count_product,weight_total,quantity_total,storage_quantity_total,storage_weight,transaction_amount,file_path,is_storage,arrive_time,au_price,storage_time,storage_user_id,storage_count_product,refund_file_status,refund_file_path,order_file_status,error_log';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'produce_order_arrive_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
        
        return self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'produce_order_arrive_id',
        );
        $condition  = "`produce_order_arrive_id` = '" . addslashes($data['produce_order_arrive_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据生产订单Id获取数据
     *
     * @param  int      $produceOrderId   订单编号
     * @return array                      数据
     */
    static  public function getByProduceOrderId($produceOrderId){
        
        if(empty($produceOrderId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `produce_order_id`=' . addslashes($produceOrderId);

        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据到货表文件状态获取一条数据
     *
     * @param  int      $orderFileStatus  文件状态
     * @return array                      数据
     */
    static  public function getByOrderFileStatus($orderFileStatus){
        
        if(empty($orderFileStatus)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `order_file_status`=' . addslashes($orderFileStatus) .' limit 0,1';

        return self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据一组生产订单ID获取数据
     *
     * @param  array    $listProduceOrderId     订单ID
     * @return array                            数据
     */
    static  public function getByMultiProduceOrderId($listProduceOrderId){
        
        if(empty($listProduceOrderId)){
            
            return array();
        }
        
        $mapOrderProduceId    = array_map('addslashes', array_unique(array_filter($listProduceOrderId)));
                                
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `produce_order_id` IN ("' . implode('","', $mapOrderProduceId) . '")';

        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据到货单ID获取数据
     *
     * @param  int      $produceOrderId   到货单ID
     * @return array                      数据
     */
    static  public function getById($produceOrderArriveId){
        
        if(empty($produceOrderArriveId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `produce_order_arrive_id`=' . addslashes($produceOrderArriveId);

        return self::_getStore()->fetchOne($sql);
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
    static  public  function listByCondition (array $condition, array $order, $offset = NULL, $limit = NULL) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = empty($limit) ? '' : ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
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
        $sql[]      = self::_conditionRefundFileStatus($condition);
        $sql[]      = self::_conditionIsStorage($condition);

        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    static  private function _conditionRefundFileStatus (array $condition) {

        if (empty($condition['refund_file_status'])) {

            return  '';
        }

        return  "`refund_file_status` = '" . addslashes($condition['refund_file_status']) . "'";
    }
    
    static  private function _conditionIsStorage (array $condition) {

        if (empty($condition['is_storage'])) {

            return  '';
        }

        return  "`is_storage` = '" . addslashes($condition['is_storage']) . "'";
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
     * 根据销售订单ID删除订单 
     *
     * @param   string $produceOrderArriveId  报价单ID
     */
    static public function delete($produceOrderArriveId) {
    
        Validate::testNull($produceOrderArriveId,"入库单ID不能为空");
        
        $condition = " `produce_order_arrive_id` = " . $produceOrderArriveId;
        
        self::_getStore()->delete(self::_tableName(), $condition);
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
    
}
