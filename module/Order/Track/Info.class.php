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
     * 获取订单号分组数量
     */
    static  public  function countOrderCodeByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(DISTINCT `order_code`) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
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
