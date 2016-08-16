<?php
/**
 * 模型 订单跟踪
 */
class   Order_Track_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'order_track_info';

    /**
     * 字段
     */
    const   FIELDS      = 'customer_name,order_code,source_code,order_date,category_name,spec_weight,color_name,order_quantity,order_weight,fee_production_customer,fee_production_supplier,supplier_code,order_date_supplier,confirm_date_supplier,batch_code_supplier,delivery_code_supplier,order_quantity_supplier,order_weight_supplier,delivery_date_supplier,arrival_date_supplier,arrival_quantity,arrival_weight,return_quantity,return_weight,arrival_weight_confirm,arrival_fee_production_confirm,shipment_time,shipment_gold_price,shipment_weight,count_order,remark,carry_sample_date,sales_name,supply_gold_price,return_money_time,warehousing_time,shipment_quantity,order_status';

    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
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
        );
        $condition  = "";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 清空数据
     */
    static  public  function clean () {

        $sql    = 'TRUNCATE TABLE `' . self::_tableName() . '`';
        self::_getStore()->execute($sql);
    }
}
