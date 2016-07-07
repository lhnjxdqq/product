<?php
/**
 * 模型 生产订单产品关系
 */
class   Produce_Order_Product_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_product_info';

    /**
     * 字段
     */
    const   FIELDS      = 'produce_order_id,product_id,quantity,remark,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
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
            'filter'    => '',
        );
        $condition  = "";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return  self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
