<?php
/**
 * 模型 销售订单详情
 */
class   Sales_Order_Goods_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_order_goods_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_order_id,goods_id,goods_quantity,reference_weight,actual_weight,shipment,transaction_price,remark';

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
            'filter'    => 'sales_order_id,goods_id',
        );
        $condition  = "`sales_order_id` = '" . addslashes($data['sales_order_id']) . "' AND `goods_id` = '" . addslashes($data['goods_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据销售订单ID 查询销售订单所含SKU
     *
     * @param $salesOrderId 销售订单ID
     * @return array        销售订单所含SKU
     */
    static public function getBySalesOrderId ($salesOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '"';

        return  self::_getStore()->fetchAll($sql);
    }
            
    /**
     * 根据报价单ID删除报价单 
     *
     * @param   string $salesOrderId  报价单ID
     */
    static public function delete($salesOrderId) {
    
        Validate::testNull($salesOrderId,"销售订单ID不能为空");
        
        $condition = " `sales_order_id` = " . (int)$salesOrderId;
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
}
