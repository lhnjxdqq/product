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
    const   FIELDS      = 'sales_order_id,sales_order_sn,sales_order_status,sales_quotation_id,numbers,models_numbers,order_amount,create_user_id,sale_id,order_time,create_time,update_time,transaction_amount,reference_amount,prepaid_amount,order_type_id,audit_person_id,order_remark,reference_weight,actual_weight,customer_id';
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
            'filter'    => 'sales_order_id',
        );
        $condition  = "`sales_order_id` = '" . addslashes($data['sales_order_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
