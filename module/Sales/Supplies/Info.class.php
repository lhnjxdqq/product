<?php
/**
 * 模型 出货单
 */
class   Sales_Supplies_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_supplies_info';

    /**
     * 字段
     */
    const   FIELDS      = 'supplies_id,supplies_quantity,supplies_quantity_total,supplies_weight,supplies_au_price,supplies_status,sales_order_id,remark,supplies_way,courier_number,create_time,review_explain,total_price,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplies_id',
        );

        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );

        self::_getStore()->insert(self::_tableName(), $newData);
        
        return self::_getStore()->lastInsertId();
    }

    /**
     * 根据出货单ID获取数据
     *
     * @param  int      $suppliesId   出货单ID
     * @return array                  数据
     */
    static  public function getById($suppliesId){
        
        if(empty($suppliesId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `supplies_id`=' . addslashes($suppliesId);

        return self::_getStore()->fetchOne($sql);
    }
       
    /**
     * 根据出货单ID获取数据
     *
     * @param  int      $suppliesId   出货单ID
     * @return array                  数据
     */
    static  public function getBySalesOrderId($salesOrderId){
        
        if(empty($salesOrderId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `sales_order_id`=' . addslashes($salesOrderId);

        return self::_getStore()->fetchAll($sql);
    }
        
    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplies_id',
        );
        $data['update_time']    = date("Y-m-d H:i:s");
        $condition  = "`supplies_id` = '" . addslashes($data['supplies_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
