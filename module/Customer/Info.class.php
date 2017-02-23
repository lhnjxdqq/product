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
    const   FIELDS      = 'customer_id,customer_name,customer_code,province_id,city_id,district_id,address,contact,telephone,create_time,delete_status,plus_price,trading_area,service_number,qr_code_image_key,salesperson_id,commodity_consultant_id';
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
        $data['create_time']    = date('Y-m-d H:i:s');
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
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
            'filter'    => 'customer_id',
        );
        $condition  = "`customer_id` = '" . addslashes($data['customer_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据条件获取数量
     *
     * @param   array   $condition  条件数据
     * @return  int                 数量
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 获取列表
     *
     * @param   array   $condition  条件数据
     * @param   array   $order      排序
     * @param   int     $offset     位置
     * @param   int     $limit      返回数量
     */
    static  public  function listByCondition (array $condition, array $order, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 条件子句整理
     *
     * @param   array   $condition  条件数据
     * @return  string              条件子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByDeleteStatus($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 根据激活状态拼接WHERE子句
     *
     * @param  array  $condition  条件
     * @return string             WHERE子句
     */
    static private function _conditionByDeleteStatus (array $condition) {

        if (!isset($condition['delete_status'])) {

            return '';
        }
        return  '`delete_status` = \'' . (int) $condition['delete_status'] . '\'';
    }

    /**
     * 排序子句整理
     *
     * @param   array   $order  排序参数
     */
    static  private function _order (array $order) {

        $sql        = array();
        $options    = array(
            'fields'    => self::FIELDS,
        );
        $orderData  = Model::create($options, $order)->getData();

        foreach ($orderData as $fieldName => $sequence) {

            $sql[]  = '`' . $fieldName . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 顺序
     *
     * @param   string  $sequence   顺序
     * @return  string              顺序子句
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
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
    /**
     * 根据客户名称获取客户信息
     *
     * @param $customerName   客户名称
     * @return array
     */
    static public function getByName ($customerName) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `customer_name` = "' . addslashes($customerName) . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
