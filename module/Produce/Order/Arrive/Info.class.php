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
    const   FIELDS      = 'produce_order_arrive_id,produce_order_id,count_product,weight_total,quantity_total,storage_quantity_total,storage_weight,transaction_amount,file_path,is_storage,arrive_time,au_price,storage_time,storage_user_id';
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
     * 根据生产订单编号获取数据
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
}
