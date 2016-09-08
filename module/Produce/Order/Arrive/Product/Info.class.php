<?php
/**
 * 模型 到货详情
 */
class   Produce_Order_Arrive_Product_Info {


    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_arrive_product_info';

    /**
     * 字段
     */
    const   FIELDS      = 'product_id,produce_order_arrive_id,quantity,weight,is_isset_produce_order,storage_quantity,refunds,storage_weight';
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
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'product_id,produce_order_arrive_id',
        );
        $condition  = "`product_id` = '" . addslashes($data['product_id']) . "' AND `produce_order_arrive_id` = '" . addslashes($data['produce_order_arrive_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据到货单ID获取数据
     *
     * @param  int      $produceOrderId   到货单ID
     * @return array                      数据
     */
    static  public function getByProduceOrderArriveId($produceOrderArriveId){
        
        if(empty($produceOrderArriveId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `produce_order_arrive_id`=' . addslashes($produceOrderArriveId);

        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据到货单ID,和产品Id获取数据
     *
     * @param  int      $produceOrderId   到货单ID
     * @param  int      $productId        到货单ID
     * @return array                      数据
     */
    static  public function getByProduceOrderArriveIdAndProductId($produceOrderArriveId,$productId){
        
        if(empty($produceOrderArriveId) || empty($productId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `produce_order_arrive_id`=' . addslashes($produceOrderArriveId) . ' AND `product_id`='.addslashes($productId);

        return self::_getStore()->fetchOne($sql);
    }
}