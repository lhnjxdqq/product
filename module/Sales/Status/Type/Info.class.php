<?php
/**
 * 模型 销售订单类型
 */
class   Sales_Status_Type_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_order_type_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_order_type_id,sales_order_type_name,is_delete,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_order_type_id',
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
            'filter'    => 'sales_order_type_id',
        );
        $condition  = "`sales_order_type_id` = '" . addslashes($data['sales_order_type_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
