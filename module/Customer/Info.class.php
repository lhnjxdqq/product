<?php
/**
 * 模型 客户
 */
class   Customer_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'customer_info';

    /**
     * 字段
     */
    const   FIELDS      = 'customer_id,customer_name,customer_code,province_id,city_id,district_id,address,contact,telephone,create_time,delete_status,plus_price';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'customer_id',
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
            'filter'    => 'customer_id',
        );
        $condition  = "`customer_id` = '" . addslashes($data['customer_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据客户ID获取客户信息
     *
     * @param $customerId   客户ID
     * @return array
     */
    static public function getById ($customerId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `customer_id` = "' . (int) $customerId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
