<?php
/**
 * 模型 供应商
 */
class   Supplier_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'supplier_info';

    /**
     * 字段
     */
    const   FIELDS      = 'supplier_id,supplier_code,supplier_type,area_id,supplier_address,supplier_sort,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplier_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);
        return      self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplier_id',
        );
        $condition  = "`supplier_id` = '" . addslashes($data['supplier_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    /**
     * 根据条件获取数据列表
     *
     * @param   array   $condition  条件
     * @param   array   $order      排序依据
     * @param   int     $offset     位置
     * @param   int     $limit      数量
     * @return  array               列表
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
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionKeywords($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    /**
     * 按关键词检索
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */
    static  private function _conditionKeywords (array $condition) {

        if (empty($condition['keyword'])) {

            return  '';
        }

        $keyword   = preg_replace('~[%_]~', "/$0", $condition['keyword']);

        return  "`supplier_code` LIKE '%" . addslashes($keyword) . "%' ESCAPE '/'";
    }
    
    /**
     * 根据一组供应商ID获取该组供应商信息
     *
     * @param array $multiId    一组供应商ID
     * @return array            该组供应商信息
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
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
    
    /**
     * 根据供应商ID 获取供应商信息
     *
     * @param $supplierId   供应商ID
     * @return array        供应商信息
     */
    static public function getById ($supplierId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` = "' . (int) $supplierId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    static public function toSort ($supplierId, $action) {

        $supplierInfo       = self::getById($supplierId);
        $operactor          = $action == 'up' ? '>' : '<';
        $sql                = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_sort` ' . $operactor . ' ' . $supplierInfo['supplier_sort'] . ' ORDER BY `supplier_sort` DESC LIMIT 1';
        $uponSupplierInfo   = self::_getStore()->fetchOne($sql);

        if (!$uponSupplierInfo) {

            return;
        }
        $updateData = array(
            array(
                'supplier_id'   => $supplierInfo['supplier_id'],
                'supplier_sort' => $uponSupplierInfo['supplier_sort'],
            ),
            array(
                'supplier_id'   => $uponSupplierInfo['supplier_id'],
                'supplier_sort' => $supplierInfo['supplier_sort'],
            ),
        );
        foreach ($updateData as $data) {

            self::update($data);
        }
        return true;
    }
}
