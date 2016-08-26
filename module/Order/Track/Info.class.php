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

    /**
     * 获取全部用户
     *
     * @return  array   用户列表
     */
    static  public  function listCustomerName () {

        $sql    = 'SELECT `customer_name` FROM `' . self::_tableName() . '` GROUP BY `customer_name`';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取全部销售员
     *
     * @return  array   销售员列表
     */
    static  public  function listSalesName () {

        $sql    = 'SELECT `sales_name` FROM `' . self::_tableName() . '` GROUP BY `sales_name`';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取全部供应商代码
     *
     * @return  array   供应商代码列表
     */
    static  public  function listSupplierCode () {

        $sql    = 'SELECT `supplier_code` FROM `' . self::_tableName() . '` GROUP BY `supplier_code`';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取订单号分组列表
     */
    static  public  function groupOrderCodeByCondition (array $condition, array $order = array(), $offset = 0, $size = NULL) {

        $sqlField       = "`order_code`";
        $sqlBase        = 'SELECT ' . $sqlField . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlGroup       = "GROUP BY `order_code`";
        $sqlOrder       = self::_order($order);
        $sqlLimit       = $size ? ' LIMIT ' . (int) $offset . ', ' . (int) $size    : '';
        $sql            = $sqlBase . $sqlCondition . $sqlGroup . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件返回列表
     */
    static  public  function listByCondition (array $condition, array $order = array(), $offset = 0, $size = NULL) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = $size ? ' LIMIT ' . (int) $offset . ', ' . (int) $size    : '';
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据总数
     *
     * @param   array   $condition  条件
     * @return  int                 总数
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 按条件汇总
     */
    static  public  function amountByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT `order_code`) AS `total_order`, COUNT(DISTINCT `batch_code_supplier`) AS `total_batch`, '
                        . 'SUM(`order_quantity`) AS `sum_order_quantity`, SUM(`arrival_quantity`) AS `sum_arrival_quantity`, '
                        . 'COUNT(DISTINCT `customer_name`) AS `total_customer` '
                        . 'FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;

        return          self::_getStore()->fetchOne($sql);
    }

    /**
     * 按条件求平均值
     */
    static  public  function averageByCondition (array $condition) {

        $sqlBase        = "SELECT max(abs(unix_timestamp(if(carry_sample_date='1970-01-01',null,carry_sample_date)) - unix_timestamp(if(order_date='1970-01-01',null,order_date)))) / 86400 as carry_sample_to_order,"
                        . "max(abs(unix_timestamp(if(order_date_supplier='1970-01-01',null,order_date_supplier)) - unix_timestamp(if(order_date='1970-01-01',null,order_date)))) / 86400 as order_to_supplier,"
                        . "max(abs(unix_timestamp(if(order_date_supplier='1970-01-01',null,order_date_supplier)) - unix_timestamp(if(confirm_date_supplier='1970-01-01',null,confirm_date_supplier)))) / 86400 as confirm_to_supplier,"
                        . "max(abs(unix_timestamp(if(delivery_date_supplier='1970-01-01',null,delivery_date_supplier)) - unix_timestamp(if(confirm_date_supplier='1970-01-01',null,confirm_date_supplier)))) / 86400 as delivery_to_supplier,"
                        . "max(abs(unix_timestamp(if(delivery_date_supplier='1970-01-01',null,delivery_date_supplier)) - unix_timestamp(if(arrival_date_supplier='1970-01-01',null,arrival_date_supplier)))) / 86400 as arrival_to_supplier,"
                        . "max(abs(unix_timestamp(if(warehousing_time='1970-01-01 08:00:00',null,warehousing_time)) - unix_timestamp(if(arrival_date_supplier='1970-01-01',null,arrival_date_supplier)))) / 86400 as arrival_to_warehousing,"
                        . "max(abs(unix_timestamp(if(warehousing_time='1970-01-01 08:00:00',null,warehousing_time)) - unix_timestamp(if(shipment_time='1970-01-01 08:00:00',null,shipment_time)))) / 86400 as warehousing_to_shipment,"
                        . "max(abs(unix_timestamp(if(return_money_time='1970-01-01 08:00:00',null,return_money_time)) - unix_timestamp(if(shipment_time='1970-01-01 08:00:00',null,shipment_time)))) / 86400 as shipment_to_return_money,"
                        . "max(abs(unix_timestamp(if(carry_sample_date='1970-01-01',null,carry_sample_date)) - unix_timestamp(if(shipment_time='1970-01-01 )8:00:00',null,shipment_time)))) / 86400 as carry_to_shipment,"
                        . 'avg(shipment_quantity / order_quantity) as progress '
                        . 'FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = 'SELECT avg(carry_sample_to_order) as carry_sample_to_order, '
                        . 'avg(order_to_supplier) as order_to_supplier, '
                        . 'avg(confirm_to_supplier) as confirm_to_supplier, '
                        . 'avg(delivery_to_supplier) as delivery_to_supplier, '
                        . 'avg(arrival_to_supplier) as arrival_to_supplier, '
                        . 'avg(arrival_to_warehousing) as arrival_to_warehousing, '
                        . 'avg(warehousing_to_shipment) as warehousing_to_shipment, '
                        . 'avg(shipment_to_return_money) as shipment_to_return_money, '
                        . 'avg(carry_to_shipment) as carry_to_shipment, '
                        . 'avg(progress) as progress '
                        . 'FROM (' . $sqlBase . $sqlCondition . ' GROUP BY `order_code`) AS `max_order_track_info`';

        return          self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionIn($condition, 'customer_name');
        $sql[]      = self::_conditionIn($condition, 'sales_name');
        $sql[]      = self::_conditionIn($condition, 'order_code');
        $sql[]      = self::_conditionIn($condition, 'supplier_code');
        $sql[]      = self::_conditionEqu($condition, 'order_code');
        $sql[]      = self::_conditionEqu($condition, 'batch_code_supplier');
        $sql[]      = self::_conditionEqu($condition, 'order_status');
        $sql[]      = self::_conditionBetween($condition, 'order_date');
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 根据相等条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @param   string  $field      字段
     * @return  string              条件SQL子句
     */
    static  private function _conditionEqu (array $condition, $field) {

        if (!isset($condition[$field]) || !is_scalar($condition[$field])) {

            return  '';
        }

        return      "`" . addslashes($field) . "` = '" . addslashes($condition[$field]) . "'";
    }

    /**
     * 根据IN条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @param   string  $field      字段
     * @return  string              条件SQL子句
     */
    static  private function _conditionIn (array $condition, $field) {

        if (!is_array($condition[$field])) {

            return  '';
        }

        $listSql    = array_map('addslashes', array_unique($condition[$field]));

        return      "`" . addslashes($field) . "` IN ('" . implode("','", $listSql) . "')";
    }

    /**
     * 根据BETWEEN条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @param   string  $field      字段
     * @return  string              条件SQL子句
     */
    static  private function _conditionBetween (array $condition, $field) {

        if (!is_array($condition[$field]) || 2 != count($condition[$field])) {

            return  '';
        }

        $listSql    = array_values(array_map('addslashes', array_unique($condition[$field])));

        return      "`" . addslashes($field) . "` BETWEEN '" . $listSql[0] . "' AND '" . $listSql[1] . "'";
    }

    /**
     * 获取排序子句
     *
     * @param   array   $order  排序依据
     * @return  string          SQL排序子句
     */
    static  private function _order (array $order) {

        $sql    = array();

        foreach ($order as $fieldName => $sequence) {

            $fieldName  = str_replace('`', '', $fieldName);
            $sql[]      = '`' . addslashes($fieldName) . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 获取排序方向
     *
     * @param   string  $sequence   排序方向
     * @return  string              排序方向
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }
}
