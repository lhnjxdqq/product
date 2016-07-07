<?php
/**
 * 模型 生产订单
 */
class   Produce_Order_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_info';

    /**
     * 字段
     */
    const   FIELDS      = 'produce_order_id,produce_order_sn,produce_order_remark,sales_order_id,supplier_id,prepaid_amount,arrival_date,order_type,create_user,verify_user,status_code,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'produce_order_id',
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
            'filter'    => 'produce_order_id',
        );
        $condition  = "`produce_order_id` = '" . addslashes($data['produce_order_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return  self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据销售订单ID 查询生产订单
     *
     * @param $salesOrderId 销售订单ID
     * @return array
     */
    static public function getBySalesOrderId ($salesOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 创建生产订单编号
     *
     * @return string
     */
    static public function createOrderSn () {

        $sql    = 'SHOW TABLE STATUS like "' . self::_tableName() . '"';
        $data   = self::_getStore()->fetchOne($sql);
        $sn     = 'P' . date('YmdHis') . $data['Auto_increment'];

        return  $sn;
    }

    /**
     * 查询SQL
     *
     * @param $sql
     * @return array
     */
    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
