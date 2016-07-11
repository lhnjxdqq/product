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
            'filter'    => 'produce_order_id,product_id',
        );
        $condition  = "`produce_order_id` = '" . addslashes($data['produce_order_id']) . "' AND `product_id` = '" . addslashes($data['product_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return  self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据生产订单ID查询 产品列表
     *
     * @param $produceOrderId
     * @return array
     */
    static public function getByProduceOrderId ($produceOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `produce_order_id` = "' . (int) $produceOrderId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 批量删除一个生产订单中的产品
     *
     * @param $produceOrderId       生产订单ID
     * @param array $multiProductId 产品ID
     * @return int
     */
    static public function deleteByMultiProductId ($produceOrderId, array $multiProductId) {

        $multiProductId = array_map('intval', array_unique(array_filter($multiProductId)));

        $sql            = 'DELETE FROM `' . self::_tableName() . '` WHERE `produce_order_id` = "' . (int) $produceOrderId . '" AND `product_id` IN ("' . implode('","', $multiProductId) . '")';

        return  self::_getStore()->execute($sql);
    }

    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
