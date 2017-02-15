<?php
/**
 * 模型 生产订单购物车
 */
class   Produce_Order_Cart {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_cart';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_order_id,supplier_id,product_id,quantity,remark,create_time,update_time';
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
        return      self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_order_id,supplier_id,product_id',
        );
        $condition  = "`sales_order_id` = '" . addslashes($data['sales_order_id']) . "' AND `supplier_id` = '" . addslashes($data['supplier_id']) . "' AND `product_id` = '" . addslashes($data['product_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param null $limit       数量
     */
    static public function listByCondition (array $condition, array $orderBy, $offset = null, $limit = null) {

        $fields         = implode(',', self::_getQueryFields());
        $sqlBase        = 'SELECT ' . $fields . ' FROM `produce_order_cart` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件统计数量
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `produce_order_cart` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          (int) $row['cnt'];
    }

    /**
     * 按条件统计买款ID
     *
     * @param array $condition
     * @return int
     */
    static public function countSourceCode (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM ( SELECT `source_info`.`source_code` FROM `produce_order_cart` LEFT JOIN ';
        $sqlJoin        = implode(' LEFT JOIN ', self::_getJoinTables());
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlJoin . $sqlCondition . ' GROUP BY `source_info`.`source_code`) AS `alias`';
        $row            = self::_getStore()->fetchOne($sql);

        return          (int) $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionBySalesOrderId($condition);
        $sql[]      = self::_conditionBySupplierId($condition);
        $sql[]      = self::_conditionBySpuDeleteStatus($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter)
                    ? ''
                    : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 按销售订单ID拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionBySalesOrderId (array $condition) {

        return  '`produce_order_cart`.`sales_order_id` = "' . (int) $condition['sales_order_id'] . '"';
    }

    /**
     * 按供应商ID拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionBySupplierId (array $condition) {

        return  '`produce_order_cart`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"';
    }

    /**
     * 按产品状态拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _conditionBySpuDeleteStatus (array $condition) {

        return  empty($condition['delete_status']) 
        ? '`product_info`.`delete_status` = '. Produce_Order_DeleteStatus::NORMAL
        : '`product_info`.`delete_status` = '.$condition['delete_status'];
    }

    /**
     * 拼接ORDER BY子句
     *
     * @param $orderBy
     * @return string
     */
    static private function _order ($orderBy) {

        return  '';
    }

    /**
     * 拼接LIMIT子句
     *
     * @param $offset   位置
     * @param $limit    数量
     * @return string
     */
    static private function _limit ($offset, $limit) {

        return  ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 查询表
     *
     * @return array
     */
    static private function _getJoinTables () {

        return  array(
            '`product_info` ON `product_info`.`product_id` = `produce_order_cart`.`product_id`',
            '`goods_info` ON `goods_info`.`goods_id` = `product_info`.`goods_id`',
            '`source_info` ON `source_info`.`source_id`  = `product_info`.`source_id`',
        );
    }

    /**
     * 查询字段
     *
     * @return array
     */
    static private function _getQueryFields () {

        return  array(
            '`produce_order_cart`.`product_id`',
            '`produce_order_cart`.`quantity`',
            '`produce_order_cart`.`remark`',
            '`product_info`.`product_sn`',
            '`product_info`.`product_cost`',
            '`goods_info`.`goods_id`',
            '`goods_info`.`goods_sn`',
            '`goods_info`.`goods_name`',
            '`goods_info`.`category_id`',
            '`goods_info`.`style_id`',
            '`source_info`.`source_code`',
        );
    }

    /**
     * 获取一个生产订单购物车中, 某个供应商生产的SKU数量
     *
     * @param $salesOrderId
     * @param $supplierId
     * @return array
     */
    static public function getSupplierGoodsDetail ($salesOrderId, $supplierId) {

        $salesOrderId   = (int) $salesOrderId;
        $supplierId     = (int) $supplierId;

        $sql            =<<<SQL
SELECT
    `produce_order_cart`.`product_id`,
    `product_info`.`goods_id`,
    `produce_order_cart`.`quantity`
FROM
    `produce_order_cart`
LEFT JOIN
    `product_info` ON `product_info`.`product_id`=`produce_order_cart`.`product_id`
WHERE
    `produce_order_cart`.`sales_order_id`="{$salesOrderId}"
AND
    `produce_order_cart`.`supplier_id`="{$supplierId}"
SQL;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据销售订单ID和供应商ID 获取生产订单购物车信息
     *
     * @param $salesOrderId 销售订单ID
     * @param $supplierId   供应商ID
     * @return array
     */
    static public function getBySalesOrderAndSupplier ($salesOrderId, $supplierId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" AND `supplier_id` = "' . (int) $supplierId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据销售订单ID和供应商ID清理数据
     *
     * @param $salesOrderId 销售订单ID
     * @param $supplierId   供应商ID
     * @return int
     */
    static public function deleteBySalesOrderAndSupplier ($salesOrderId, $supplierId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" AND `supplier_id` = "' . (int) $supplierId . '"';

        return  self::_getStore()->execute($sql);
    }

    /**
     * 删除生产订单购物车中某个供应商的多个产品
     *
     * @param $salesOrderId         销售订单ID
     * @param $supplierId           供应商ID
     * @param array $multiProductId 产品ID
     * @return int
     */
    static public function deleteByMultiProductId ($salesOrderId, $supplierId, array $multiProductId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" AND `supplier_id` = "' . (int) $supplierId . '" AND `product_id` IN ("' . implode('","', $multiProductId) . '")';

        return  self::_getStore()->execute($sql);
    }

    /**
     * 根据销售订单ID 供应商ID 产品ID 获取生产订单购物车内的几率
     *
     * @param $salesOrderId 销售订单ID
     * @param $supplierId   供应商ID
     * @param $productId    产品ID
     * @return array
     */
    static public function getByPrimaryKey ($salesOrderId, $supplierId, $productId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" AND `supplier_id` = "' . (int) $supplierId . '" AND `product_id` = "' . (int) $productId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
